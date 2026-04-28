<?php

namespace src\dossiers\application;

use src\shared\config\ConfigGlobal;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
/**
 * Filas de la tabla de relación de dossiers (modo lista en dossiers_ver).
 * `href_ver` / `href_abrir` se firman en el borde HTTP (ver `dossiers_lista_fichas_data.php`).
 */
class DossiersListaFichasData
{
    /**
     * @return array{a_filas: list<array<string, mixed>>, web_icons: string}
     */
    public static function build(string $pau, int $id_pau, string $Qobj_pau): array
    {
        $aWhere = [
            'tabla_from' => $pau,
            '_ordre' => 'descripcion',
        ];
        $TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        $cTipoDossier = $TipoDossierRepository->getTiposDossiers($aWhere);
        $DossierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        $i = 0;
        $a_filas = [];

        foreach ($cTipoDossier as $oTipoDossier) {
            $id_tipo_dossier = $oTipoDossier->getId_tipo_dossier();
            $tabla_from = $oTipoDossier->getTabla_from();
            $tabla_to = $oTipoDossier->getTabla_to();
            $app = $oTipoDossier->getApp();
            $descripcion = $oTipoDossier->getDescripcion();
            $depende_modificar = 1;
            $id_dossier = $id_tipo_dossier;

            if (!ConfigGlobal::is_app_installed($app)) {
                continue;
            }
            if (ConfigGlobal::mi_ambito() === 'rstgr') {
                $status_dossier = 'f';
            } else {
                $oDossier = $DossierRepository->findByPk(
                    DossierPk::fromArray([
                        'tabla' => $tabla_from,
                        'id_pau' => $id_pau,
                        'id_tipo_dossier' => $id_tipo_dossier,
                    ])
                );
                $status_dossier = $oDossier?->isActive() ?? false;
            }
            switch ($status_dossier) {
                case "t":
                    $a_filas[$i]['imagen'] = ConfigGlobal::getWeb_icons() . '/folder.open.gif';
                    break;
                case "f":
                default:
                    $a_filas[$i]['imagen'] = ConfigGlobal::getWeb_icons() . '/folder.gif';
                    break;
            }
            $a_filas[$i]['clase'] = $i % 2 ? 'imp' : 'par';
            $a_filas[$i]['descripcion'] = $descripcion;
            $perm_a = 3;

            $a_filas[$i]['href_ver_link_spec'] = [
                'path' => 'frontend/dossiers/controller/dossiers_ver.php',
                'query' => [
                    'pau' => $pau,
                    'id_pau' => $id_pau,
                    'obj_pau' => $Qobj_pau,
                    'id_dossier' => $id_dossier,
                    'permiso' => $perm_a,
                    'depende' => $depende_modificar,
                ],
            ];
            $a_filas[$i]['href_abrir_link_spec'] = [
                'path' => 'frontend/dossiers/controller/dossier_abrir.php',
                'query' => [
                    'pau' => $pau,
                    'id_pau' => $id_pau,
                    'obj_pau' => $Qobj_pau,
                    'id_dossier' => $id_dossier,
                    'tabla_to' => $tabla_to,
                    'permiso' => $perm_a,
                ],
            ];
            $a_filas[$i]['perm_a'] = $perm_a;
            $i++;
        }

        return [
            'a_filas' => $a_filas,
            'web_icons' => ConfigGlobal::getWeb_icons(),
        ];
    }
}
