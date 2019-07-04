<?php

use App\Ability;
use App\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $abilities = Ability::getAbilities();

        // Admin Role
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Admin',
            'type' => Role::ROLE_ADMIN,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $allAbilityId = DB::table('abilities')->insertGetId([
            'code' => 'all',
            'description' => $abilities['all'][0],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('role_abilities')->insert([
            'role_id' => $adminRoleId,
            'ability_id' => $allAbilityId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Client and Supplier roles
        DB::table('roles')->insert([
            [
                'name' => 'Client',
                'type' => Role::ROLE_CLIENT,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Supplier',
                'type' => Role::ROLE_SUPPLIER,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // Abilities
        $abilityRecords = [];
        foreach ($abilities as $code => $config) {
            if ($code === 'all') {
                continue;
            }
            $abilityRecords[] = [
                'code' => $code,
                'description' => $config[0],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('abilities')->insert($abilityRecords);

        // Admin user
        DB::transaction(function () use ($adminRoleId) {
            $adminUserId = DB::table('users')->insertGetId([
                'name' => 'Admin',
                'email' => config('nova.admin-email', 'admin@example.com'),
                'password' => bcrypt('123456'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::table('user_roles')->insert([
                'role_id' => $adminRoleId,
                'user_id' => $adminUserId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        });

        // Box and Box Service categories
        DB::table('product_categories')->insert([
            [
                'code' => config('nova.box_category_code'),
                'name' => 'Box',
                'active' => true,
                'custom_attributes' => json_encode([
                    [
                        'caption' => 'Cpu Cores',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [],
                        'name' => 'cpu_cores',
                        'required' => true,
                        'type' => 'integer',
                    ],
                    [
                        'caption' => 'Ram',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [],
                        'name' => 'ram',
                        'required' => true,
                        'type' => 'integer',
                    ],
                    [
                        'caption' => 'Ram Unit',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [
                            [
                                'caption' => 'KB',
                                'captions' => [
                                    'backend' => '',
                                    'en' => '',
                                ],
                                'value' => 'kb',
                            ],
                            [
                                'caption' => 'MB',
                                'captions' => [
                                    'backend' => '',
                                    'en' => '',
                                ],
                                'value' => 'mb',
                            ],
                            [
                                'caption' => 'GB',
                                'captions' => [
                                    'backend' => '',
                                    'en' => '',
                                ],
                                'value' => 'gb',
                            ],
                        ],
                        'name' => 'ram_unit',
                        'required' => true,
                        'type' => 'lookup',
                    ],
                    [
                        'caption' => 'Storage',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [],
                        'name' => 'storage',
                        'required' => true,
                        'type' => 'integer',
                    ],
                    [
                        'caption' => 'Storage Unit',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [
                            [
                                'caption' => 'GB',
                                'captions' => [
                                    'backend' => '',
                                    'en' => '',
                                ],
                                'value' => 'gb',
                            ],
                            [
                                'caption' => 'TB',
                                'captions' => [
                                    'backend' => '',
                                    'en' => '',
                                ],
                                'value' => 'tb',
                            ],
                        ],
                        'name' => 'storage_unit',
                        'required' => true,
                        'type' => 'lookup',
                    ],
                    [
                        'caption' => 'Bandwidth',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [],
                        'name' => 'bandwidth',
                        'required' => false,
                        'type' => 'integer',
                    ],
                    [
                        'caption' => 'Bandwidth Unit',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [
                            [
                                'caption' => 'MB',
                                'captions' => [
                                    'backend' => '',
                                    'en' => '',
                                ],
                                'value' => 'mb',
                            ],
                            [
                                'caption' => 'GB',
                                'captions' => [
                                    'backend' => '',
                                    'en' => '',
                                ],
                                'value' => 'gb',
                            ],
                            [
                                'caption' => 'TB',
                                'captions' => [
                                    'backend' => '',
                                    'en' => '',
                                ],
                                'value' => 'tb',
                            ],
                        ],
                        'name' => 'bandwidth_unit',
                        'required' => false,
                        'type' => 'lookup',
                    ],
                    [
                        'caption' => 'Unlimited Bandwidth',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [],
                        'name' => 'has_unlimited_bandwidth',
                        'required' => false,
                        'type' => 'boolean',
                    ],
                    [
                        'caption' => 'Minimum Employees',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [],
                        'name' => 'min_employee',
                        'required' => true,
                        'type' => 'integer',
                    ],
                    [
                        'caption' => 'Maximum Employees',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [],
                        'name' => 'max_employee',
                        'required' => false,
                        'type' => 'integer',
                    ],
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => config('nova.box_service_category_code'),
                'name' => 'Box Service',
                'active' => true,
                'custom_attributes' => json_encode([
                    [
                        'caption' => 'Mandatory',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [],
                        'name' => 'mandatory',
                        'required' => false,
                        'type' => 'boolean',
                    ],
                    [
                        'caption' => 'Pre Included',
                        'captions' => [
                            'backend' => '',
                            'en' => '',
                        ],
                        'lookupValues' => [],
                        'name' => 'pre_included',
                        'required' => false,
                        'type' => 'boolean',
                    ],
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        DB::table('ticket_categories')->insert([
            'title' => 'Miscellaneous',
            'title_translations' => json_encode([
                'fr' => 'Divers',
            ]),
            'type' => \App\TicketCategory::TYPE_INCOMING,
            'active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
