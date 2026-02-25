<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('base64_api_params', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endpoint_id')
                ->constrained('base64_api_endpoints')
                ->cascadeOnDelete();
            $table->enum('direction', ['request', 'response']);
            $table->string('field_key');
            $table->string('field_label');
            $table->string('field_type')->default('text');
            $table->string('placeholder')->nullable();
            $table->string('helper_text')->nullable();
            $table->boolean('is_required')->default(true);
            $table->string('default_value')->nullable();
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['endpoint_id', 'direction', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base64_api_params');
    }
};
