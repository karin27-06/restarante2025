<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('idCategory');
            $table->text('details')->nullable();
            $table->unsignedBigInteger('idAlmacen');
            $table->boolean('state')->default(true);
            $table->timestamps();

            // Relaciones
            $table->foreign('idCategory')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');

            $table->foreign('idAlmacen')
                  ->references('id')
                  ->on('almacens')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

