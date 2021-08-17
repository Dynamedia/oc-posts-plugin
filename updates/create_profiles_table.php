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
            $table->string('username')->nullable()->index();
            $table->string('website_url')->nullable()->default(null);
            $table->string('facebook_handle')->nullable()->default(null);
            $table->string('twitter_handle')->nullable()->default(null);
            $table->string('instagram_handle')->nullable()->default(null);
            $table->text('mini_biography')->nullable()->default(null);
            $table->text('full_biography')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('backend_users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_profiles', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_profiles_user_id_foreign');
        });

        Schema::dropIfExists('dynamedia_posts_profiles');
    }
}
