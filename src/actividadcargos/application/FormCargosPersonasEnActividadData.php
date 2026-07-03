<?php

namespace src\actividadcargos\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\StatusId;
use src\shared\config\ConfigGlobal;

/**
 * Datos para `form_cargos_personas_en_actividad` (vista por persona).
 */
final class FormCargosPersonasEnActividadData
{
    public function __construct(
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private CargoRepositoryInterface $cargoRepository,
    ) {
    }

    /**
     * @param iterable<ActividadAll> $cActividades
     * @return list<array{id_activ: int, nom_activ: string}>
     */
    public static function actividadesToRows(iterable $cActividades): array
    {
        $rows = [];
        foreach ($cActividades as $oActividad) {
            $rows[] = [
                'id_activ' => (int) $oActividad->getId_activ(),
                'nom_activ' => (string) $oActividad->getNom_activ(),
            ];
        }
        return $rows;
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed> Incluye `hash_form_config` (el front convierte a `hash_campos_html`).
     */
    public function build(array $post): array
    {
        $Qid_item = '';
        $id_cargo = '';

        $Qpermiso = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'permiso');

        $a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($post, 'sel');
        $Qque_dl = '';
        $Qid_tipo = 0;
        if ($a_sel !== []) {
            $Qid_item = (int) strtok($a_sel[0], '#');
        } else {
            $Qque_dl = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'que_dl');
            $Qid_tipo = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_tipo');
        }

        $Qmod = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'mod');
        $Qid_pau = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_pau');
        $Qid_dossier = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_dossier');
        if ($Qid_dossier <= 0) {
            $Qid_dossier = 1302;
        }

        $obj = 'actividadcargos\\model\\entity\\ActividadCargo';

        $id_activ_real = '';
        $nom_activ = '';
        $observ = '';
        $puede_agd = '';
        $aActividadesRows = [];

        if ($Qid_item !== '') {
            $oActividadCargo = $this->actividadCargoRepository->findById($Qid_item);
            if ($oActividadCargo === null) {
                return ['error' => _('no encuentro el cargo')];
            }
            $id_activ = $oActividadCargo->getId_activ();
            $id_cargo = $oActividadCargo->getId_cargo();
            $puede_agd = $oActividadCargo->isPuede_agd();
            $observ = (string) ($oActividadCargo->getObserv() ?? '');

            $oActividad = $this->actividadRepository->findById($id_activ);
            if ($oActividad === null) {
                return ['error' => _('actividad no encontrada')];
            }
            $nom_activ = $oActividad->getNom_activ();
            $id_activ_real = $id_activ;
        } else {
            if ($Qid_tipo === 0) {
                $mi_sfsv = ConfigGlobal::mi_sfsv();
                $id_tipo = '^' . $mi_sfsv;
            } else {
                $id_tipo = '^' . $Qid_tipo;
            }
            $aWhere = [];
            $aOperadores = [];
            if ($Qque_dl !== '') {
                $aWhere['dl_org'] = $Qque_dl;
            } else {
                $aWhere['dl_org'] = ConfigGlobal::mi_delef();
                $aOperadores['dl_org'] = '!=';
            }
            $aWhere['id_tipo_activ'] = $id_tipo;
            $aOperadores['id_tipo_activ'] = '~';
            $aWhere['status'] = StatusId::ACTUAL;
            $aWhere['_ordre'] = 'f_ini';

            $aActividadesRows = self::actividadesToRows(
                $this->actividadRepository->getActividades($aWhere, $aOperadores)
            );
        }

        $chk = (!empty($puede_agd) && \src\shared\domain\helpers\FuncTablasSupport::isTrue($puede_agd)) ? 'checked' : '';

        $camposForm = 'id_cargo!observ';
        $camposNo = 'puede_agd';
        $a_camposHidden = [
            'id_item' => $Qid_item,
            'id_nom' => $Qid_pau,
            'mod' => $Qmod,
        ];
        if ($id_activ_real !== '') {
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
            'cargos_select' => [
                'opciones' => $this->cargoRepository->getArrayCargos(),
                'opcion_sel' => (string) $id_cargo,
            ],
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
