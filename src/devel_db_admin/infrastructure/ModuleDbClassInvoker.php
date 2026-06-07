<?php

declare(strict_types=1);

namespace src\devel_db_admin\infrastructure;

use ReflectionClass;

/**
 * Instancia clases legacy App/db/DB* y src/App/db/DB* via Reflection.
 */
final class ModuleDbClassInvoker
{
    /**
     * @param string $legacyClass FQCN legacy (`app\db\...`) o vacío si no aplica
     * @param string $srcClass FQCN bajo `src\`
     * @param list<mixed> $constructorArgs
     */
    public static function invokeMethod(
        string $legacyClass,
        string $srcClass,
        string $method,
        array $constructorArgs = [],
    ): bool {
        $executed = false;
        foreach ([$legacyClass, $srcClass] as $class) {
            if (!class_exists($class)) {
                continue;
            }
            $instance = (new ReflectionClass($class))->newInstanceArgs($constructorArgs);
            if (!method_exists($instance, $method)) {
                continue;
            }
            $instance->{$method}();
            $executed = true;
        }

        return $executed;
    }
}
