<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('reference')->nullable()->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('summary')->nullable();
            $table->json('data')->nullable();
            $table->string('status')->default('new')->index();
            $table->text('notes')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });

        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category')->index();
            $table->string('summary');
            $table->text('body')->nullable();
            $table->string('duration')->nullable();
            $table->string('distance')->nullable();
            $table->string('pickup')->nullable();
            $table->string('dropoff')->nullable();
            $table->unsignedSmallInteger('price_group')->nullable();
            $table->unsignedSmallInteger('price_private')->nullable();
            $table->json('includes')->nullable();
            $table->json('bring')->nullable();
            $table->json('notes')->nullable();
            $table->string('cover')->nullable();
            $table->json('gallery')->nullable();
            $table->string('booking_group_url')->nullable();
            $table->string('booking_private_url')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('booked_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('status')->default('booked');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('origin')->nullable();
            $table->string('tour_title')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('body');
            $table->string('source')->default('website');
            $table->string('source_url')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->string('link_text')->nullable();
            $table->string('link_url')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('booked_dates');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('enquiries');
    }
};
