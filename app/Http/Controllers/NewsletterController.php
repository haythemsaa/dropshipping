<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check if already subscribed
        $existing = NewsletterSubscription::where('email', $request->email)->first();

        if ($existing) {
            if ($existing->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous êtes déjà inscrit à notre newsletter'
                ], 400);
            } else {
                // Reactivate subscription
                $existing->resubscribe();
                return response()->json([
                    'success' => true,
                    'message' => 'Votre abonnement a été réactivé avec succès!'
                ]);
            }
        }

        // Create new subscription
        NewsletterSubscription::create([
            'email' => $request->email,
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Merci de vous être inscrit à notre newsletter!'
        ]);
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request, $token)
    {
        $subscription = NewsletterSubscription::where('unsubscribe_token', $token)->firstOrFail();

        $subscription->unsubscribe();

        return view('newsletter.unsubscribed', compact('subscription'));
    }
}
