<?php
// Rutas del módulo Usuarios
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/usuarios/infrastructure/ui/http/controllers/*.php
    // se mapea a la ruta /usuarios/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

};