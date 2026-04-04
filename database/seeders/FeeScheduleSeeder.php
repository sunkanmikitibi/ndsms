<?php

namespace Database\Seeders;

use App\Models\FeeSchedule;
use Illuminate\Database\Seeder;

class FeeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default active fees for all services
        $fees = [
            [
                'service_type' => 'address_registration',
                'service_name' => 'Address Registration',
                'description' => 'Register a new property address in the system',
                'base_amount' => 2000.00,
                'currency' => 'NGN',
                'status' => 'active',
            ],
            [
                'service_type' => 'street_registration',
                'service_name' => 'Street Registration/Naming',
                'description' => 'Apply for street naming and registration',
                'base_amount' => 5000.00,
                'currency' => 'NGN',
                'status' => 'active',
            ],
            [
                'service_type' => 'address_indexing',
                'service_name' => 'Address Indexing (Google Maps)',
                'description' => 'Request Google Maps address indexing for your property',
                'base_amount' => 3000.00,
                'currency' => 'NGN',
                'status' => 'active',
            ],
            [
                'service_type' => 'street_revalidation',
                'service_name' => 'Street Revalidation',
                'description' => 'Request revalidation of street information',
                'base_amount' => 2500.00,
                'currency' => 'NGN',
                'status' => 'active',
            ],
            [
                'service_type' => 'qr_code_generation',
                'service_name' => 'QR Code Generation',
                'description' => 'Generate QR code plate for your address',
                'base_amount' => 1500.00,
                'currency' => 'NGN',
                'status' => 'active',
            ],
            [
                'service_type' => 'certificate_generation',
                'service_name' => 'Address Verification Certificate',
                'description' => 'Generate official address verification certificate',
                'base_amount' => 1000.00,
                'currency' => 'NGN',
                'status' => 'active',
            ],
        ];

        foreach ($fees as $fee) {
            FeeSchedule::updateOrCreate(
                ['service_type' => $fee['service_type']],
                $fee
            );
        }

        $this->command->info('✓ Fee schedules seeded successfully.');
        $this->command->info('  Services with active fees:');
        foreach (FeeSchedule::active()->get() as $fee) {
            $this->command->info("    • {$fee->service_name}: {$fee->getFormattedAmount()}");
        }
    }
}
