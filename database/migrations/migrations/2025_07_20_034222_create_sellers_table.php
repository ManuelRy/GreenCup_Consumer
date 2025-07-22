<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('email')->unique();
            $table->text('description')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('password');
            
            // Location fields (from seller_locations)
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            
            // Photo fields (from seller_photos - for primary/featured photo)
            $table->string('photo_url', 512)->nullable();
            $table->string('photo_caption')->nullable();
            
            // Additional useful fields
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('total_points')->default(0); // For ranking system
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
}