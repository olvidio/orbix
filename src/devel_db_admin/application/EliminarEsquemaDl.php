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
    public function ejecutar(
        string $region,
        string $dl,
        int $comun,
        int $sv,
        int $sf,
    ): void {
        $esquema = "$region-$dl";
        $esquemav = $esquema . 'v';
        $esquemaf = $esquema . 'f';

        $RegionNew = $region;
        $DlNew = $dl;

        $oConfigDB = new ConfigDB('importar');

        if ($comun !== 0) {
            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('comun');

            $oTrasvase->actividades('dl2resto');
            $oTrasvase->cdc('dl2resto');
        }

        if ($sv !== 0) {
            $config = $oConfigDB->getEsquema('publicv');

            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('sv');

            $oTrasvase->ctr('dl2resto');

            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($config);
            $oDBEsquemaCreate->setRegionNew($RegionNew);
            $oDBEsquemaCreate->setDlNew($DlNew);
            $oDBEsquemaCreate->eliminar();

            $config = $oConfigDB->getEsquema('publicv-e');
            $oDBEsquemaCreate->setConfig($config);
            $oDBEsquemaCreate->eliminar();
        }

        if ($sf !== 0) {
            $config = $oConfigDB->getEsquema('publicf');
        }

        if ($sv !== 0 && $sf !== 0) {
            $config = $oConfigDB->getEsquema('public');
            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($config);
            $oDBEsquemaCreate->setRegionNew($RegionNew);
            $oDBEsquemaCreate->setDlNew($DlNew);
            $oDBEsquemaCreate->eliminar();

            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();

            $oDBRol = new DBRol();
            $oDBRol->setDbConexion($oDevelPC);

            $oDBRol->setUser($esquema);
            $oDBRol->eliminarUsuario();
            $oDBRol->setUser($esquemav);
            $oDBRol->eliminarUsuario();
            $oDBRol->setUser($esquemaf);
            $oDBRol->eliminarUsuario();
        }
    }
}
