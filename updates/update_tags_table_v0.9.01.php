<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdateTagsTableV0901 extends Migration
{
    public function up()
    {
        Schema::table('dynamedia_posts_tags', function (Blueprint $table) {
            $table->boolean('is_approved')->index()->default(false);
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_tags', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
}
