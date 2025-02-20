<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile_number');
            $table->string('email');
            $table->text('address');
            $table->string('status')->default('Pending');
            $table->string('stage_id')->default('1');
            $table->string('assigned_to')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('leads');
    }
};
