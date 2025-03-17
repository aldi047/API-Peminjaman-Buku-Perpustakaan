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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->bigIncrements('peminjaman_id');
            $table->bigInteger('peminjam_user_id');
            $table->bigInteger('petugas_user_id');
            $table->bigInteger('buku_id');
            $table->timestamp('waktu_peminjaman')->useCurrent();
            $table->smallInteger('durasi_peminjaman_in_days')->nullable();
            $table->timestamp('waktu_pengembalian')->nullable();
            $table->smallInteger('total_keterlambatan_in_days')->nullable();
            $table->float('total_denda')->nullable();
            $table->timestamps();

            $table->foreign('peminjam_user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('petugas_user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('buku_id')->references('buku_id')->on('buku')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
