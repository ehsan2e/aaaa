<?php

use App\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientRoleId = Role::where('type', Role::ROLE_CLIENT)->first()->id;
        $supplierRole = Role::where('type', Role::ROLE_SUPPLIER)->first()->id;

        DB::transaction(function () use ($clientRoleId) {
            $testUserId = DB::table('users')->insertGetId([
                'name' => 'Test',
                'email' => 'test@example.com',
                'password' => bcrypt('123456'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('user_roles')->insert([
                'role_id' => $clientRoleId,
                'user_id' => $testUserId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('clients')->insert([
                'user_id' => $testUserId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        });

        array_map(function ($name) use ($supplierRole) {
            DB::transaction(function () use ($name, $supplierRole) {
                if ($name === 'Sitak') {
                    $supplierId = DB::table('users')->insertGetId([
                        'name' => $name,
                        'email' => \Illuminate\Support\Str::snake($name) . '@example.com',
                        'password' => bcrypt('123456'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    DB::table('user_roles')->insert([
                        'role_id' => $supplierRole,
                        'user_id' => $supplierId,
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
