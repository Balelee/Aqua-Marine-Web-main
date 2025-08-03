<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->string('type'); // 'text', 'image', ou 'video'
            $table->string('background_color')->nullable();
            $table->string('text_color')->nullable();
            $table->text('text')->nullable(); // Texte pour les stories de type 'text'
            $table->string('userId'); // Chemin ou URL pour les images/vidéos
            $table->string('name'); // Chemin ou URL pour les miniatures
            $table->text('content')->nullable(); // Légende pour les images/vidéos
            $table->text('thumbnail')->nullable(); // Légende pour les images/vidéos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stories');
    }
}
