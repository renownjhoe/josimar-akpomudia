<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\DroneModel;
use App\Models\DroneState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DemoUsers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //This is demo data of users with customer and vendor access
        User::create([
            'first_name' => 'Customer',
            'last_name' => 'Test',
            'email' => 'customer@logistic.com',
            'email_verified_at' => NOW(),
            'phone_number' => '+23408087654321',
            'is_phone_verified' => 1,
            'account_type' => 3,
            'password' => bcrypt('testing123'),
        ]);
        User::create([
            'first_name' => 'Vendor',
            'last_name' => 'Test',
            'email' => 'vendor@logistic.com',
            'email_verified_at' => NOW(),
            'phone_number' => '+23408012345678',
            'is_phone_verified' => 1,
            'account_type' => 2,
            'password' => bcrypt('testing123'),
        ]);

        //This created the account type data
        AccountType::create([
            'account_name' => 'Admin',
            'slug' => 'admin'
        ]);
        AccountType::create([
            'account_name' => 'Vendor',
            'slug' => 'vendor'
        ]);
        AccountType::create([
            'account_name' => 'Customer',
            'slug' => 'customer'
        ]);

        //This create drone model type
        DroneModel::create([
            'name' => 'Lightweight',
            'slug' => 'light-weight'
        ]);
        DroneModel::create([
            'name' => 'Middleweight',
            'slug' => 'middle-weight'
        ]);
        DroneModel::create([
            'name' => 'Cruiserweight',
            'slug' => 'cruiser-weight'
        ]);
        DroneModel::create([
            'name' => 'Heavyweight',
            'slug' => 'heavy-weight'
        ]);

        //This create data for the drone states
        DroneState::create([
            'name' => 'Idle',
            'slug' => 'idle'
        ]);
        DroneState::create([
            'name' => 'Loading',
            'slug' => 'loading'
        ]);
        DroneState::create([
            'name' => 'Loaded',
            'slug' => 'loaded'
        ]);
        DroneState::create([
            'name' => 'Delivering',
            'slug' => 'delivering'
        ]);
        DroneState::create([
            'name' => 'Delivered',
            'slug' => 'delivered'
        ]);
        DroneState::create([
            'name' => 'Returning',
            'slug' => 'returning'
        ]);


    }
}
