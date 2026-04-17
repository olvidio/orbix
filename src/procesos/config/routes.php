<?php
// Rutas del módulo Procesos
// Convención: cada archivo en src/procesos/infrastructure/ui/http/controllers/*.php
// se mapea a la ruta /src/procesos/<nombre_archivo_sin_php>
// Permitimos GET y POST para máxima compatibilidad durante la migración.

return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_select_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_select_data.php';
    });
};
