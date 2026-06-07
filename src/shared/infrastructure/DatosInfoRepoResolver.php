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
    public static function resolve(string $className): DatosInfoRepo
    {
        $className = ltrim($className, '\\');
        if ($className === '' || !class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Clase Info invalida: %s', $className));
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
