<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DetailInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtl_invoice', function (Blueprint $table) {
            $table->text('kwitansi_seq');
            $table->text('inv_seq');
            $table->integer('inv_rownum');
            $table->text('inv_itemid');
            $table->text('inv_deskripsi')->nullable();
            $table->integer('inv_qty');
            $table->integer('inv_cost');
            $table->string('inv_satuan')->default('UNIT');
            $table->integer('is_active')->default(1);
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
        Schema::dropIfExists('dtl_invoice');
    }
}
