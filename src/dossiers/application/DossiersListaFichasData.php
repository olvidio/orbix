<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\shared\config\ConfigGlobal;

/**
 * Filas de la tabla de relación de dossiers (modo lista en dossiers_ver).
 * `href_ver` / `href_abrir` se firman en el borde HTTP (ver `dossiers_lista_fichas_data.php`).
 */
class DossiersListaFichasData
{
    public function __construct(
        private TipoDossierRepositoryInterface $tipoDossierRepository,
        private DossierRepositoryInterface $dossierRepository,
        private DossierTipoFileSuffixResolver $suffixResolver,
    ) {
    }

    /**
     * @return array{a_filas: list<array<string, mixed>>, web_icons: string}
     */
    public function build(string $pau, int $id_pau, string $Qobj_pau): array
    {
        $aWhere = [
            'tabla_from' => $pau,
            '_ordre' => 'descripcion',
        ];
        $cTipoDossier = $this->tipoDossierRepository->getTiposDossiers($aWhere);
        $i = 0;
        $a_filas = [];

        foreach ($cTipoDossier as $oTipoDossier) {
            $id_tipo_dossier = $oTipoDossier->getId_tipo_dossier();
            $tabla_to = $oTipoDossier->getTabla_to() ?? '';
            $descripcion = $oTipoDossier->getDescripcion() ?? '';
            $permiso_lectura = $oTipoDossier->getPermiso_lectura();
            $permiso_escritura = $oTipoDossier->getPermiso_escritura() ?? 0;
            $depende_modificar = $oTipoDossier->isDepende_modificar();
            $id_dossier = $id_tipo_dossier;

            if (!$this->isTipoDossierDisponible($oTipoDossier)) {
                continue;
            }
            if (!$this->suffixResolver->canRenderFichaSegment($oTipoDossier)) {
                continue;
            }
            if (!ConfigGlobal::usaTablaDossiersAbierto()) {
                $status_dossier = 'f';
            } else {
                $oDossier = $this->dossierRepository->findByPk(
                    DossierPk::fromArray([
                        'tabla' => $pau,
                        'id_pau' => $id_pau,
                        'id_tipo_dossier' => $id_tipo_dossier,
                    ])
                );
                $status_dossier = ($oDossier?->isActive() ?? false) ? 't' : 'f';
            }
            switch ($status_dossier) {
                case 't':
                    $a_filas[$i]['imagen'] = ConfigGlobal::getWeb_icons() . '/folder.open.gif';
                    break;
                case 'f':
                default:
                    $a_filas[$i]['imagen'] = ConfigGlobal::getWeb_icons() . '/folder.gif';
                    break;
            }
            $a_filas[$i]['clase'] = $i % 2 ? 'imp' : 'par';
            $a_filas[$i]['descripcion'] = $descripcion;
            $perm_a = PermDossier::permiso(
                $permiso_lectura,
                $permiso_escritura,
                $depende_modificar,
                $pau,
                $id_pau
            );

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
            'a_filas' => array_values($a_filas),
            'web_icons' => ConfigGlobal::getWeb_icons(),
        ];
    }

    /**
     * - Habitaciones CDC (2006): solo con app `ubiscamas` instalada.
     * - Resto con `app` en BD: según esa app.
     * - Sin `app`: visible (comportamiento legacy).
     */
    private function isTipoDossierDisponible(TipoDossier $tipo): bool
    {
        if ($this->esDossierUbiscamas($tipo)) {
            return ConfigGlobal::is_app_installed('ubiscamas');
        }

        $app = $tipo->getApp() ?? '';
        if ($app === '') {
            return true;
        }

        return ConfigGlobal::is_app_installed($app);
    }

    private function esDossierUbiscamas(TipoDossier $tipo): bool
    {
        if (($tipo->getCodigo() ?? '') === 'habitaciones_cdc') {
            return true;
        }
        if ($tipo->getId_tipo_dossier() === 2006) {
            return true;
        }

        return ($tipo->getTabla_to() ?? '') === 'du_habitaciones_dl';
    }
}
