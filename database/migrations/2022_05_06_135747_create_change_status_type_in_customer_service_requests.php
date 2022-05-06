<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeStatusTypeInCustomerServiceRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('customer_service_requests', 'status')) {
            Schema::table('customer_service_requests', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        Schema::table('customer_service_requests', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0);
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
            $table->dropColumn('status');
        });
    }
}
