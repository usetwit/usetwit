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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('role')->nullable();
            $table->string('email')->nullable();
            $table->string('company_number')->nullable();
            $table->string('company_ext')->nullable();
            $table->text('comments')->nullable();
            $table->string('contactable_type');
            $table->unsignedBigInteger('contactable_id');
            $table->index(['contactable_id', 'contactable_type'], 'model_has_contacts_contactable_id_contactable_type_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
