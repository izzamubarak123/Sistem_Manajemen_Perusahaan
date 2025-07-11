<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('data_pakets', function (Blueprint $table) {
            $table->dropColumn('resi');
        });
    }

    public function down(): void
    {
        Schema::table('data_pakets', function (Blueprint $table) {
            if (!Schema::hasColumn('data_pakets', 'resi')) {
                $table->string('resi')->after('cost');
            }
        });
    }
};
