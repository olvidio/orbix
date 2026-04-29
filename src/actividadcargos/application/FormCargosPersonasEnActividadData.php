<?php

namespace src\actividadcargos\application;

use src\shared\config\ConfigGlobal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use frontend\shared\web\Desplegable;
use function src\shared\domain\helpers\is_true;

/**
 * Datos para `form_cargos_personas_en_actividad` (vista por persona).
 */
final class FormCargosPersonasEnActividadData
{
    /**
     * @return list<array{id_activ: int, nom_activ: string}>
     */
    public static function actividadesToRows(iterable $cActividades): array
    {
        $rows = [];
        foreach ($cActividades as $oActividad) {
            $rows[] = [
                'id_activ' => (int)$oActividad->getId_activ(),
                'nom_activ' => (string)$oActividad->getNom_activ(),
            ];
        }
        return $rows;
    }

    /**
     * @return array<string, mixed> Incluye `hash_form_config` (el front convierte a `hash_campos_html`).
     */
    public static function build(array $post): array
    {
        $Qid_item = '';
        $id_cargo = '';

        $Qpermiso = (int)($post['permiso'] ?? 0);

        $a_sel = isset($post['sel']) ? (array)$post['sel'] : [];
        $Qque_dl = '';
        $Qid_tipo = 0;
        if (!empty($a_sel)) {
            $Qid_item = (int)strtok($a_sel[0], '#');
        } else {
            $Qque_dl = (string)($post['que_dl'] ?? '');
            $Qid_tipo = (int)($post['id_tipo'] ?? 0);
        }

        $Qmod = (string)($post['mod'] ?? '');
        $pau = (string)($post['pau'] ?? '');
        $Qid_pau = (int)($post['id_pau'] ?? 0);
        $Qid_dossier = (int)($post['id_dossier'] ?? 0);
        if ($Qid_dossier <= 0) {
            $Qid_dossier = 1302;
        }

        $obj = 'actividadcargos\\model\\entity\\ActividadCargo';

        $id_activ_real = '';
        $nom_activ = '';
        $observ = '';
        $puede_agd = '';
        $aActividadesRows = [];

        if (!empty($Qid_item)) {
            $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
            $oActividadCargo = $ActividadCargoRepository->findById($Qid_item);
            $id_activ = $oActividadCargo->getId_activ();
            $id_cargo = $oActividadCargo->getId_cargo();
            $puede_agd = $oActividadCargo->isPuede_agd();
            $observ = $oActividadCargo->getObserv();

            $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
            $oActividad = $ActividadRepository->findById($id_activ);
            $nom_activ = $oActividad->getNom_activ();
            $id_activ_real = $id_activ;
        } else {
            if (empty($Qid_tipo)) {
                $mi_sfsv = ConfigGlobal::mi_sfsv();
                $id_tipo = '^' . $mi_sfsv;
            } else {
                $id_tipo = '^' . $Qid_tipo;
            }
            $aWhere = [];
            $aOperadores = [];
            if (!empty($Qque_dl)) {
                $aWhere['dl_org'] = $Qque_dl;
            } else {
                $aWhere['dl_org'] = ConfigGlobal::mi_delef();
                $aOperadores['dl_org'] = '!=';
            }
            $aWhere['id_tipo_activ'] = $id_tipo;
            $aOperadores['id_tipo_activ'] = '~';
            $aWhere['status'] = StatusId::ACTUAL;
            $aWhere['_ordre'] = 'f_ini';

            $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
            $aActividadesRows = self::actividadesToRows(
                $ActividadRepository->getActividades($aWhere, $aOperadores)
            );
        }

        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $oDesplegableCargos = new Desplegable();
        $oDesplegableCargos->setNombre('id_cargo');
        $oDesplegableCargos->setBlanco(false);
        $oDesplegableCargos->setOpciones($CargoRepository->getArrayCargos());
        $oDesplegableCargos->setOpcion_sel($id_cargo);

        $chk = (!empty($puede_agd) && is_true($puede_agd)) ? 'checked' : '';

        $camposForm = 'id_cargo!observ';
        $camposNo = 'puede_agd';
        $a_camposHidden = [
            'id_item' => $Qid_item,
            'id_nom' => $Qid_pau,
            'mod' => $Qmod,
        ];
        if (!empty($id_activ_real)) {
            $a_camposHidden['id_activ'] = $id_activ_real;
        } else {
            if ($Qmod === 'nuevo') {
                $camposNo .= '!asis';
                $camposForm .= '!asis_presente';
            }
            $camposForm .= '!id_activ';
        }

        $web = rtrim(ConfigGlobal::getWeb(), '/');

        return [
            'obj' => $obj,
            'Qpermiso' => $Qpermiso,
            'id_activ_real' => $id_activ_real,
            'nom_activ' => $nom_activ,
            'aActividades' => $aActividadesRows,
            'desplegable_cargos_html' => $oDesplegableCargos->desplegable(),
            'hash_form_config' => [
                'campos_form' => $camposForm,
                'campos_no' => $camposNo,
                'campos_hidden' => $a_camposHidden,
            ],
            'chk' => $chk,
            'observ' => $observ,
            'Qmod' => $Qmod,
            'url_cargo_nuevo' => $web . '/src/actividadcargos/cargo_nuevo',
            'url_cargo_editar' => $web . '/src/actividadcargos/cargo_editar',
        ];
    }
}
