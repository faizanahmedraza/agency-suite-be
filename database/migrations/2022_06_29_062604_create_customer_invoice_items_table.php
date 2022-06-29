<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('rate');
            $table->integer('quantity');
            $table->double('discount')->nullable();
            $table->double('gross_amount');
            $table->double('net_amount');
            $table->foreignId('agency_id')->constrained('agencies')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('agency_customers','user_id')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('customer_invoices')->cascadeOnDelete();
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
        Schema::dropIfExists('customer_invoice_items');
    }
}
