<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdateProfilesTableV0905 extends Migration
{
    // Use a profile slug to fetch users. Avoid exposing backend username
    public function up()
    {
        Schema::table('dynamedia_posts_profiles', function (Blueprint $table) {
            $table->string('username')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_profiles', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
}
