<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

use DI\ContainerBuilder;
use src\shared\config\ConfigGlobal;

/**
 * Construye y cachea el contenedor PHP-DI compartido por `global_object.inc` e `index.php`.
 */
final class DiContainerBootstrap
{
    public static function ensureBuilt(): void
    {
        if (isset($GLOBALS['container'])) {
            return;
        }

        $builder = new ContainerBuilder();

        $dependenciesFiles = glob(__DIR__ . '/../../*/config/dependencies.php');
        if (is_array($dependenciesFiles)) {
            foreach ($dependenciesFiles as $dependenciesFile) {
                $builder->addDefinitions($dependenciesFile);
            }
        }

        if (class_exists(ConfigGlobal::class) && !ConfigGlobal::is_debug_mode()) {
            $cacheDir = __DIR__ . '/../../../var/cache/php-di';
            if (!is_dir($cacheDir)) {
                if (!mkdir($cacheDir, 0775, true) && !is_dir($cacheDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $cacheDir));
                }
            }
            $builder->enableCompilation($cacheDir);
            $builder->writeProxiesToFile(true, $cacheDir . '/proxies');
        }

        $GLOBALS['container'] = $builder->build();
    }
}
