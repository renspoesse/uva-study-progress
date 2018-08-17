<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLtiTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti2_consumer', function (Blueprint $table) {

            $table->increments('consumer_pk');
            $table->string('name', 50);
            $table->string('consumer_key256', 256);
            $table->text('consumer_key')->nullable();
            $table->string('secret', 1024);
            $table->string('lti_version', 10)->nullable();
            $table->string('consumer_name', 255)->nullable();
            $table->string('consumer_version', 255)->nullable();
            $table->string('consumer_guid', 1024)->nullable();
            $table->text('profile')->nullable();
            $table->text('tool_proxy')->nullable();
            $table->text('settings')->nullable();
            $table->boolean('protected');
            $table->boolean('enabled');
            $table->dateTime('enable_from')->nullable();
            $table->dateTime('enable_until')->nullable();
            $table->date('last_access')->nullable();
            $table->dateTime('created');
            $table->dateTime('updated');

            $table->unique('consumer_key256');
        });

        Schema::create('lti2_tool_proxy', function (Blueprint $table) {

            $table->increments('tool_proxy_pk');
            $table->string('tool_proxy_id', 32);
            $table->integer('consumer_pk');
            $table->text('tool_proxy');
            $table->dateTime('created');
            $table->dateTime('updated');

            //$table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
            $table->unique('tool_proxy_id');
            $table->index('consumer_pk');
        });

        Schema::create('lti2_nonce', function (Blueprint $table) {

            $table->integer('consumer_pk');
            $table->string('value', 32);
            $table->dateTime('expires');

            $table->primary(['consumer_pk', 'value']);
            //$table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
        });

        Schema::create('lti2_context', function (Blueprint $table) {

            $table->increments('context_pk');
            $table->integer('consumer_pk');
            $table->string('lti_context_id', 255);
            $table->text('settings')->nullable();
            $table->dateTime('created');
            $table->dateTime('updated');

            //$table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
            $table->index('consumer_pk');
        });

        Schema::create('lti2_resource_link', function (Blueprint $table) {

            $table->increments('resource_link_pk');
            $table->integer('context_pk')->nullable();
            $table->integer('consumer_pk')->nullable();
            $table->string('lti_resource_link_id', 255);
            $table->text('settings')->nullable();
            $table->integer('primary_resource_link_pk')->nullable();
            $table->boolean('share_approved')->nullable();
            $table->dateTime('created');
            $table->dateTime('updated');

            //$table->foreign('context_pk')->references('context_pk')->on('lti2_context');
            //$table->foreign('primary_resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
            $table->index('consumer_pk');
            $table->index('context_pk');
        });

        Schema::create('lti2_user_result', function (Blueprint $table) {

            $table->increments('user_pk');
            $table->integer('resource_link_pk');
            $table->string('lti_user_id', 255);
            $table->string('lti_result_sourcedid', 1024);
            $table->dateTime('created');
            $table->dateTime('updated');

            //$table->foreign('resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
            $table->index('resource_link_pk');
        });

        Schema::create('lti2_share_key', function (Blueprint $table) {

            $table->string('share_key_id', 32);
            $table->integer('resource_link_pk');
            $table->boolean('auto_approve');
            $table->dateTime('expires');

            $table->primary('share_key_id');
            //$table->foreign('resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
            $table->index('resource_link_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lti2_share_key');
        Schema::dropIfExists('lti2_user_result');
        Schema::dropIfExists('lti2_resource_link');
        Schema::dropIfExists('lti2_context');
        Schema::dropIfExists('lti2_nonce');
        Schema::dropIfExists('lti2_tool_proxy');
        Schema::dropIfExists('lti2_consumer');
    }
}
