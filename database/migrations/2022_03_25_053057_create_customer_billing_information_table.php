<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBillingInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agency_customers',function (Blueprint $table) {
            $table->unique('user_id');
        });
        Schema::create('customer_billing_information', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_to');
            $table->string('address');
            $table->string('country');
            $table->string('city');
            $table->string('state');
            $table->char('zip_code',30);
            $table->char('tax_code',30)->nullable();
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('agency_customers','user_id')->cascadeOnDelete();
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
        Schema::table('agency_customers',function (Blueprint $table) {
            $table->dropUnique('user_id');
        });
        Schema::dropIfExists('customer_billing_information');
    }
}
