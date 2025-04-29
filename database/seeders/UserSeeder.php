<?php

namespace Database\Seeders;

use App\Enums\OrganizationType;
use App\Enums\Origin;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '+212 600000000',
            'position' => 'Administrator',
            'status' => true,
        ]);

        $admin->assignRole(UserRole::ADMIN->value);

        // Example Issuer user
        $issuerExample = User::create([
            'name' => 'Example Issuer',
            'email' => 'issuer@example.com',
            'password' => Hash::make('password'),
            'phone' => '+212 611111111',
            'position' => 'Finance Director',
            'status' => true,
        ]);

        $issuerExample->assignRole(UserRole::ISSUER->value);

        // Example Investor user
        $investorExample = User::create([
            'name' => 'Example Investor',
            'email' => 'investor@example.com',
            'password' => Hash::make('password'),
            'phone' => '+212 622222222',
            'position' => 'Portfolio Manager',
            'status' => true,
        ]);

        $investorExample->assignRole(UserRole::INVESTOR->value);

        // Récupérer les pays par leur nom anglais
        $morocco = Country::where('name_en', 'Morocco')->first();
        $france = Country::where('name_en', 'France')->first();
        $usa = Country::where('name_en', 'United States')->first();

        // Create issuers
        $issuers = [
            [
                'user' => [
                    'name' => 'John Smith',
                    'email' => 'john@bmcecapital.com',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000001',
                    'position' => 'CFO',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'BMCE Bank',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::BANQUE->value,
                    'country_id' => $morocco->id,
                    'description' => 'Leading banking institution in Morocco',
                ]
            ],
            [
                'user' => [
                    'name' => 'Sara Alaoui',
                    'email' => 'sara@attijariwafabank.com',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000002',
                    'position' => 'Head of Investor Relations',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'Attijariwafa Bank',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::BANQUE->value,
                    'country_id' => $morocco->id,
                    'description' => 'One of the largest commercial banks in Morocco',
                ]
            ],
            [
                'user' => [
                    'name' => 'Mohammed Tazi',
                    'email' => 'mohammed@maroctelecom.ma',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000003',
                    'position' => 'Financial Director',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'Maroc Telecom',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::AUTRE->value,
                    'organization_type_other' => 'Telecom',
                    'country_id' => $morocco->id,
                    'description' => 'Telecommunications company',
                ]
            ],
        ];

        foreach ($issuers as $issuerData) {
            // Créer d'abord l'organisation
            $organization = Organization::create($issuerData['organization']);

            // Créer l'utilisateur avec une référence à l'organisation
            $user = User::create(array_merge(
                $issuerData['user'],
                ['organization_id' => $organization->id]
            ));

            $user->assignRole(UserRole::ISSUER->value);
        }

        // Create investors
        $investors = [
            [
                'user' => [
                    'name' => 'Fatima Zahra',
                    'email' => 'fatima@cimr.ma',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000004',
                    'position' => 'Investment Manager',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'CIMR',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::CAISSE_RETRAITE->value,
                    'country_id' => $morocco->id,
                    'description' => 'Pension fund',
                ]
            ],
            [
                'user' => [
                    'name' => 'Pierre Dubois',
                    'email' => 'pierre@amundi.fr',
                    'password' => Hash::make('password'),
                    'phone' => '+33 123456789',
                    'position' => 'Portfolio Manager',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'Amundi Asset Management',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::OPCVM->value,
                    'country_id' => $france->id,
                    'description' => 'European asset management company',
                ]
            ],
            [
                'user' => [
                    'name' => 'Michael Johnson',
                    'email' => 'michael@blackrock.com',
                    'password' => Hash::make('password'),
                    'phone' => '+1 2125551234',
                    'position' => 'Senior Investment Officer',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'BlackRock',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::FONDS_INVESTISSEMENT->value,
                    'country_id' => $usa->id,
                    'description' => 'Global investment management corporation',
                ]
            ],
        ];

        foreach ($investors as $investorData) {
            // Créer d'abord l'organisation
            $organization = Organization::create($investorData['organization']);

            // Créer l'utilisateur avec une référence à l'organisation
            $user = User::create(array_merge(
                $investorData['user'],
                ['organization_id' => $organization->id]
            ));

            $user->assignRole(UserRole::INVESTOR->value);
        }
    }
}
