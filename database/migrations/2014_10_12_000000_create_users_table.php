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
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 150)->primary()->index();
            // $table->increments('id')->primary()->index();
            $table->string('email', 150)->unique();
            $table->string('username', 150)->unique();
            $table->string('password', 150);
            $table->integer('email_verified_at')->nullable();
            $table->rememberToken();
            $table->integer('created_at');
            $table->integer('updated_at');
            // $table->timestamp('trx_timestamp', 0)->nullable($value = true);
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
            $table->dropPrimary('id');
            $table->dropColumn('id');
        });
        Schema::dropIfExists('users');
    }
};
