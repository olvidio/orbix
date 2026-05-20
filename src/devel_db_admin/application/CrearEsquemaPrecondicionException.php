<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

/**
 * Precondición de {@see CrearEsquema} (roles, destino ocupado, referencia…): aviso bloqueante, no error SQL.
 */
final class CrearEsquemaPrecondicionException extends \RuntimeException
{
}
