<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MstCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_customer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode')->unique();
            $table->string('store_name');
            $table->string('store_rgm');
            $table->text('store_address');
            $table->string('store_city');
            $table->integer('store_postal_code')->nullable();
            $table->string('store_area');
            $table->string('rgm_cug');
            $table->string('store_cug');
            $table->string('store_email');
            $table->string('business_hour')->nullable()->default('24 Hour (Mon-Sun)');
            $table->string('store_status')->nullable()->default('Open');;
            $table->string('store_category')->nullable();
            $table->integer('is_active')->default(1);
            $table->string('created_by');
            $table->dateTime('created_at')->useCurrent();
            $table->string('edited_by');
            $table->dateTime('updated_at')->useCurrent();
        });
        DB::statement('CREATE SEQUENCE customer_sequance START WITH 1 INCREMENT BY 1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_customer');
    }
}
