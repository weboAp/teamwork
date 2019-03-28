<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TeamworkSetupTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('teamwork.users_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('current_team_id')->nullable();
        });


        Schema::create(config('teamwork.teams_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');

            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('publisher')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('publisher')->references('id')->on('users');

        });

        Schema::create(config('teamwork.team_user_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id');
            $table->timestamps();

            $table->foreign('user_id')
                ->references(config('teamwork.user_foreign_key'))
                ->on(config('teamwork.users_table'))
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('team_id')
                ->references('id')
                ->on(config('teamwork.teams_table'))
                ->onDelete('cascade');
        });

        Schema::create(config('teamwork.team_invites_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id');
            $table->enum('type', ['invite', 'request']);
            $table->string('email');
            $table->string('accept_token');
            $table->string('deny_token');
            $table->timestamps();
            $table->foreign('team_id')
                ->references('id')
                ->on(config('teamwork.teams_table'))
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('teamwork.users_table'), function (Blueprint $table) {
            $table->dropColumn('current_team_id');
        });

        Schema::table(config('teamwork.team_user_table'), function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['team_id']);
        });

        Schema::drop(config('teamwork.team_user_table'));
        Schema::drop(config('teamwork.team_invites_table'));
        Schema::drop(config('teamwork.teams_table'));

    }
}
