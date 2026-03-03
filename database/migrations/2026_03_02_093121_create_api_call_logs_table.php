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
        Schema::create('api_call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45)->nullable()->index();          // IPv4 / IPv6
            $table->string('country', 100)->nullable();             // Resolved country name
            $table->string('country_code', 5)->nullable();          // 2-letter ISO code
            $table->text('url')->nullable();                        // Full request URL
            $table->string('api_endpoint', 100)->nullable()->index(); // Base64 slug
            $table->string('method', 10)->default('POST');          // HTTP method
            $table->unsignedSmallInteger('http_status')->nullable(); // Response status
            $table->unsignedInteger('duration_ms')->nullable();     // Round-trip ms
            $table->text('request_snippet')->nullable();            // First 500 chars of request
            $table->text('response_snippet')->nullable();           // First 500 chars of response
            $table->string('level', 20)->default('info')->index(); // info | warning | error
            $table->string('message')->nullable();                  // Human-readable summary
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_call_logs');
    }
};
