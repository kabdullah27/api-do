<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MstItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_item', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('kode')->unique();
            $table->text('deskripsi_barang')->nullable();
            $table->text('satuan');
            $table->integer('harga');
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
        Schema::dropIfExists('mst_item');
    }
}
