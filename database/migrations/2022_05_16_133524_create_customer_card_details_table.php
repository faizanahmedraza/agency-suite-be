<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCardDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_card_details', function (Blueprint $table) {
            $table->id();
            $table->string('card_id')->nullable();
            $table->integer('exp_month');
            $table->integer('exp_year');
            $table->integer('last_digits');
            $table->string('brand')->nullable();
            $table->string('holder_name');
            $table->string('address');
            $table->string('country');
            $table->string('city');
            $table->string('street')->nullable();
            $table->string('state');
            $table->char('zip_code',30);
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('agency_customers','user_id')->cascadeOnDelete();
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
        Schema::dropIfExists('customer_card_details');
    }
}
