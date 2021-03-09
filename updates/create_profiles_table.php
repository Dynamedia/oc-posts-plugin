<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_profiles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('website_url')->nullable()->default(null);
            $table->string('facebook_handle')->nullable()->default(null);
            $table->string('twitter_handle')->nullable()->default(null);
            $table->string('instagram_handle')->nullable()->default(null);
            $table->text('mini_biography')->nullable()->default(null);
            $table->text('full_biography')->nullable()->default(null);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dynamedia_posts_profiles');
    }
}
