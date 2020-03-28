<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MstDeliveryOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_delivery_order', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('do_seq')->unique();
            $table->date('do_date');
            $table->text('do_custid');
            $table->text('do_deskripsi');
            $table->text('is_active');
            $table->text('created_by');
            $table->dateTime('created_at')->useCurrent();
            $table->text('edited_by');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_delivery_order');
    }
}
