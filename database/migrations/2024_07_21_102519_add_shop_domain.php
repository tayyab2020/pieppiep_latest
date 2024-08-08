<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopDomain_newTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('shop_name');
            $table->string('subdomain')->unique();
            $table->text('shop_description')->nullable();
            // Add other necessary fields if needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('shop_name');
            $table->dropColumn('subdomain');
            $table->dropColumn('shop_description');
            // Drop other necessary fields if added in the up method
        });
    }
}
