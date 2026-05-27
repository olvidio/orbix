<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\config\ReplicaSelectPolicy;
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

        $RegionNew = $region;
        $DlNew = $dl;

        $oConfigDB = new ConfigDB('importar');
        $incluirSelect = ReplicaSelectPolicy::incluirSelect();

        if ($comun !== 0) {
            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('comun');
            $oTrasvase->usarRolesDelEsquemaObjetivo();

            $oTrasvase->actividades('dl2resto');
            $oTrasvase->cdc('dl2resto');
            $avisos = array_merge($avisos, $oTrasvase->consumirAvisosConexion());

            $clavesComun = ['public'];
            if ($incluirSelect) {
                $clavesComun[] = 'public_select';
            }
            $avisos = array_merge($avisos, $this->eliminarEsquemaEnClavesImportar(
                $oConfigDB,
                $RegionNew,
                $DlNew,
                $esquema,
                $clavesComun,
            ));
        }

        if ($sv !== 0) {
            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('sv');
            $oTrasvase->usarRolesDelEsquemaObjetivo();

            $oTrasvase->ctr('dl2resto');
            $avisos = array_merge($avisos, $oTrasvase->consumirAvisosConexion());

            $clavesSv = ['publicv', 'publicv-e'];
            if ($incluirSelect) {
                $clavesSv[] = 'publicv-e_select';
            }
            $avisos = array_merge($avisos, $this->eliminarEsquemaEnClavesImportar(
                $oConfigDB,
                $RegionNew,
                $DlNew,
                $esquemav,
                $clavesSv,
            ));
        }

        if ($sf !== 0) {
            $clavesSf = ['publicf', 'publicf-e'];
            $avisos = array_merge($avisos, $this->eliminarEsquemaEnClavesImportar(
                $oConfigDB,
                $RegionNew,
                $DlNew,
                $esquemaf,
                $clavesSf,
            ));
        }

        $this->revocarPermisosRestoTrasTraslado($region, $dl, $comun, $sv);

        if ($comun !== 0 || $sv !== 0 || $sf !== 0) {
            $oDBRol = new DBRol();
            if ($comun !== 0) {
                $clavesComun = ['public'];
                if ($incluirSelect) {
                    $clavesComun[] = 'public_select';
                }
                $avisos = array_merge($avisos, $this->eliminarRolSiExiste(
                    $oDBRol,
                    $oConfigDB,
                    'public',
                    $clavesComun,
                    $esquema,
                ));
            }
            if ($sv !== 0) {
                $clavesSv = ['publicv', 'publicv-e'];
                if ($incluirSelect) {
                    $clavesSv[] = 'publicv-e_select';
                }
                $avisos = array_merge($avisos, $this->eliminarRolSiExiste(
                    $oDBRol,
                    $oConfigDB,
                    'publicv',
                    $clavesSv,
                    $esquemav,
                ));
            }
            if ($sf !== 0) {
                $avisos = array_merge($avisos, $this->eliminarRolSiExiste(
                    $oDBRol,
                    $oConfigDB,
                    'publicf',
                    ['publicf', 'publicf-e'],
                    $esquemaf,
                ));
            }
        }

        $this->quitarEntradasPasswordEnFicheros($comun, $sv, $sf, $esquema, $esquemav, $esquemaf);

        return $avisos;
    }

    private function quitarEntradasPasswordEnFicheros(
        int $comun,
        int $sv,
        int $sf,
        string $esquema,
        string $esquemav,
        string $esquemaf,
    ): void {
        if ($comun !== 0) {
            (new ConfigDB('comun'))->removeEsquemaEnFicheroPasswords('comun', $esquema);
        }
        if ($sv !== 0) {
            (new ConfigDB('sv'))->removeEsquemaEnFicheroPasswords('sv', $esquemav);
            (new ConfigDB('sv-e'))->removeEsquemaEnFicheroPasswords('sv-e', $esquemav);
        }
        if ($sf !== 0) {
            (new ConfigDB('sf'))->removeEsquemaEnFicheroPasswords('sf', $esquemaf);
            (new ConfigDB('sf-e'))->removeEsquemaEnFicheroPasswords('sf-e', $esquemaf);
        }
    }

    /**
     * @param list<string> $clavesImportar p. ej. public, public_select
     * @return list<string>
     */
    private function eliminarEsquemaEnClavesImportar(
        ConfigDB $oConfigDB,
        string $regionNew,
        string $dlNew,
        string $nombreEsquema,
        array $clavesImportar,
    ): array {
        $avisos = [];
        foreach ($clavesImportar as $clave) {
            try {
                $configMant = $oConfigDB->getConexionMantenimiento($clave);
                $configMant['schema'] = $clave;

                $oDBEsquemaCreate = new DBEsquemaCreate();
                $oDBEsquemaCreate->setConfig($configMant);
                $oDBEsquemaCreate->setRegionNew($regionNew);
                $oDBEsquemaCreate->setDlNew($dlNew);
                $oDBEsquemaCreate->eliminar($nombreEsquema);
            } catch (\Throwable $e) {
                $avisos[] = sprintf(
                    _('No se pudo eliminar el esquema «%1$s» en %2$s: %3$s'),
                    $nombreEsquema,
                    $clave,
                    $e->getMessage(),
                );
            }
        }

        return $avisos;
    }

    private function revocarPermisosRestoTrasTraslado(
        string $region,
        string $dl,
        int $comun,
        int $sv,
    ): void {
        if ($comun !== 0) {
            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('comun');
            $oTrasvase->usarRolesDelEsquemaObjetivo();
            $oTrasvase->revocarPermisosDl2resto('comun');
        }

        if ($sv !== 0) {
            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('sv');
            $oTrasvase->usarRolesDelEsquemaObjetivo();
            $oTrasvase->revocarPermisosDl2resto('sfsv');
        }
    }

    /**
     * @param list<string> $clavesDropOwned plantillas importar (public, public_select, …)
     * @return list<string>
     */
    private function eliminarRolSiExiste(
        DBRol $oDBRol,
        ConfigDB $oConfigDB,
        string $claveDropRole,
        array $clavesDropOwned,
        string $rol,
    ): array {
        if (!$this->rolExisteEnAlgunaClave($oConfigDB, $clavesDropOwned, $rol)) {
            return [sprintf(_('El rol «%s» ya no existía; no se intentó borrarlo.'), $rol)];
        }

        $pdo = (new DBConnection($oConfigDB->getConexionMantenimiento($claveDropRole)))->getPDO();
        $oDBRol->setDbConexion($pdo);
        $oDBRol->setUser($rol);
        $error = $oDBRol->intentarEliminarUsuario($clavesDropOwned);
        if ($error !== null) {
            return [sprintf(
                _('Aviso: no se pudo eliminar el rol «%1$s» (los esquemas ya se borraron): %2$s'),
                $rol,
                $error,
            )];
        }

        return [];
    }

    /**
     * @param list<string> $clavesImportar
     */
    private function rolExisteEnAlgunaClave(ConfigDB $oConfigDB, array $clavesImportar, string $rol): bool
    {
        foreach ($clavesImportar as $clave) {
            try {
                $pdo = (new DBConnection($oConfigDB->getConexionMantenimiento($clave)))->getPDO();
                if ($this->rolExiste($pdo, $rol)) {
                    return true;
                }
            } catch (\Throwable) {
                // Siguiente clave (p. ej. réplica no accesible).
            }
        }

        return false;
    }

    private function rolExiste(\PDO $pdo, string $rol): bool
    {
        $st = $pdo->prepare('SELECT 1 FROM pg_roles WHERE rolname = :rol LIMIT 1');
        $st->execute(['rol' => $rol]);

        return (bool) $st->fetchColumn();
    }
}
