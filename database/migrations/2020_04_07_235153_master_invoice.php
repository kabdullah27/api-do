<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_invoice', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kwitansi_seq');
            $table->string('inv_seq')->unique();
            $table->date('inv_date')->useCurrent();
            $table->text('inv_custid');
            $table->text('inv_deskripsi')->nullable();
            $table->integer('is_active')->default(1);
            $table->text('created_by');
            $table->dateTime('created_at')->useCurrent();
            $table->text('edited_by');
            $table->dateTime('updated_at')->useCurrent();
        });

        DB::statement('CREATE SEQUENCE inv_sequance START WITH 1 INCREMENT BY 1;');
        DB::statement('CREATE SEQUENCE kwitansi_sequance START WITH 1 INCREMENT BY 1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_invoice');
    }
}
