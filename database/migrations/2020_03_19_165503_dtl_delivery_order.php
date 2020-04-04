<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DtlDeliveryOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtl_delivery_order', function (Blueprint $table) {
            $table->text('do_seq');
            $table->integer('do_rownum');
            $table->text('do_itemid');
            $table->text('do_deskripsi')->nullable();
            $table->integer('do_qty');
            $table->integer('do_cost');
            $table->text('do_satuan')->default('PCS');
            $table->text('is_active')->default(1);
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
        Schema::dropIfExists('dtl_delivery_order');
    }
}
