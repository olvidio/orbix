<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\config\ServerConf;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBEsquemaCreate;
use src\shared\infrastructure\persistence\postgresql\DBRol;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Crea esquemas BD (comun / sv / sv-e / sf) a partir de un esquema de referencia.
 */
final class CrearEsquema
{
    public function __construct(
        private readonly object $container,
    ) {
    }

    /**
     * @return list<string> avisos no bloqueantes
     */
    public function ejecutar(
        string $esquemaRef,
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

        $oDBRol = new DBRol();

        $esquemaBase = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen($esquemaRef);
        if ($esquemaBase === '' || !str_contains($esquemaBase, '-')) {
            throw new \InvalidArgumentException(_('Esquema de referencia no válido.'));
        }
        [$RegionRef, $DlRef] = explode('-', $esquemaBase, 2);

        $RegionNew = $region;
        $DlNew = $dl;

        $isDocker = (bool) preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR);

        (new ComprobarPrecondicionesCrearEsquema())->asegurarDestinoLibre(
            $region,
            $dl,
            $esquemaBase,
            $comun,
            $sv,
            $sf,
        );

        if ($comun !== 0) {
            $oConfigDB = new ConfigDB('importar');
            $config = $oConfigDB->getEsquema('public');

            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();

            $oDBRol->setDbConexion($oDevelPC);
            $oDBRol->setUser($esquema);

            $oDBRol->addGrupo('orbix');

            $oDBRol->crearSchema();

            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($config);
            $oDBEsquemaCreate->setRegionRef($RegionRef);
            $oDBEsquemaCreate->setDlRef($DlRef);
            $oDBEsquemaCreate->setRegionNew($RegionNew);
            $oDBEsquemaCreate->setDlNew($DlNew);
            $oDBEsquemaCreate->crear();
            $sqlVolcadoComun = $oDBEsquemaCreate->getFileNew();

            $oDBRol->delGrupo('orbix');

            if (!$isDocker) {
                $config = $oConfigDB->getEsquema('public_select');

                $oConexion = new DBConnection($config);
                $oDevelPC = $oConexion->getPDO();

                $oDBRol->setDbConexion($oDevelPC);
                $oDBRol->setUser($esquema);

                $oDBRol->addGrupo('orbix');

                $oDBRol->crearSchema();

                $oDBEsquemaSelect = new DBEsquemaCreate();
                $oDBEsquemaSelect->setConfig($config);
                $oDBEsquemaSelect->setFileNew($sqlVolcadoComun);
                $aviso = $oDBEsquemaSelect->crear_select('comun');
                if ($aviso !== null) {
                    $avisos[] = $aviso;
                }

                $oDBRol->delGrupo('orbix');
            }
            $schema = $RegionNew . '-' . $DlNew;
            $DbSchemaRepository = $this->container->get(DbSchemaRepositoryInterface::class);
            $DbSchemaRepository->llenarNuevo($schema, 'comun');
        }

        if ($sv !== 0) {
            $oConfigDB = new ConfigDB('importar');
            $config = $oConfigDB->getEsquema('publicv');
            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();

            $oDBRol = new DBRol();
            $oDBRol->setDbConexion($oDevelPC);
            $oDBRol->setUser($esquemav);
            $oDBRol->addGrupo('orbixv');
            $oDBRol->crearSchema();
            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($config);
            $oDBEsquemaCreate->setRegionRef($RegionRef);
            $oDBEsquemaCreate->setDlRef($DlRef);
            $oDBEsquemaCreate->setRegionNew($RegionNew);
            $oDBEsquemaCreate->setDlNew($DlNew);
            $oDBEsquemaCreate->crear();
            $sqlVolcadoSv = $oDBEsquemaCreate->getFileNew();
            $oDBRol->delGrupo('orbixv');

            $schema = $RegionNew . '-' . $DlNew;
            $DbSchemaRepository = $this->container->get(DbSchemaRepositoryInterface::class);
            $DbSchemaRepository->llenarNuevo($schema, 'sv');

            $config = $oConfigDB->getEsquema('publicv-e');
            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();

            $oDBRol = new DBRol();
            $oDBRol->setDbConexion($oDevelPC);
            $oDBRol->setUser($esquemav);
            $oDBRol->addGrupo('orbixv');
            $oDBRol->crearSchema();
            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($config);
            $oDBEsquemaCreate->setRegionRef($RegionRef);
            $oDBEsquemaCreate->setDlRef($DlRef);
            $oDBEsquemaCreate->setRegionNew($RegionNew);
            $oDBEsquemaCreate->setDlNew($DlNew);
            $oDBEsquemaCreate->crear();
            $sqlVolcadoSve = $oDBEsquemaCreate->getFileNew();
            $oDBRol->delGrupo('orbixv');

            // En Docker, sv-e ya se rellenó con crear(); crear_select duplicaría el .sql en la misma BD.
            if (!$isDocker) {
                $config = $oConfigDB->getEsquema('publicv-e_select');
                $oConexion = new DBConnection($config);
                $oDevelPC = $oConexion->getPDO();

                $oDBRol = new DBRol();
                $oDBRol->setDbConexion($oDevelPC);
                $oDBRol->setUser($esquemav);
                $oDBRol->addGrupo('orbixv');
                $oDBRol->crearSchema();
                $oDBEsquemaSelect = new DBEsquemaCreate();
                $oDBEsquemaSelect->setConfig($config);
                $oDBEsquemaSelect->setFileNew($sqlVolcadoSve);
                $aviso = $oDBEsquemaSelect->crear_select('sv-e');
                if ($aviso !== null) {
                    $avisos[] = $aviso;
                }
                $oDBRol->delGrupo('orbixv');
            }

            $schema = $RegionNew . '-' . $DlNew;
            $DbSchemaRepository = $this->container->get(DbSchemaRepositoryInterface::class);
            $DbSchemaRepository->llenarNuevo($schema, 'sv-e');
        }
        if ($sf !== 0) {
            $oConfigDB = new ConfigDB('importar');
            $config = $oConfigDB->getEsquema('publicf');
            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();

            $oDBRol->setDbConexion($oDevelPC);
            $oDBRol->setUser($esquemaf);
            $oDBRol->addGrupo('orbixf');
            $oDBRol->crearSchema();
            $oDBEsquemaCreate = new DBEsquemaCreate();
            $oDBEsquemaCreate->setConfig($config);
            $oDBEsquemaCreate->setRegionRef($RegionRef);
            $oDBEsquemaCreate->setDlRef($DlRef);
            $oDBEsquemaCreate->setRegionNew($RegionNew);
            $oDBEsquemaCreate->setDlNew($DlNew);
            $oDBEsquemaCreate->crear();

            $oDBRol->delGrupo('orbixf');

            $schema = $RegionNew . '-' . $DlNew;
            $DbSchemaRepository = $this->container->get(DbSchemaRepositoryInterface::class);
            $DbSchemaRepository->llenarNuevo($schema, 'sf');
        }

        return $avisos;
    }
}
