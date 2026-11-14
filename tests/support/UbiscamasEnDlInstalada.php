<?php

declare(strict_types=1);

namespace Tests\support;

use PDO;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\infrastructure\persistence\postgresql\PgCamaDlRepository;
use src\ubiscamas\infrastructure\persistence\postgresql\PgHabitacionDlRepository;

/**
 * Los tests que llegan a las tablas `_dl` de ubiscamas (`du_habitaciones_dl`, `du_camas_dl`) se
 * ejecutan contra una dl que tiene el módulo de habitaciones instalado: por defecto `H-dlmE`
 * (variable de entorno `ORBIX_TEST_ESQUEMA_UBISCAMAS` para usar otra). La dl de la sesión de los tests
 * (`H-dlb`) no lo tiene; antes los tests creaban allí las tablas, cambiando la BD de desarrollo.
 *
 * {@see self::usarUbiscamasDeDlInstalada()} pone en el contenedor repositorios nuevos conectados a ese
 * esquema (no toca la conexión de los que ya había) y rehace los casos de uso del módulo para que los
 * reciban. Llamar a {@see self::dejarUbiscamasComoEstaba()} en `tearDown()` (o en un `finally`).
 */
trait UbiscamasEnDlInstalada
{
    /** @var array<class-string, object> */
    private array $ubiscamasOriginales = [];

    private static ?PDO $pdoUbiscamas = null;

    public static function esquemaUbiscamas(): string
    {
        $esquema = getenv('ORBIX_TEST_ESQUEMA_UBISCAMAS');

        return is_string($esquema) && $esquema !== '' ? $esquema : 'H-dlmE';
    }

    /** Salta el test si ese esquema no está configurado o no tiene las tablas del módulo. */
    private function usarUbiscamasDeDlInstalada(): void
    {
        $esquema = self::esquemaUbiscamas();
        if (self::$pdoUbiscamas === null) {
            $oConfigDB = new ConfigDB('comun');
            if (!$oConfigDB->tieneEsquema($esquema)) {
                $this->markTestSkipped("El esquema $esquema (ubiscamas) no está en la configuración de la BD comun");
            }
            self::$pdoUbiscamas = (new DBConnection($oConfigDB->getEsquema($esquema)))->getPDO();
        }
        $pdo = self::$pdoUbiscamas;
        $stmt = $pdo->query("SELECT to_regclass('du_habitaciones_dl') IS NOT NULL AND to_regclass('du_camas_dl') IS NOT NULL");
        if ($stmt === false || $stmt->fetchColumn() !== true) {
            $this->markTestSkipped("El esquema $esquema no tiene instalado el módulo de habitaciones (ubiscamas)");
        }

        $container = $GLOBALS['container'];
        foreach ([
            HabitacionDlRepositoryInterface::class => new PgHabitacionDlRepository(),
            CamaDlRepositoryInterface::class => new PgCamaDlRepository(),
        ] as $id => $repo) {
            $repo->setoDbl($pdo);
            $repo->setoDbl_Select($pdo);
            if (!isset($this->ubiscamasOriginales[$id])) {
                $this->ubiscamasOriginales[$id] = $container->get($id);
            }
            $container->set($id, $repo);
        }
        self::rehacerCasosDeUsoDeUbiscamas();
    }

    private function dejarUbiscamasComoEstaba(): void
    {
        if ($this->ubiscamasOriginales === []) {
            return;
        }
        foreach ($this->ubiscamasOriginales as $id => $original) {
            $GLOBALS['container']->set($id, $original);
        }
        $this->ubiscamasOriginales = [];
        self::rehacerCasosDeUsoDeUbiscamas();
    }

    /**
     * El contenedor guarda las instancias ya creadas: se vuelven a definir las clases de `application/`
     * del módulo para que la próxima vez se construyan con los repositorios que haya en ese momento.
     */
    private static function rehacerCasosDeUsoDeUbiscamas(): void
    {
        $definiciones = require __DIR__ . '/../../src/ubiscamas/config/dependencies.php';
        foreach (is_array($definiciones) ? $definiciones : [] as $id => $definicion) {
            if (is_string($id) && !str_ends_with($id, 'RepositoryInterface')) {
                $GLOBALS['container']->set($id, $definicion);
            }
        }
    }
}
