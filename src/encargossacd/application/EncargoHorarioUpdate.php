<?php

namespace src\encargossacd\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

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
        $mod = input_string($post, 'mod');
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

        $Qdia = input_string($post, 'dia');
        $Qid_item_h = input_int($post, 'id_item_h');
        $Qid_enc = input_int($post, 'id_enc');
        $Qf_ini = input_string($post, 'f_ini');
        $Qf_fin = input_string($post, 'f_fin');
        $Qdia_ref = input_string($post, 'dia_ref');
        $Qdia_num = input_int($post, 'dia_num');
        $Qmas_menos = input_string($post, 'mas_menos');
        $Qdia_inc = input_int($post, 'dia_inc');
        $Qh_ini = input_string($post, 'h_ini');
        $Qh_fin = input_string($post, 'h_fin');
        $Qn_sacd = input_int($post, 'n_sacd');
        $Qmes = input_int($post, 'mes');

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
