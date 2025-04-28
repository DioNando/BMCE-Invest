<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
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
            'profile_completed' => true,
        ]);

        $admin->assignRole('admin');

        // Create issuers
        $issuers = [
            [
                'user' => [
                    'name' => 'John Smith',
                    'email' => 'john@bmcecapital.com',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000001',
                    'position' => 'CFO',
                ],
                'organization' => [
                    'name' => 'BMCE Bank',
                    'type' => 'issuer',
                    'country' => 'Morocco',
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
                ],
                'organization' => [
                    'name' => 'Attijariwafa Bank',
                    'type' => 'issuer',
                    'country' => 'Morocco',
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
                ],
                'organization' => [
                    'name' => 'Maroc Telecom',
                    'type' => 'issuer',
                    'country' => 'Morocco',
                    'description' => 'Telecommunications company',
                ]
            ],
        ];

        foreach ($issuers as $issuerData) {
            $user = User::create($issuerData['user']);
            $user->assignRole('issuer');

            Organization::create(array_merge(
                $issuerData['organization'],
                ['user_id' => $user->id]
            ));
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
                ],
                'organization' => [
                    'name' => 'CIMR',
                    'type' => 'investor',
                    'organization_type' => 'Caisse de retraite',
                    'country' => 'Morocco',
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
                ],
                'organization' => [
                    'name' => 'Amundi Asset Management',
                    'type' => 'investor',
                    'organization_type' => 'OPCVM',
                    'country' => 'France',
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
                ],
                'organization' => [
                    'name' => 'BlackRock',
                    'type' => 'investor',
                    'organization_type' => 'Fonds d\'investissement',
                    'country' => 'USA',
                    'description' => 'Global investment management corporation',
                ]
            ],
        ];

        foreach ($investors as $investorData) {
            $user = User::create($investorData['user']);
            $user->assignRole('investor');

            Organization::create(array_merge(
                $investorData['organization'],
                ['user_id' => $user->id]
            ));
        }
    }
}
