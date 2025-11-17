<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Notifications\ReviewApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Affiche la liste des avis à modérer
     */
    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'user', 'orderItem']);

        // Filtrer par statut
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'approved') {
                $query->approved();
            }
        } else {
            // Par défaut, afficher les avis en attente
            $query->pending();
        }

        // Filtrer par note
        if ($request->has('rating') && $request->rating !== 'all') {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        // Compter par statut
        $statusCounts = [
            'all' => ProductReview::count(),
            'pending' => ProductReview::pending()->count(),
            'approved' => ProductReview::approved()->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'statusCounts'));
    }

    /**
     * Approuve un avis
     */
    public function approve(ProductReview $review)
    {
        DB::beginTransaction();
        try {
            $review->approve();

            // Notifier l'auteur de l'avis que son avis a été approuvé
            $review->user->notify(new ReviewApproved($review));

            DB::commit();

            return back()->with('success', 'Avis approuvé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Rejette un avis
     */
    public function reject(ProductReview $review)
    {
        DB::beginTransaction();
        try {
            $review->reject();

            // TODO: Notifier l'auteur que son avis a été rejeté

            DB::commit();

            return back()->with('success', 'Avis rejeté.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Supprime un avis
     */
    public function destroy(ProductReview $review)
    {
        DB::beginTransaction();
        try {
            $review->delete();

            DB::commit();

            return back()->with('success', 'Avis supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Approuve plusieurs avis en masse
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id',
        ]);

        DB::beginTransaction();
        try {
            $reviews = ProductReview::with('user')->whereIn('id', $validated['review_ids'])->get();

            foreach ($reviews as $review) {
                $review->update([
                    'is_approved' => true,
                    'approved_at' => now(),
                ]);

                // Notifier l'auteur de chaque avis
                $review->user->notify(new ReviewApproved($review));
            }

            DB::commit();

            $count = count($validated['review_ids']);
            return back()->with('success', "{$count} avis approuvés avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Supprime plusieurs avis en masse
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id',
        ]);

        DB::beginTransaction();
        try {
            ProductReview::whereIn('id', $validated['review_ids'])->delete();

            DB::commit();

            $count = count($validated['review_ids']);
            return back()->with('success', "{$count} avis supprimés avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue.');
        }
    }
}
