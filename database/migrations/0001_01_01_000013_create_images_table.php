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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->unique();
            $table->string('hash', 64);
            $table->string('extension', 5);
            $table->string('mime_type', 50);
            $table->string('alt_text')->nullable();
            $table->text('comments')->nullable();
            $table->unsignedInteger('size');
            $table->unsignedSmallInteger('width');
            $table->unsignedSmallInteger('height');
            $table->boolean('default_image')->default(false);
            $table->string('imageable_type');
            $table->unsignedBigInteger('imageable_id');
            $table->index(['imageable_id', 'imageable_type'], 'model_has_images_imageable_id_imageable_type_index');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
