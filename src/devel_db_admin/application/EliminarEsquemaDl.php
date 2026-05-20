<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBEsquemaCreate;
use src\shared\infrastructure\persistence\postgresql\DBRol;
use src\shared\infrastructure\persistence\postgresql\DBTrasvase;

/**
 * Traslado a «resto», borrado de esquemas y usuarios según flags comun/sv/sf (herramienta devel_db_admin).
 */
final class EliminarEsquemaDl
{
    /**
     * @return list<string> avisos no bloqueantes (p. ej. rol del esquema ya eliminado)
     */
    public function ejecutar(
        string $region,
        string $dl,
        int $comun,
        int $sv,
        int $sf,
    ): array {
        $avisos = [];
        $esquema = "$region-$dl";
        $esquemav = $esquema . 'v';
        $esquemaf = $esquema . 'f';

        $oConfigDB = new ConfigDB('importar');

        if ($comun !== 0) {
            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('comun');
            $oTrasvase->usarRolesDelEsquemaObjetivo();

            $oTrasvase->actividades('dl2resto');
            $oTrasvase->cdc('dl2resto');
            $avisos = array_merge($avisos, $oTrasvase->consumirAvisosConexion());

            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($oConfigDB->getEsquema('public'));
            $oDBEsquemaCreate->eliminar($esquema);
        }

        if ($sv !== 0) {
            $config = $oConfigDB->getEsquema('publicv');

            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('sv');
            $oTrasvase->usarRolesDelEsquemaObjetivo();

            $oTrasvase->ctr('dl2resto');
            $avisos = array_merge($avisos, $oTrasvase->consumirAvisosConexion());

            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($config);
            $oDBEsquemaCreate->eliminar($esquemav);

            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($oConfigDB->getEsquema('publicv-e'));
            $oDBEsquemaCreate->eliminar($esquemav);
        }

        if ($sf !== 0) {
            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($oConfigDB->getEsquema('publicf'));
            $oDBEsquemaCreate->eliminar($esquemaf);
        }

        if ($comun !== 0 || $sv !== 0 || $sf !== 0) {
            $oDBRol = new DBRol();
            if ($comun !== 0) {
                $avisos = array_merge($avisos, $this->eliminarRolSiExiste(
                    $oDBRol,
                    (new DBConnection($oConfigDB->getEsquema('public')))->getPDO(),
                    $esquema,
                ));
            }
            if ($sv !== 0) {
                $avisos = array_merge($avisos, $this->eliminarRolSiExiste(
                    $oDBRol,
                    (new DBConnection($oConfigDB->getEsquema('publicv')))->getPDO(),
                    $esquemav,
                ));
            }
            if ($sf !== 0) {
                $avisos = array_merge($avisos, $this->eliminarRolSiExiste(
                    $oDBRol,
                    (new DBConnection($oConfigDB->getEsquema('publicf')))->getPDO(),
                    $esquemaf,
                ));
            }
        }

        return $avisos;
    }

    /**
     * @return list<string>
     */
    private function eliminarRolSiExiste(DBRol $oDBRol, \PDO $pdo, string $rol): array
    {
        if (!$this->rolExiste($pdo, $rol)) {
            return [sprintf(_('El rol «%s» ya no existía; no se intentó borrarlo.'), $rol)];
        }

        $oDBRol->setDbConexion($pdo);
        $oDBRol->setUser($rol);
        $oDBRol->eliminarUsuario();

        return [];
    }

    private function rolExiste(\PDO $pdo, string $rol): bool
    {
        $st = $pdo->prepare('SELECT 1 FROM pg_roles WHERE rolname = :rol LIMIT 1');
        $st->execute(['rol' => $rol]);

        return (bool) $st->fetchColumn();
    }
}
