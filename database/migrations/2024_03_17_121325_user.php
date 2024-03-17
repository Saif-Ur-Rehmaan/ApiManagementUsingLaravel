<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("User_FullName");
            $table->string("User_UserName");
            $table->string("User_Email")->unique();
            $table->string("User_Phone");
            $table->string("User_DOB");
            $table->string("User_Password");
        });
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
