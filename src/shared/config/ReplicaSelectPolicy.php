<?php

declare(strict_types=1);

namespace src\shared\config;

/**
 * Criterio unificado para operar también en bases réplica (*_select).
 *
 * Misma regla que {@see \src\devel_db_admin\application\CrearEsquema},
 * {@see \src\devel_db_admin\application\EliminarEsquemaDl} y renombrado de esquemas:
 * en entornos *.docker (monolito) solo la principal; fuera de docker, principal + réplica.
 */
final class ReplicaSelectPolicy
{
    public static function esEntornoDocker(): bool
    {
        return (bool) preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR);
    }

    /**
     * ¿Deben ejecutarse pasos en comun_select / sv-e_select además de la BD principal?
     */
    public static function incluirSelect(): bool
    {
        return !self::esEntornoDocker();
    }

    /**
     * Claves importar de réplica usadas en herramientas devel_db_admin.
     *
     * @return list<string>
     */
    public static function clavesImportarSelect(): array
    {
        return ['public_select', 'publicv-e_select'];
    }
}
