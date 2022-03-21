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
            $table->integer('price')->nullable();
            $table->integer('purchase_limit')->nullable();
            $table->integer('weekly')->nullable();
            $table->integer('monthly')->nullable();
            $table->integer('quarterly')->nullable();
            $table->integer('biannually')->nullable();
            $table->integer('annually')->nullable();
            $table->integer('max_concurrent_requests')->nullable();
            $table->integer('max_requests_per_month')->nullable();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('agency_id')->constrained('agencies')->cascadeOnDelete();
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
