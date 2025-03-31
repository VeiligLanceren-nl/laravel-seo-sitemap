<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('url_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('route_name')->unique();
            $table->string('priority')->default(null)->nullable();
            $table->timestamp('lastmod')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('url_metadata');
    }
};