<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusFieldToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('services', 'status')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedInteger('status')->default(0)->comment('[pending => 0, active => 1, blocked => 2]');
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
            $table->dropColumn('status');
        });
    }
}
