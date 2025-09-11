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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->boolean('is_main')->default(false);
            $table->string('mime_type');
            $table->string('variant')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('resolution')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('mediaable_id')->nullable();
            $table->string('mediaable_type')->nullable();
            $table->timestamps();

            $table->index(['mediaable_id', 'mediaable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
