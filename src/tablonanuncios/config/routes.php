<?php
// Rutas del módulo Tablonanuncios
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/tablonanuncios/infrastructure/ui/http/controllers/*.php
    // se mapea a la ruta /tablonanuncios/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/src/tablonanuncios/anuncio_delete', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/anuncio_delete.php';
    });
};
