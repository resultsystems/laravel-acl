<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->integer("branch_id")
                ->unsigned();
            $table->integer("role_id")
                ->unsigned();
            $table->integer("user_id")
                ->unsigned();

            $table->foreign("branch_id")
                ->references("id")
                ->on("branches")
                ->onDelete("CASCADE");
            $table->foreign("role_id")
                ->references("id")
                ->on("roles")
                ->onDelete("CASCADE");
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onDelete("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("groups");
    }
}
