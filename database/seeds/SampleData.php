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

        list($novaId, $sitakId, $digitalOceanId) = array_map(function ($name) use ($supplierRole) {
            $id = 0;
            DB::transaction(function () use ($name, $supplierRole, &$id) {
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
                $id = DB::table('suppliers')->insertGetId([
                    'name' => $name,
                    'user_id' => $supplierId ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            });
            return $id;
        }, ['Nova', 'Sitak', 'Digital Ocean']);

        $boxCategoryId = DB::table('product_categories')->select('id')->where('code', config('nova.box_category_code'))->first()->id;
        $boxServiceCategoryId = DB::table('product_categories')->select('id')->where('code', config('nova.box_service_category_code'))->first()->id;
        $adminId = DB::table('roles')->join('user_roles', 'user_roles.role_id', '=', 'roles.id')->select('user_id')->where('type', Role::ROLE_ADMIN)->first()->user_id;


        DB::table('product_types')->insert([
            [
                'category_id' => $boxCategoryId,
                'supplier_id' => $digitalOceanId,
                'created_by' => $adminId,
                'name' => '1 Core Cpu 1 Gigabyte Ram',
                'sku' => 'box-1',
                'active' => true,
                'stock_less' => true,
                'original_price' => 10.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'ram' => '1',
                    'storage' => '30',
                    'ram_unit' => 'gb',
                    'bandwidth' => '1',
                    'cpu_cores' => '1',
                    'max_employee' => '5',
                    'min_employee' => '1',
                    'storage_unit' => 'gb',
                    'bandwidth_unit' => 'tb',
                    'has_unlimited_bandwidth' => false,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxCategoryId,
                'supplier_id' => $digitalOceanId,
                'created_by' => $adminId,
                'name' => '2 Core Cpu 2 Gigabyte Ram',
                'sku' => 'box-2',
                'active' => true,
                'stock_less' => true,
                'original_price' => 20.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'ram' => '2',
                    'storage' => '40',
                    'ram_unit' => 'gb',
                    'bandwidth' => '1',
                    'cpu_cores' => '2',
                    'max_employee' => '10',
                    'min_employee' => '5',
                    'storage_unit' => 'gb',
                    'bandwidth_unit' => 'tb',
                    'has_unlimited_bandwidth' => false,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxCategoryId,
                'supplier_id' => $digitalOceanId,
                'created_by' => $adminId,
                'name' => '4 Core Cpu 4 Gigabyte Ram',
                'sku' => 'box-4',
                'active' => true,
                'stock_less' => true,
                'original_price' => 40.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'ram' => '4',
                    'storage' => '50',
                    'ram_unit' => 'gb',
                    'bandwidth' => '1',
                    'cpu_cores' => '4',
                    'max_employee' => '20',
                    'min_employee' => '10',
                    'storage_unit' => 'gb',
                    'bandwidth_unit' => 'tb',
                    'has_unlimited_bandwidth' => false,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxCategoryId,
                'supplier_id' => $digitalOceanId,
                'created_by' => $adminId,
                'name' => '8 Core Cpu 8 Gigabyte Ram',
                'sku' => 'box-8',
                'active' => true,
                'stock_less' => true,
                'original_price' => 80.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'ram' => '8',
                    'storage' => '80',
                    'ram_unit' => 'gb',
                    'bandwidth' => '',
                    'cpu_cores' => '8',
                    'max_employee' => '40',
                    'min_employee' => '20',
                    'storage_unit' => 'gb',
                    'bandwidth_unit' => 'tb',
                    'has_unlimited_bandwidth' => true,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxCategoryId,
                'supplier_id' => $digitalOceanId,
                'created_by' => $adminId,
                'name' => '16 Core Cpu 16 Gigabyte Ram',
                'sku' => 'box-16',
                'active' => true,
                'stock_less' => true,
                'original_price' => 160.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'ram' => '16',
                    'storage' => '120',
                    'ram_unit' => 'gb',
                    'bandwidth' => '',
                    'cpu_cores' => '16',
                    'max_employee' => '',
                    'min_employee' => '40',
                    'storage_unit' => 'gb',
                    'bandwidth_unit' => 'tb',
                    'has_unlimited_bandwidth' => false,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxServiceCategoryId,
                'supplier_id' => $sitakId,
                'created_by' => $adminId,
                'name' => 'Nova telephony administrative panel',
                'sku' => 'box-srv-tp',
                'active' => true,
                'stock_less' => true,
                'original_price' => 0.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'mandatory' => true,
                    'pre_included' => true,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxServiceCategoryId,
                'supplier_id' => $sitakId,
                'created_by' => $adminId,
                'name' => 'Call Center',
                'sku' => 'box-srv-cc',
                'active' => true,
                'stock_less' => true,
                'original_price' => 40.00,
                'imposes_pre_invoice_negotiation' => true,
                'custom_attributes' => json_encode([
                    'mandatory' => false,
                    'pre_included' => false,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxServiceCategoryId,
                'supplier_id' => $sitakId,
                'created_by' => $adminId,
                'name' => 'Call Back',
                'sku' => 'box-srv-cb',
                'active' => true,
                'stock_less' => true,
                'original_price' => 15.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'mandatory' => false,
                    'pre_included' => false,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxServiceCategoryId,
                'supplier_id' => $sitakId,
                'created_by' => $adminId,
                'name' => 'Reservation',
                'sku' => 'box-srv-rsv',
                'active' => true,
                'stock_less' => true,
                'original_price' => 20.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'mandatory' => false,
                    'pre_included' => false,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxServiceCategoryId,
                'supplier_id' => $sitakId,
                'created_by' => $adminId,
                'name' => 'Survey',
                'sku' => 'box-srv-svy',
                'active' => true,
                'stock_less' => true,
                'original_price' => 20.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'mandatory' => false,
                    'pre_included' => false,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => $boxServiceCategoryId,
                'supplier_id' => $novaId,
                'created_by' => $adminId,
                'name' => 'Panel Management',
                'sku' => 'box-srv-pm',
                'active' => true,
                'stock_less' => true,
                'original_price' => 80.00,
                'imposes_pre_invoice_negotiation' => false,
                'custom_attributes' => json_encode([
                    'mandatory' => false,
                    'pre_included' => false,
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $vatId = DB::table('tax_groups')->insertGetId([
            'user_id' => $adminId,
            'name' => 'Vat',
            'amount' => 8,
            'active' => true,
            'is_percentage' => true,
            'name_translations' => json_encode([
                'fr' => 'la T.V.A.',
            ]),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $skus = ['box-1','box-2','box-4','box-8','box-16','box-srv-tp', 'Call Center', 'box-srv-cb', 'box-srv-rsv', 'box-srv-svy', 'box-srv-pm'];
        $productTypeIds = DB::table('product_types')->select('id')->whereIn('sku', $skus)->pluck('id')->toArray();
        DB::table('product_type_tax_groups')->insert(array_map(function($productTypeId) use ($vatId){
            return [
                'product_type_id' => $productTypeId,
                'tax_group_id' => $vatId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $productTypeIds));
    }
}
