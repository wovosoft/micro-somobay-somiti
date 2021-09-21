<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string("membership_no");
            $table->string("name");
            $table->string("pf_index");
            $table->string("current_workplace")->nullable();
            $table->date("bank_joining_date")->nullable();
            $table->date("membership_entry_date")->nullable();
            $table->string("home_district")->nullable();
            $table->string("nid_no")->nullable();
            $table->string("tin_no")->nullable();
            $table->string("phone")->nullable();
            $table->string("secondary_phone")->nullable();
            $table->string("email")->nullable();
            $table->string("photo")->nullable();
            $table->string("signature")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
