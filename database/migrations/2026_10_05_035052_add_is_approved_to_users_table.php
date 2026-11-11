<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom is_approved, defaultnya false (belum disetujui)
            $table->boolean('is_approved')->default(false)->after('password');
        });

        // SANGAT PENTING: Setujui semua akun yang sudah ada saat ini agar Anda tidak terkunci
        DB::table('users')->update(['is_approved' => true]);
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};