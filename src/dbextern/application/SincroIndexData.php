<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\CopiarBDU;
use src\dbextern\application\support\SincroDBFactory;
use src\permisos\domain\XPermisos;
use src\personas\application\support\PersonaRepositoryResolver;
use src\shared\config\ConfigGlobal;

class SincroIndexData
{
    public function __construct(
        private IdMatchPersonaRepositoryInterface $idMatchRepository,
        private PersonaBDURepositoryInterface $personaBDURepository,
        private CopiarBDU $copiarBDU,
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private SincroDBFactory $sincroDBFactory,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(string $tipo_persona): array
    {
        $mi_dl = ConfigGlobal::mi_delef();
        $region = ConfigGlobal::mi_region();

        $oSincroDB = $this->sincroDBFactory->create();
        $dl_listas = $oSincroDB->dlOrbix2Listas($mi_dl);
        if ($dl_listas === false) {
            return ['error' => _("No se encontró la delegación en listas")];
        }

        $fecha_actualizacion = $this->copiarBDU->ultimaActualizacion() . ' UTC';

        $id_tipo = $this->resolverIdTipo($tipo_persona);
        if ($id_tipo === 0) {
            return ['error' => _("no tiene permisos")];
        }

        $obj_pau = $this->resolverObjPau($tipo_persona);
        if ($obj_pau === '') {
            return ['error' => _("No existe la clase de la persona")];
        }

        try {
            $repoPersona = $this->personaRepositoryResolver->repositorio($obj_pau);
        } catch (\InvalidArgumentException) {
            return ['error' => _("No existe la clase de la persona")];
        }

        $tabla = 'tmp_bdu';
        $Query = "SELECT * FROM $tabla
                  WHERE identif::text LIKE '$id_tipo%' AND  Dl='$dl_listas'
                       AND (pertenece_r='$region' OR compartida_con_r='$region') ";
        $cPersonasBDU = $this->personaBDURepository->getPersonaBDUQuery($Query);

        if (array_key_exists($region, ConfigGlobal::REGIONES_CON_DL)) {
            $cPersonasBDU_n = [];
            foreach (ConfigGlobal::REGIONES_CON_DL[$region] as $dl_n) {
                $Query = "SELECT * FROM $tabla
                  WHERE identif::text LIKE '$id_tipo%' AND  Dl='$dl_n'
                       AND (pertenece_r='$region' OR compartida_con_r='$region') ";
                $cPersonasBDU_n[] = $this->personaBDURepository->getPersonaBDUQuery($Query);
            }
            $cPersonasBDU = array_merge($cPersonasBDU, ...$cPersonasBDU_n);
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
            if ($cIdMatch !== []) {
                $id_orbix = $cIdMatch[0]->getId_orbix();
                if ($id_orbix === null) {
                    continue;
                }
                $cPersonas = $repoPersona->getPersonas(['id_nom' => $id_orbix]);
                if ($cPersonas !== []) {
                    $situacion = $cPersonas[0]->getSituacion();
                    if ($situacion === 'A') {
                        $p1_unidas_dl++;
                    } else {
                        $dl_orbix = $oSincroDB->buscarEnOrbix($id_orbix);
                        if ($dl_orbix !== '') {
                            $p2_unidas_otradl++;
                            $a_ids_traslados[] = $id_orbix;
                        }
                    }
                } else {
                    $dl_orbix = $oSincroDB->buscarEnOrbix($id_orbix);
                    if ($dl_orbix !== '') {
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

        $cPersonasOrbix = $repoPersona->getPersonas(['situacion' => 'A']);
        $p7_orbix_unidas_otra_dl = 0;
        $p8_orbix_unidas_desaparecidas = 0;
        $p910_orbix_no_unidas = 0;

        $a_ids_traslados_A = [];
        $a_ids_desaparecidos_de_listas = [];

        foreach ($cPersonasOrbix as $oPersonaOrbix) {
            $id_nom_orbix = $oPersonaOrbix->getId_nom();

            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_orbix' => $id_nom_orbix]);
            if ($cIdMatch !== []) {
                $id_nom_listas = $cIdMatch[0]->getId_listas();
                $oPersonaBDU = $this->personaBDURepository->findById($id_nom_listas);
                if ($oPersonaBDU === null || $oPersonaBDU->getApenom() === '') {
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
        $oPerm = $_SESSION['oPerm'] ?? null;
        if (!$oPerm instanceof XPermisos) {
            return 0;
        }

        return match ($tipo_persona) {
            'n' => $oPerm->have_perm_oficina('sm') ? 1 : 0,
            'a' => $oPerm->have_perm_oficina('agd') ? 2 : 0,
            's' => $oPerm->have_perm_oficina('sg') ? 3 : 0,
            'sssc' => $oPerm->have_perm_oficina('des') ? 4 : 0,
            default => 0,
        };
    }

    private function resolverObjPau(string $tipo_persona): string
    {
        return match ($tipo_persona) {
            'n' => 'PersonaN',
            'a' => 'PersonaAgd',
            's' => 'PersonaS',
            'sssc' => 'PersonaSSSC',
            default => '',
        };
    }
}
