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
            $table->text("title")->fulltext();
            $table->text("description")->nullable()->fulltext();
            $table->text("image")->nullable();
            $table->text("url");
            $table->text("content")->fulltext();
            $table->string("author")->index()->nullable();
            $table->string("provider");
            $table->string("language")->nullable();
            $table->string("tags")->nullable();
            $table->string("country")->nullable();
            $table->datetime("published_at")->nullable();
            $table->string("category")->index();
            $table->string("source")->nullable();

//            $table->foreign("category")->references("name")->on("categories");
//            $table->foreign("source")->references("name")->on("sources");
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
