<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBranchGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_user', function (Blueprint $table) {
            $table->integer("branch_id")->unsigned();
            $table->integer("role_id")->unsigned();
            $table->integer("user_id")->unsigned();

            $table->foreign("branch_id")->references("id")->on("branches")->onUpdate("NO ACTION")->onDelete("CASCADE");
            $table->foreign("role_id")->references("id")->on("roles")->onUpdate("NO ACTION")->onDelete("CASCADE");
            $table->foreign("user_id")->references("id")->on("users")->onUpdate("NO ACTION")->onDelete("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("branch_user");
    }
}
