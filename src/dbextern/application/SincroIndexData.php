<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\CopiarBDU;
use src\dbextern\domain\SincroDB;
use src\shared\config\ConfigGlobal;

class SincroIndexData
{
    private IdMatchPersonaRepositoryInterface $idMatchRepository;
    private PersonaBDURepositoryInterface $personaBDURepository;

    public function __construct(
        IdMatchPersonaRepositoryInterface $idMatchRepository,
        PersonaBDURepositoryInterface     $personaBDURepository
    )
    {
        $this->idMatchRepository = $idMatchRepository;
        $this->personaBDURepository = $personaBDURepository;
    }

    /**
     * Calcula los 10 contadores del dashboard de sincronización.
     *
     * @return array Datos serializables para el frontend
     */
    public function __invoke(string $tipo_persona): array
    {
        $mi_dl = ConfigGlobal::mi_delef();
        $region = ConfigGlobal::mi_region();

        $oSincroDB = new SincroDB();
        $dl_listas = $oSincroDB->dlOrbix2Listas($mi_dl);

        $oCopiarBDU = new CopiarBDU();
        $fecha_actualizacion = $oCopiarBDU->ultimaActualizacion() . ' UTC';

        $id_tipo = $this->resolverIdTipo($tipo_persona);
        if (empty($id_tipo)) {
            return ['error' => _("no tiene permisos")];
        }

        $obj_pau = $this->resolverGestor($tipo_persona);
        $obj = 'personas\\model\\entity\\' . $obj_pau;
        $GesPersonas = new $obj();

        // Personas BDU
        $tabla = 'tmp_bdu';
        $Query = "SELECT * FROM $tabla
                  WHERE identif::text LIKE '$id_tipo%' AND  Dl='$dl_listas'
                       AND (pertenece_r='$region' OR compartida_con_r='$region') ";
        $cPersonasBDU = $this->personaBDURepository->getPersonaBDUQuery($Query);

        // Añadir las delegaciones dependientes de la región
        if (array_key_exists($region, ConfigGlobal::REGIONES_CON_DL)) {
            $cPersonasBDU_n = [];
            foreach (ConfigGlobal::REGIONES_CON_DL[$region] as $dl_n) {
                $Query = "SELECT * FROM $tabla
                  WHERE identif::text LIKE '$id_tipo%' AND  Dl='$dl_n'
                       AND (pertenece_r='$region' OR compartida_con_r='$region') ";
                $cPersonasBDU_n[] = $this->personaBDURepository->getPersonaBDUQuery($Query);
            }
            $cPersonasBDU = array_merge($cPersonasBDU, ...array_values($cPersonasBDU_n));
        }

        $p1_unidas_dl = 0;
        $p2_unidas_otradl = 0;
        $p3_unidas_desaparecidas = 0;
        $p456_listas_no_unidas = 0;

        $a_ids_traslados = [];
        $a_ids_desaparecidos_de_orbix = [];

        $oSincroDB->setTipo_persona($tipo_persona);
        foreach ($cPersonasBDU as $oPersonaBDU) {
            $id_nom_listas = $oPersonaBDU->getIdentif();

            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_listas' => $id_nom_listas]);
            if (!empty($cIdMatch[0]) && count($cIdMatch) > 0) {
                $id_orbix = $cIdMatch[0]->getId_orbix();
                $cPersonas = $GesPersonas->getPersonas(['id_nom' => $id_orbix]);
                if (!empty($cPersonas) && count($cPersonas) > 0) {
                    $situacion = $cPersonas[0]->getSituacion();
                    if ($situacion === 'A') {
                        $p1_unidas_dl++;
                    } else {
                        $dl_orbix = $oSincroDB->buscarEnOrbix($id_orbix);
                        if (!empty($dl_orbix)) {
                            $p2_unidas_otradl++;
                            $a_ids_traslados[] = $id_orbix;
                        }
                    }
                } else {
                    $dl_orbix = $oSincroDB->buscarEnOrbix($id_orbix);
                    if (!empty($dl_orbix)) {
                        $p2_unidas_otradl++;
                        $a_ids_traslados[] = $id_orbix;
                    } else {
                        $p3_unidas_desaparecidas++;
                        $a_ids_desaparecidos_de_orbix[] = $id_nom_listas;
                    }
                }
            } else {
                $p456_listas_no_unidas++;
            }
        }

        // Personas Orbix
        $cPersonasOrbix = $GesPersonas->getPersonas(['situacion' => 'A']);
        $p7_orbix_unidas_otra_dl = 0;
        $p8_orbix_unidas_desaparecidas = 0;
        $p910_orbix_no_unidas = 0;

        $a_ids_traslados_A = [];
        $a_ids_desaparecidos_de_listas = [];

        foreach ($cPersonasOrbix as $oPersonaOrbix) {
            $id_nom_orbix = $oPersonaOrbix->getId_nom();

            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_orbix' => $id_nom_orbix]);
            if (!empty($cIdMatch[0]) && count($cIdMatch) > 0) {
                $id_nom_listas = $cIdMatch[0]->getId_listas();
                $oPersonaBDU = $this->personaBDURepository->findById($id_nom_listas);
                if ($oPersonaBDU === null || empty($oPersonaBDU->getApeNom())) {
                    $p8_orbix_unidas_desaparecidas++;
                    $a_ids_desaparecidos_de_listas[] = $id_nom_orbix;
                } else {
                    $dl_persona = $oPersonaBDU->getDl();
                    if ($dl_persona !== $dl_listas) {
                        $p7_orbix_unidas_otra_dl++;
                        $a_ids_traslados_A[] = $id_nom_listas;
                    }
                }
            } else {
                $p910_orbix_no_unidas++;
            }
        }

        return [
            'fecha_actualizacion' => $fecha_actualizacion,
            'region' => $region,
            'dl_listas' => $dl_listas,
            'tipo_persona' => $tipo_persona,
            'p1_unidas_dl' => $p1_unidas_dl,
            'p2_unidas_otradl' => $p2_unidas_otradl,
            'p3_unidas_desaparecidas' => $p3_unidas_desaparecidas,
            'p456_listas_no_unidas' => $p456_listas_no_unidas,
            'p7_orbix_unidas_otra_dl' => $p7_orbix_unidas_otra_dl,
            'p8_orbix_unidas_desaparecidas' => $p8_orbix_unidas_desaparecidas,
            'p910_orbix_no_unidas' => $p910_orbix_no_unidas,
            'ids_traslados' => $a_ids_traslados,
            'ids_desaparecidos_de_orbix' => $a_ids_desaparecidos_de_orbix,
            'ids_traslados_A' => $a_ids_traslados_A,
            'ids_desaparecidos_de_listas' => $a_ids_desaparecidos_de_listas,
            // link_specs para las sub-pantallas
            'link_spec_ver_traslados' => [
                'path' => 'frontend/dbextern/controller/ver_traslados.php',
                'query' => ['region' => $region, 'tipo_persona' => $tipo_persona, 'ids_traslados' => json_encode($a_ids_traslados)],
            ],
            'link_spec_ver_desaparecidos_orbix' => [
                'path' => 'frontend/dbextern/controller/ver_desaparecidos_de_orbix.php',
                'query' => ['region' => $region, 'tipo_persona' => $tipo_persona, 'ids_desaparecidos_de_orbix' => json_encode($a_ids_desaparecidos_de_orbix)],
            ],
            'link_spec_ver_listas' => [
                'path' => 'frontend/dbextern/controller/ver_listas.php',
                'query' => ['region' => $region, 'dl' => $dl_listas, 'tipo_persona' => $tipo_persona],
            ],
            'link_spec_ver_orbix_otradl' => [
                'path' => 'frontend/dbextern/controller/ver_orbix_otradl.php',
                'query' => ['region' => $region, 'tipo_persona' => $tipo_persona, 'ids_traslados_A' => json_encode($a_ids_traslados_A)],
            ],
            'link_spec_ver_desaparecidos_listas' => [
                'path' => 'frontend/dbextern/controller/ver_desaparecidos_de_listas.php',
                'query' => ['region' => $region, 'tipo_persona' => $tipo_persona, 'ids_desaparecidos_de_listas' => json_encode($a_ids_desaparecidos_de_listas)],
            ],
            'link_spec_ver_orbix' => [
                'path' => 'frontend/dbextern/controller/ver_orbix.php',
                'query' => ['region' => $region, 'tipo_persona' => $tipo_persona],
            ],
            'link_spec_self' => [
                'path' => 'frontend/dbextern/controller/sincro_index.php',
                'query' => ['tipo' => $tipo_persona],
            ],
        ];
    }

    private function resolverIdTipo(string $tipo_persona): int
    {
        return match ($tipo_persona) {
            'n' => ($_SESSION['oPerm']->have_perm_oficina('sm')) ? 1 : 0,
            'a' => ($_SESSION['oPerm']->have_perm_oficina('agd')) ? 2 : 0,
            's' => ($_SESSION['oPerm']->have_perm_oficina('sg')) ? 3 : 0,
            'sssc' => ($_SESSION['oPerm']->have_perm_oficina('des')) ? 4 : 0,
            default => 0,
        };
    }

    private function resolverGestor(string $tipo_persona): string
    {
        return match ($tipo_persona) {
            'n' => 'GestorPersonaN',
            'a' => 'GestorPersonaAgd',
            's' => 'GestorPersonaS',
            'sssc' => 'GestorPersonaSSSC',
            default => '',
        };
    }
}
