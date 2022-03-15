<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgencyIdToUserVerificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_verifications', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->constrained('agencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_verifications', function (Blueprint $table) {
            $table->dropColumn('agency_id');
        });
    }
}
