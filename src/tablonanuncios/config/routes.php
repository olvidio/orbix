<?php
// Rutas del módulo Tablonanuncios
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/tablonanuncios/infrastructure/controllers/*.php
    // se mapea a la ruta /tablonanuncios/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/tablonanuncios/anuncio_delete', function () {
        require __DIR__ . '/../infrastructure/controllers/anuncio_delete.php';
    });
};
