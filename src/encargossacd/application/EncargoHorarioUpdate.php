<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoHorario;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Alta/edición/baja de horario de encargo (tabla encargo_horario).
 */
final class EncargoHorarioUpdate
{

    public function __construct(
        private EncargoHorarioRepositoryInterface $encargoHorarioRepository
    ) {
    }

    /**
     * @param array<string, mixed> $post
     * @return array{ok: true}|array{_error: string}
     */
    public function ejecutar(array $post): array
    {
        $mod = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'mod');
        if ($mod === 'eliminar') {
            $selNom = $post['sel_nom'];
            if (!is_array($selNom) || !array_key_exists(0, $selNom)) {
                return ['_error' => _('acción no válida')];
            }
            $first = $selNom[0];
            $token = is_scalar($first) ? (string) $first : '';
            $parts = explode('#', $token, 2);
            $id_item_h = (int)$parts[0];
            $o = $this->encargoHorarioRepository->findById($id_item_h);
            if ($o !== null && $this->encargoHorarioRepository->Eliminar($o) === false) {
                return ['_error' => $this->encargoHorarioRepository->getErrorTxt()];
            }

            return ['ok' => true];
        }

        $Qdia = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'dia');
        $Qid_item_h = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_item_h');
        $Qid_enc = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_enc');
        $Qf_ini = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'f_ini');
        $Qf_fin = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'f_fin');
        $Qdia_ref = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'dia_ref');
        $Qdia_num = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'dia_num');
        $Qmas_menos = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'mas_menos');
        $Qdia_inc = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'dia_inc');
        $Qh_ini = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'h_ini');
        $Qh_fin = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'h_fin');
        $Qn_sacd = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'n_sacd');
        $Qmes = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'mes');

        $oF_ini = $Qf_ini === '' ? null : new DateTimeLocal($Qf_ini);
        $oF_fin = $Qf_fin === '' ? null : new DateTimeLocal($Qf_fin);
        $oH_ini = $Qh_ini === '' ? null : TimeLocal::fromString($Qh_ini);
        $oH_fin = $Qh_fin === '' ? null : TimeLocal::fromString($Qh_fin);

        if ($Qmas_menos === '' || $Qmas_menos === '0') {
            $Qdia_ref = $Qdia;
        }

        if ($mod === 'nuevo') {
            if ($Qf_ini === '' || $Qdia === '') {
                return ['_error' => _('Debe llenar todos los campos que tengan un (*)')];
            }

            $newId = $this->encargoHorarioRepository->getNewId();
            $o = new EncargoHorario();
            $o->setId_item_h((int)$newId);
            $o->setId_enc($Qid_enc);
            $o->setF_ini($oF_ini);
            $o->setF_fin($oF_fin);
            $o->setDia_ref($Qdia_ref === '' ? null : $Qdia_ref);
            $o->setDia_num($Qdia_num === 0 ? null : $Qdia_num);
            $o->setMas_menos($Qmas_menos);
            $o->setDia_inc($Qdia_inc);
            $o->setH_ini($oH_ini);
            $o->setH_fin($oH_fin);
            $o->setN_sacd($Qn_sacd);
            $o->setMes($Qmes);
            if ($this->encargoHorarioRepository->Guardar($o) === false) {
                return ['_error' => _('hay un error, no se ha guardado') . "\n" . $this->encargoHorarioRepository->getErrorTxt()];
            }

            return ['ok' => true];
        }

        if ($mod === 'editar') {
            if ($Qf_ini === '' || $Qdia === '') {
                return ['_error' => _('Debe llenar todos los campos que tengan un (*)')];
            }

            $o = $this->encargoHorarioRepository->findById($Qid_item_h);
            if ($o === null) {
                return ['_error' => _('registro no encontrado')];
            }

            $o->setF_ini($oF_ini);
            $o->setF_fin($oF_fin);
            $o->setDia_ref($Qdia_ref === '' ? null : $Qdia_ref);
            $o->setDia_num($Qdia_num === 0 ? null : $Qdia_num);
            $o->setMas_menos($Qmas_menos);
            $o->setDia_inc($Qdia_inc);
            $o->setH_ini($oH_ini);
            $o->setH_fin($oH_fin);
            $o->setN_sacd($Qn_sacd);
            $o->setMes($Qmes);
            if ($this->encargoHorarioRepository->Guardar($o) === false) {
                return ['_error' => _('hay un error, no se ha guardado') . "\n" . $this->encargoHorarioRepository->getErrorTxt()];
            }

            return ['ok' => true];
        }

        return ['_error' => _('acción no válida')];
    }
}
