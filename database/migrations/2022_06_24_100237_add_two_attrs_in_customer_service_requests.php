<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoAttrsInCustomerServiceRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_service_requests', function (Blueprint $table) {
            $table->integer('quantity')->nullable();
            $table->timestamp('next_recurring_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_service_requests', function (Blueprint $table) {
            $table->dropColumn(['quantity','next_recurring_date','expiry_date']);
        });
    }
}
