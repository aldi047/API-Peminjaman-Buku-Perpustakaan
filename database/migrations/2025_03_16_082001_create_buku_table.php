<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->bigIncrements('buku_id');
            $table->bigInteger('kategori_id');
            $table->string('nama');
            $table->string('isbn');
            $table->string('pengarang');
            $table->text('sinopsis')->default('');
            $table->smallInteger('stok')->default(0);
            $table->string('foto');
            $table->timestamps();

            $table->foreign('kategori_id')->references('kategori_id')->on('kategori')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
