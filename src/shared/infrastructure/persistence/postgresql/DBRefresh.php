<?php

namespace src\shared\infrastructure\persistence\postgresql;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

class DBRefresh
{

    public function __construct()
    {
    }

    public function refreshSubscriptionModulo(string $db): ?string
    {
        $fileLog = ConfigGlobal::$directorio . '/log/db/pg_error_modulos.sql';
        //cambiar la conexión
        // OJO en "importar" no está la Base de Datos de pruebas, pero aquí sólo hace falta el host.
        $oConfigDB = new ConfigDB('importar');
        if ($db === 'comun') {
            $config = $oConfigDB->getConexionImportarReplica('public_select');
        }
        if ($db === 'sv-e') {
            $config = $oConfigDB->getConexionImportarReplica('publicv-e_select');
        }
        if (!isset($config)) {
            return null;
        }
        $host = self::stringConfigValue($config['host'] ?? null);
        $oConnection = new DBConnection($config);
        $dsn = $oConnection->getURI();
        $nombreBd = self::stringConfigValue($config['dbname'] ?? null);

        return $this->refreshSubscription($host, $db, $dsn, $fileLog, $nombreBd);
    }

    /**
     * @param $host
     * @param string $db 'comun, sv-e'
     * @param string $dsn
     * @param $fileLog
     * @return void
     */
    /**
     * Refresca la suscripción lógica en el servidor interior (réplica). No bloquea el flujo principal.
     *
     * @return string|null Aviso si falla; null si OK o no aplica
     */
    public function refreshSubscription(string $host, string $db, string $dsn, string $fileLog, string $nombreBd = ''): ?string
    {
        $subNombre = $this->nombreSuscripcion($db);
        if ($subNombre === null) {
            return null;
        }

        $psql = self::rutaPsql();
        if ($nombreBd === '') {
            $nombreBd = ConfigGlobal::esEntornoPruebas() ? 'pruebas-' . $db : $db;
        }
        $sql = 'ALTER SUBSCRIPTION ' . $subNombre . ' REFRESH PUBLICATION;';

        $command = 'LC_ALL=C PGOPTIONS=' . escapeshellarg('--client-min-messages=warning')
            . ' ' . escapeshellarg($psql)
            . ' -h ' . escapeshellarg((string) $host)
            . ' -d ' . escapeshellarg($nombreBd)
            . ' -U postgres -q -X -t --pset pager=off -c '
            . escapeshellarg($sql)
            . ' ' . escapeshellarg($dsn)
            . ' > ' . escapeshellarg((string) $fileLog) . ' 2>&1';

        passthru($command);
        $error = is_readable($fileLog) ? trim((string) file_get_contents($fileLog)) : '';
        if ($error === '' || $this->esErrorSuscripcionInexistente($error)) {
            return null;
        }

        return sprintf(
            _('Aviso: no se pudo refrescar la suscripción «%1$s» en %2$s (%3$s). La estructura del esquema puede estar creada igualmente. Detalle: %4$s'),
            $subNombre,
            $nombreBd,
            $db,
            $error,
        );
    }

    private function nombreSuscripcion(string $db): ?string
    {
        if ($db === 'comun') {
            return ConfigGlobal::esEntornoPruebas() ? 'subpruebascomun' : 'subcomun';
        }
        if ($db === 'sv-e') {
            return ConfigGlobal::esEntornoPruebas() ? 'subpruebassve' : 'subsve';
        }

        return null;
    }

    private function esErrorSuscripcionInexistente(string $error): bool
    {
        return str_contains($error, 'does not exist')
            && str_contains(strtolower($error), 'subscription');
    }

    private static function rutaPsql(): string
    {
        foreach (['/usr/lib/postgresql/15/bin/psql', '/usr/bin/psql'] as $ruta) {
            if (is_executable($ruta)) {
                return $ruta;
            }
        }

        return '/usr/bin/psql';
    }

    private static function stringConfigValue(mixed $value): string
    {
        if (is_string($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return '';
    }
}