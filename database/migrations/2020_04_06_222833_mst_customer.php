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
            $table->text('kode')->unique();
            $table->text('store_name');
            $table->text('store_rgm');
            $table->text('store_address');
            $table->text('store_city');
            $table->integer('store_postal_code')->nullable();
            $table->text('store_area');
            $table->text('rgm_cug');
            $table->text('store_cug');
            $table->text('store_email');
            $table->text('business_hour')->default('24 Hour (Mon-Sun)');
            $table->text('store_status')->default('Open');;
            $table->text('store_category')->nullable();
            $table->integer('is_active')->default(1);
            $table->text('created_by');
            $table->dateTime('created_at')->useCurrent();
            $table->text('edited_by');
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
