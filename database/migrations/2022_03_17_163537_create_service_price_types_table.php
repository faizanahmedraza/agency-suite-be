<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePriceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_price_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('agency_id')->constrained('agencies');
            $table->integer('price')->default(0);
            $table->integer('purchase_limit')->default(0);
            $table->integer('weekly')->default(0);
            $table->integer('monthly')->default(0);
            $table->integer('quarterly')->default(0);
            $table->integer('biannually')->default(0);
            $table->integer('annually')->default(0);
            $table->integer('max_concurrent_requests')->default(0);
            $table->integer('max_requests_per_month')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::dropIfExists('service_price_types');
    }
}
