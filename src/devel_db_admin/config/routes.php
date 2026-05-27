<?php

declare(strict_types=1);

return static function ($r): void {
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/db_propiedades_data', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/db_propiedades_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/apptables_apps_data', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/apptables_apps_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/apptables_update', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/apptables_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/db_lugar', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/db_lugar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/absorber_esquema', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/absorber_esquema.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/copiar_esquema', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/copiar_esquema.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/crear_esquema', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/crear_esquema.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/crear_usuarios', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/crear_usuarios.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/eliminar_esquema', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/eliminar_esquema.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/mover_tabla', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/mover_tabla.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/migraciones_lista_data', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/migraciones_lista_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/migraciones_ejecutar', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/migraciones_ejecutar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/migraciones_quitar_registro', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/migraciones_quitar_registro.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/renombrar_esquema', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/renombrar_esquema.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/verificar_renombrar_esquema', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/verificar_renombrar_esquema.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/corregir_renombrar_esquema', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/corregir_renombrar_esquema.php';
    });
};
