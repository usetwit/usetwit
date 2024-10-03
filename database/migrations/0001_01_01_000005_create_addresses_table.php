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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address_line_1', 50)->nullable();
            $table->string('address_line_2', 50)->nullable();
            $table->string('address_line_3', 50)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('country', 2)->collation('utf8mb4_bin')->nullable();
            $table->boolean('default_address')->default(false);
            $table->string('addressable_type')->collation('utf8mb4_bin');
            $table->unsignedBigInteger('addressable_id');
            $table->index(['addressable_id', 'addressable_type'], 'model_has_addresses_addressable_id_addressable_type_index');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
