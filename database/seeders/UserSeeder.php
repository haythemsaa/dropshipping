<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        \App\Models\User::create([
            'name' => 'Administrateur',
            'email' => 'admin@dropshipping.tn',
            'password' => bcrypt('password'),
            'phone' => '+216 20 123 456',
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Fournisseurs actifs
        $suppliers = [
            [
                'name' => 'Mohamed Ben Ali',
                'email' => 'supplier1@example.tn',
                'business_name' => 'TechStore Tunisia',
                'business_sector' => 'Électronique',
                'phone' => '+216 21 234 567',
                'business_phone' => '+216 71 234 567',
                'business_email' => 'contact@techstore.tn',
                'business_address' => 'Avenue Habib Bourguiba, Tunis',
                'commission_rate' => 10.00,
            ],
            [
                'name' => 'Fatma Trabelsi',
                'email' => 'supplier2@example.tn',
                'business_name' => 'Mode Chic',
                'business_sector' => 'Mode & Vêtements',
                'phone' => '+216 22 345 678',
                'business_phone' => '+216 71 345 678',
                'business_email' => 'contact@modechic.tn',
                'business_address' => 'Rue de la République, Sfax',
                'commission_rate' => 12.00,
            ],
            [
                'name' => 'Ahmed Mejri',
                'email' => 'supplier3@example.tn',
                'business_name' => 'Maison & Déco',
                'business_sector' => 'Maison & Décoration',
                'phone' => '+216 23 456 789',
                'business_phone' => '+216 73 456 789',
                'business_email' => 'contact@maisondeco.tn',
                'business_address' => 'Avenue de la Liberté, Sousse',
                'commission_rate' => 15.00,
            ],
        ];

        $admin = \App\Models\User::where('role', 'admin')->first();

        foreach ($suppliers as $supplierData) {
            \App\Models\User::create(array_merge($supplierData, [
                'password' => bcrypt('password'),
                'role' => 'supplier',
                'status' => 'active',
                'email_verified_at' => now(),
                'approved_at' => now(),
                'approved_by' => $admin->id,
                'sms_notifications' => true,
                'email_notifications' => true,
            ]));
        }

        // Un fournisseur en attente d'approbation
        \App\Models\User::create([
            'name' => 'Salah Eddine',
            'email' => 'pending@example.tn',
            'password' => bcrypt('password'),
            'phone' => '+216 24 567 890',
            'role' => 'supplier',
            'status' => 'pending',
            'business_name' => 'Beauty Products TN',
            'business_sector' => 'Beauté & Santé',
            'business_phone' => '+216 74 567 890',
            'business_email' => 'contact@beauty.tn',
            'business_address' => 'Rue Mongi Slim, Bizerte',
            'email_verified_at' => now(),
        ]);

        // Clients
        $clients = [
            [
                'name' => 'Amina Bouazizi',
                'email' => 'client1@example.tn',
                'phone' => '+216 25 678 901',
            ],
            [
                'name' => 'Karim Jebali',
                'email' => 'client2@example.tn',
                'phone' => '+216 26 789 012',
            ],
            [
                'name' => 'Leila Mansouri',
                'email' => 'client3@example.tn',
                'phone' => '+216 27 890 123',
            ],
        ];

        foreach ($clients as $clientData) {
            \App\Models\User::create(array_merge($clientData, [
                'password' => bcrypt('password'),
                'role' => 'client',
                'status' => 'active',
                'email_verified_at' => now(),
            ]));
        }
    }
}
