<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_service_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('agency_id');
            $table->integer('customer_id');
            $table->integer('service_id');
            $table->boolean('is_recurring');
            $table->string('recurring_type')->nullable();
            $table->boolean('status')->default(0);
            $table->longText('intake_form');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_service_requests');
    }
}
