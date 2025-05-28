<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_platforms', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');

            $table->string('platform_status')->default('pending'); 
            $table->text('platform_response')->nullable(); 

            $table->timestamps();

            $table->unique(['post_id', 'platform_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_platforms');
    }
};
