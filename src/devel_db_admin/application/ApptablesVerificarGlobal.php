<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use PDO;
use src\shared\config\ReplicaSelectPolicy;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

/**
 * Comprueba que tablas globales existen en principal y réplica tras apptables crear_global.
 */
final class ApptablesVerificarGlobal
{
    /** @var array<string, array{principal: list<string>, replica: list<string>}> */
    private const TABLAS = [
        'ubiscamas' => [
            'principal' => ['public.du_habitaciones', 'public.du_camas'],
            'replica' => ['public.du_habitaciones', 'public.du_camas'],
        ],
    ];

    /**
     * @return array<string, mixed> diagnóstico (host/dbname réplica)
     */
    public function verificar(string $nomApp): array
    {
        $expect = self::TABLAS[$nomApp] ?? null;
        if ($expect === null) {
            return [];
        }

        $importar = new ConfigDB('importar');
        $diag = ['app' => $nomApp];

        $this->assertTablas(
            $this->pdoPrincipal($importar),
            $expect['principal'],
            _('comun (principal)'),
        );
        $diag['principal'] = $expect['principal'];

        if (!ReplicaSelectPolicy::incluirSelect()) {
            return $diag;
        }

        $cfgReplica = $importar->getConexionImportarReplica('public_select');
        $diag['replica_host'] = (string) ($cfgReplica['host'] ?? '');
        $diag['replica_dbname'] = (string) ($cfgReplica['dbname'] ?? '');

        $this->assertTablas(
            $this->pdoReplica($cfgReplica),
            $expect['replica'],
            sprintf(
                _('comun_select (%s / %s)'),
                $diag['replica_host'],
                $diag['replica_dbname'],
            ),
        );
        $diag['replica'] = $expect['replica'];

        return $diag;
    }

    private function pdoPrincipal(ConfigDB $importar): PDO
    {
        $pdo = (new DBConnection($importar->getEsquema('public')))->getPDO();
        $pdo->exec('SET search_path TO public');

        return $pdo;
    }

    /**
     * @param array<string, mixed> $cfgReplica
     */
    private function pdoReplica(array $cfgReplica): PDO
    {
        $pdo = (new DBConnection($cfgReplica))->getPDO();
        $pdo->exec('SET search_path TO public');

        return $pdo;
    }

    /**
     * @param list<string> $tablas
     */
    private function assertTablas(PDO $pdo, array $tablas, string $destino): void
    {
        $faltan = [];
        foreach ($tablas as $tabla) {
            if (!$this->existeTabla($pdo, $tabla)) {
                $faltan[] = $tabla;
            }
        }

        if ($faltan !== []) {
            throw new \RuntimeException(sprintf(
                _('Tras crear_global faltan tablas en %1$s: %2$s'),
                $destino,
                implode(', ', $faltan),
            ));
        }
    }

    private function existeTabla(PDO $pdo, string $tablaCualificada): bool
    {
        $st = $pdo->query('SELECT to_regclass(' . $pdo->quote($tablaCualificada) . ')');
        if ($st === false) {
            return false;
        }

        return (string) $st->fetchColumn() === $tablaCualificada;
    }
}
