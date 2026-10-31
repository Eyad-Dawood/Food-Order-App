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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('name');
            $table->string('slug')->unique(); // search engine friendly URL
            $table->text('description')->nullable();
            $table->string('cuisine')->nullable(); // fast food , italian , chinese , indian
            $table->string('logo_path')->nullable();
            $table->string('phone');
            
            $table->time('opening_time');
            $table->time('closing_time');
            
            $table->boolean('is_open')->default(true); // open or closed (manually set by the restaurant manager) 
            $table->boolean('is_active')->default(true); // active or inactive (set by the admin, for example if the restaurant violates the platform's policies)
            
            $table->decimal('min_order_amount', 8, 2)->default(0);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            
            // calculated fields for performance optimization, updated when a new rating is added
            $table->decimal('avg_rating', 3, 2)->default(0); 
            $table->unsignedInteger('ratings_count')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
