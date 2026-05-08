<?php

declare(strict_types=1);

return static function ($r): void {
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/db_propiedades_data', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/db_propiedades_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/devel_db_admin/apptables_apps_data', function (): void {
        require __DIR__ . '/../infrastructure/ui/http/controllers/apptables_apps_data.php';
    });
};
