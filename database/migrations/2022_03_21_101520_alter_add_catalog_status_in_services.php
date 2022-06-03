<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddCatalogStatusInServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('services', 'catalog_status')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('catalog_status');
            });
        }
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedInteger('catalog_status')->default(0)->comment('pending => 0, active => 1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('catalog_status');
        });
    }
}
