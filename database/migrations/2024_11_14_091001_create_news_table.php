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
        Schema::create('news', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->timestamps();
            $table->string("title");
            $table->string("description");
            $table->string("image");
            $table->string("url");
            $table->string("tags");
            $table->string("provider");
            $table->string("language");
            $table->string("author");
            $table->string("content");
            $table->bigInteger("category");
            $table->bigInteger("source");

            $table->foreign("category")->references("id")->on("categories");
            $table->foreign("source")->references("id")->on("sources");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
