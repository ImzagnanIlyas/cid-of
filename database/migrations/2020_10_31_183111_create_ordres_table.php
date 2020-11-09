<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordres', function (Blueprint $table) {
            $table->id();
            $table->string('client');
            $table->string('code_affaire');
            $table->text('motif')->nullable();
            $table->date('date_envoi');
            $table->date('date_accept')->nullable();
            $table->date('date_refus')->nullable();
            $table->date('date_modification')->nullable();
            $table->boolean('justification')->nullable();
            $table->double('montant');
            $table->string('montant_devise');
            $table->string('numero_of');
            $table->text('observation')->nullable();
            $table->boolean('refus')->default(0);
            $table->string('statut')->default('En cours');
            $table->string('type');
            $table->string('ville')->nullable();
            $table->unsignedBigInteger('division_id');
            $table->foreign('division_id')->references('id')->on('divisions');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('ordres');
    }
}
