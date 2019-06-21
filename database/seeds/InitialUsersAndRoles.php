<?php

use App\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialUsersAndRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Admin',
            'type' => Role::ROLE_ADMIN,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $allAbilityId = DB::table('abilities')->insertGetId([
            'code' => 'all',
            'description' => __('All'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $abilities = [
            'raw-query' => 'Run raw query in listing search boxes',
        ];
        array_walk(
            $abilities,
            function($label, $code){
            DB::table('abilities')->insertGetId([
                'code' => $code,
                'description' => $label,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        });
        DB::table('role_abilities')->insert([
            'role_id' => $adminRoleId,
            'ability_id' => $allAbilityId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $clientRoleId = DB::table('roles')->insertGetId([
            'name' => 'Client',
            'type' => Role::ROLE_CLIENT,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $supplierRole = DB::table('roles')->insertGetId([
            'name' => 'Supplier',
            'type' => Role::ROLE_SUPPLIER,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => config('nova.admin-email', 'admin@example.com'),
            'password' => bcrypt('123456'),
            'role_id' => $adminRoleId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::transaction(function() use($clientRoleId){
            $testUserId = DB::table('users')->insertGetId([
                'name' => 'Test',
                'email' => 'test@example.com',
                'password' => bcrypt('123456'),
                'role_id' => $clientRoleId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('clients')->insert([
                'user_id' => $testUserId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        });

        array_map(function($name) use ($supplierRole){
            DB::transaction(function() use($name, $supplierRole){
                if($name === 'Sitak'){
                    $supplierId = DB::table('users')->insertGetId([
                        'name' => $name,
                        'email' => \Illuminate\Support\Str::snake($name) . '@example.com',
                        'password' => bcrypt('123456'),
                        'role_id' => $supplierRole,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
                DB::table('suppliers')->insert([
                    'name' => $name,
                    'user_id' => $supplierId ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            });
        }, ['Nova', 'Sitak', 'Digital Ocean']);
    }
}
