<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'status')) {
                $table->string('status')->default('active');
            }
        });
    }

    public function down() {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

