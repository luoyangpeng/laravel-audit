<?php

/**
 * This file is part of itas/laravel-audit.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    itas<luoylangpeng@gmail.com>
 * @copyright itas<luoylangpeng@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('audit.audit_user_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger(config('audit.user_foreign_key'))->index()->comment('user_id');
            $table->unsignedInteger(config('audit.audit_table').'_id')->index()->comment('audit_id');
            $table->string('node', 20)->comment('审核节点');
            $table->unsignedInteger('sort')->comment('审核顺序');
            $table->unsignedInteger('status')->comment('审核状态 0：未审核 1：审核通过 2：审核不通过');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('audit_user');
    }
}