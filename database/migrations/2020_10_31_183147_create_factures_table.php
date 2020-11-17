<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->date('date_facturation');
            $table->date('date_reception_client')->nullable();
            $table->decimal('montant');
            $table->string('montant_devise');
            $table->string('numero_facture');
            $table->boolean('reception_client')->default(0);
            $table->unsignedBigInteger('ordre_id');
            $table->foreign('ordre_id')->references('id')->on('ordres')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('factures');
    }
}
