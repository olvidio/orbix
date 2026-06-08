<?php

declare(strict_types=1);

namespace src\shared\domain\contracts;

/**
 * Repositorio resuelto dinámicamente para relaciones en tablas/formularios Datos*.
 */
interface DatosLookupRepositoryInterface
{
  public function findById(mixed $id): ?object;
}
