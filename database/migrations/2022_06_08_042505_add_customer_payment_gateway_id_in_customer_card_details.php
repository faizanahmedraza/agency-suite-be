<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerPaymentGatewayIdInCustomerCardDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('customer_card_details', 'customer_payment_gateway_id')) {
            Schema::table('customer_card_details', function (Blueprint $table) {
                $table->dropColumn('customer_payment_gateway_id');
            });
        }
        Schema::table('customer_card_details', function (Blueprint $table) {
            $table->foreignId('customer_payment_gateway_id')->constrained('customer_payment_gateways')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_card_details', function (Blueprint $table) {
            $table->dropForeign(['customer_payment_gateway_id']);
        });
    }
}
