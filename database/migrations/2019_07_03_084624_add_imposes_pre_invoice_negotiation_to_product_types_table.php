<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImposesPreInvoiceNegotiationToProductTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_types', function (Blueprint $table) {
            $table->boolean('imposes_pre_invoice_negotiation')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_types', function (Blueprint $table) {
            $table->dropColumn(['imposes_pre_invoice_negotiation']);
        });
    }
}
