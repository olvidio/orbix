<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

/**
 * Abre la matriz de conexiones PDO según sesión (`session_auth.sfsv` / esquema).
 *
 * Sustituye el bloque procedural histórico de `global_object.inc`.
 */
final class ConnectionBootstrap
{
    /**
     * Contexto de esquema (sv/sf + base sin sufijo) sin abrir conexiones PDO.
     * Usado por {@see RefreshCrStgrMaterializedViews} desde `FrontBootstrap`.
     *
     * @return array{0: int|string, 1: string, 2: string, 3: string}|null
     */
    public static function schemaTupleFromSession(): ?array
    {
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        if (!is_array($sessionAuth)) {
            return null;
        }

        $userSfsv = $sessionAuth['sfsv'] ?? null;
        if (!is_int($userSfsv) && !is_string($userSfsv)) {
            return null;
        }

        $esquemaSession = $sessionAuth['esquema'] ?? '';
        if (!is_string($esquemaSession) || $esquemaSession === '') {
            return null;
        }

        if ($userSfsv == 1) {
            $esquemav = $esquemaSession;
            $esquema = substr($esquemav, 0, -1);
            $esquemaf = $esquema . 'f';
        } elseif ($userSfsv == 2) {
            $esquemaf = $esquemaSession;
            $esquema = substr($esquemaf, 0, -1);
            $esquemav = $esquema . 'v';
        } else {
            return null;
        }

        return [$userSfsv, $esquema, $esquemav, $esquemaf];
    }

    public static function buildFromSession(): ConnectionBootstrapResult
    {
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        if (!is_array($sessionAuth)) {
            throw new \RuntimeException('session_auth no disponible tras login');
        }

        $userSfsv = $sessionAuth['sfsv'] ?? null;
        if (!is_int($userSfsv) && !is_string($userSfsv)) {
            throw new \RuntimeException('session_auth.sfsv inválido');
        }

        $isDocker = (bool) preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR);

        /** @var array<string, PDO|string> $connections */
        $connections = [];

        $oConfigDB = new ConfigDB('comun');
        $connections['oDBPC'] = self::openPdo($oConfigDB, 'comun', 'public');
        $connections['oDBRC'] = self::openPdo($oConfigDB, 'comun', 'resto');

        $oConfigDBSelect = new ConfigDB('comun_select');
        $connections['oDBPC_Select'] = self::openPdo($oConfigDBSelect, 'comun_select', 'public');
        $connections['oDBRC_Select'] = self::openPdo($oConfigDBSelect, 'comun_select', 'resto');

        $esquema = null;
        $esquemav = null;
        $esquemaf = null;

        switch ($userSfsv) {
            case 1:
                $esquemaSession = $sessionAuth['esquema'] ?? '';
                if (!is_string($esquemaSession) || $esquemaSession === '') {
                    throw new \RuntimeException('session_auth.esquema inválido (sv)');
                }
                [$esquema, $esquemav, $esquemaf, $connections] = self::connectSv(
                    $oConfigDB,
                    $connections,
                    $esquemaSession,
                    $isDocker
                );
                break;
            case 2:
                $esquemaSession = $sessionAuth['esquema'] ?? '';
                if (!is_string($esquemaSession) || $esquemaSession === '') {
                    throw new \RuntimeException('session_auth.esquema inválido (sf)');
                }
                [$esquema, $esquemav, $esquemaf, $connections] = self::connectSf(
                    $oConfigDB,
                    $connections,
                    $esquemaSession
                );
                break;
            default:
                $connections['oDBC'] = $connections['oDBPC'];
                $connections['oDBC_Select'] = $connections['oDBPC_Select'];
                break;
        }

        $connections = self::appendListasConnection($connections, $isDocker);

        return new ConnectionBootstrapResult($userSfsv, $esquema, $esquemav, $esquemaf, $connections);
    }

    /**
     * @param array<string, PDO|string> $connections
     * @return array{0: string, 1: string, 2: string, 3: array<string, PDO|string>}
     */
    private static function connectSv(
        ConfigDB $oConfigDB,
        array $connections,
        string $esquemav,
        bool $isDocker
    ): array {
        $esquema = substr($esquemav, 0, -1);
        $esquemaf = $esquema . 'f';

        $connections['oDBC'] = self::openPdo($oConfigDB, 'comun', $esquema);
        $connections['oDBC_Select'] = self::openPdo($oConfigDB, 'comun_select', $esquema);

        if (!ConfigGlobal::is_dmz()) {
            $connections['oDB'] = self::openPdo($oConfigDB, 'sv', $esquemav);
            $connections['oDBP'] = self::openPdo($oConfigDB, 'sv', 'publicv');
            $connections['oDBR'] = self::openPdo($oConfigDB, 'sv', 'restov');
        }

        $connections['oDBE'] = self::openPdo($oConfigDB, 'sv-e', $esquemav);
        $connections['oDBEP'] = self::openPdo($oConfigDB, 'sv-e', 'publicv');
        $connections['oDBER'] = self::openPdo($oConfigDB, 'sv-e', 'restov');
        $connections['oDBE_Select'] = self::openPdo($oConfigDB, 'sv-e_select', $esquemav);
        $connections['oDBEP_Select'] = self::openPdo($oConfigDB, 'sv-e_select', 'publicv');
        $connections['oDBER_Select'] = self::openPdo($oConfigDB, 'sv-e_select', 'restov');

        if (!$isDocker) {
            try {
                $oConfigSf = new ConfigDB('sf');
                $configSf = $oConfigSf->getEsquema($esquemaf);
                $connections['oDBF'] = (new DBConnection($configSf))->getPDO();
            } catch (\Throwable) {
            }
        }

        return [$esquema, $esquemav, $esquemaf, $connections];
    }

    /**
     * @param array<string, PDO|string> $connections
     * @return array{0: string, 1: string, 2: string, 3: array<string, PDO|string>}
     */
    private static function connectSf(ConfigDB $oConfigDB, array $connections, string $esquemaf): array
    {
        $esquema = substr($esquemaf, 0, -1);
        $esquemav = $esquema . 'v';

        $connections['oDBC'] = self::openPdo($oConfigDB, 'comun', $esquema);
        $connections['oDBC_Select'] = self::openPdo($oConfigDB, 'comun_select', $esquema);

        $oConfigDB->setDataBase('sf');
        $oConexion = new DBConnection($oConfigDB->getEsquema($esquemaf));
        $connections['oDB'] = $oConexion->getPDO();
        $connections['oDBE'] = $oConexion->getPDO();
        $connections['oDBE_Select'] = $oConexion->getPDO();

        $oConexion = new DBConnection($oConfigDB->getEsquema('publicf'));
        $connections['oDBP'] = $oConexion->getPDO();
        $connections['oDBEP'] = $oConexion->getPDO();
        $connections['oDBEP_Select'] = $oConexion->getPDO();

        $oConexion = new DBConnection($oConfigDB->getEsquema('restof'));
        $connections['oDBR'] = $oConexion->getPDO();
        $connections['oDBER'] = $oConexion->getPDO();
        $connections['oDBER_Select'] = $oConexion->getPDO();

        $connections['oDBF'] = $connections['oDB'];

        return [$esquema, $esquemav, $esquemaf, $connections];
    }

    /**
     * @param array<string, PDO|string> $connections
     * @return array<string, PDO|string>
     */
    private static function appendListasConnection(array $connections, bool $isDocker): array
    {
        if (!ConfigGlobal::is_app_installed('dbextern') || ConfigGlobal::is_dmz() || $isDocker) {
            return $connections;
        }
        if (ConfigGlobal::mi_region() === ConfigGlobal::mi_delef()) {
            return $connections;
        }

        try {
            $oConfigDB = new ConfigDB('listas');
            $config = $oConfigDB->getEsquema('public');
            $connections['oDBListas'] = (new DBConnection($config))->getPDOListas();
        } catch (\InvalidArgumentException $e) {
            echo '/*';
            echo $e->getMessage() . '<br>';
            echo '*/';
            $connections['oDBListas'] = 'error';
        } catch (\PDOException $e) {
            echo '/*';
            echo _('No puedo conectar con la base de datos de listas') . ':<br>';
            echo $e->getMessage();
            echo '*/';
            $connections['oDBListas'] = 'error';
        }

        return $connections;
    }

    private static function openPdo(ConfigDB $oConfigDB, string $database, string $schema): PDO
    {
        $oConfigDB->setDataBase($database);
        $config = $oConfigDB->getEsquema($schema);

        return (new DBConnection($config))->getPDO();
    }
}
