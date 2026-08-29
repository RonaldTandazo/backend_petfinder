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
        Schema::create('pictures', function (Blueprint $table) {
            $table->id();
            $table->morphs('pictureable');
            // Key en el bucket definitivo (petfinder-pictures). Null hasta que
            // SyncPictureJob la copie ahí; se mantiene igual en actualizaciones
            // posteriores del binario (mismo key, contenido nuevo).
            $table->string('path')->nullable();
            // Key en el bucket temporal (petfinder-tmp) — el último upload
            // pendiente de sincronizar (inicial o de reemplazo).
            $table->string('path_temp')->nullable();
            // Solo aplica a pictures de Pet (foto principal). Nullable aquí;
            // la obligatoriedad/uso se valida en la capa de aplicación según el modelo dueño.
            $table->boolean('is_main')->nullable();
            // false = 'path_temp' todavía no se copió (o recopió) a 'path'.
            $table->boolean('synced')->default(false);
            // Id del User/Shelter autenticado que subió el archivo (sin FK: puede
            // referenciar cualquiera de las dos tablas). Solo para trazabilidad/metadata.
            $table->unsignedBigInteger('uploaded_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pictures');
    }
};
