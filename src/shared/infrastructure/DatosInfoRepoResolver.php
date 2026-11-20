<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

use InvalidArgumentException;
use src\shared\domain\DatosInfoRepo;

/**
 * Resuelve clases Info* (tablaDB / dossier) desde el nombre enviado por POST.
 * Sustituye `new $clase()` tras migración DI.
 */
final class DatosInfoRepoResolver
{
    /**
     * El nombre es el de una clase `Info*` del dominio de un módulo (`src\<modulo>\domain\Info...`).
     * Se comprueba ANTES de tocar el autoload: el nombre llega de la petición, y con cualquier otro el
     * autoload incluiría (y ejecutaría) el fichero PHP que le corresponda.
     */
    public static function esNombreAdmitido(string $className): bool
    {
        return preg_match('/^src\\\\[a-z0-9_]+\\\\domain\\\\Info[A-Za-z0-9_]*$/', ltrim($className, '\\')) === 1;
    }

    public static function resolve(string $className): DatosInfoRepo
    {
        $className = ltrim($className, '\\');
        if (!self::esNombreAdmitido($className) || !class_exists($className) || !is_subclass_of($className, DatosInfoRepo::class)) {
            throw new InvalidArgumentException('Clase Info no válida');
        }

        $instance = DependencyResolver::get($className);
        if (!$instance instanceof DatosInfoRepo) {
            throw new InvalidArgumentException(
                sprintf('%s no extiende %s', $className, DatosInfoRepo::class)
            );
        }

        return $instance;
    }
}
