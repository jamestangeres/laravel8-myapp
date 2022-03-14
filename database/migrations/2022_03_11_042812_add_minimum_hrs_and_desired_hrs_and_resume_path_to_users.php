<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimumHrsAndDesiredHrsAndResumePathToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('minimum_hrs')->nullable();
            $table->double('desired_hrs')->nullable();
            $table->string('resume_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('minimum_hrs');
            $table->dropColumn('desired_hrs');
            $table->dropColumn('resume_path');
        });
    }
}
