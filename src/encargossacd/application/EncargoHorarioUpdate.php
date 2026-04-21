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
    /**
     * @param array<string, mixed> $post
     * @return array{ok: true}|array{_error: string}
     */
    public static function ejecutar(array $post): array
    {
        $mod = (string)($post['mod'] ?? '');
        $repo = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);

        if ($mod === 'eliminar') {
            if (empty($post['sel_nom']) || !is_array($post['sel_nom'])) {
                return ['_error' => _('acción no válida')];
            }
            $token = (string)$post['sel_nom'][0];
            $parts = explode('#', $token, 2);
            $id_item_h = (int)$parts[0];
            $o = $repo->findById($id_item_h);
            if ($o !== null && $repo->Eliminar($o) === false) {
                return ['_error' => $repo->getErrorTxt()];
            }

            return ['ok' => true];
        }

        $Qdia = (string)($post['dia'] ?? '');
        $Qid_item_h = (int)($post['id_item_h'] ?? 0);
        $Qid_enc = (int)($post['id_enc'] ?? 0);
        $Qf_ini = (string)($post['f_ini'] ?? '');
        $Qf_fin = (string)($post['f_fin'] ?? '');
        $Qdia_ref = isset($post['dia_ref']) ? (string)$post['dia_ref'] : '';
        $Qdia_num = isset($post['dia_num']) && $post['dia_num'] !== '' ? (int)$post['dia_num'] : 0;
        $Qmas_menos = (string)($post['mas_menos'] ?? '');
        $Qdia_inc = isset($post['dia_inc']) && $post['dia_inc'] !== '' ? (int)$post['dia_inc'] : 0;
        $Qh_ini = (string)($post['h_ini'] ?? '');
        $Qh_fin = (string)($post['h_fin'] ?? '');
        $Qn_sacd = isset($post['n_sacd']) && $post['n_sacd'] !== '' ? (int)$post['n_sacd'] : 0;
        $Qmes = isset($post['mes']) && $post['mes'] !== '' ? (int)$post['mes'] : 0;

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

            $newId = $repo->getNewId();
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
            if ($repo->Guardar($o) === false) {
                return ['_error' => _('hay un error, no se ha guardado') . "\n" . $repo->getErrorTxt()];
            }

            return ['ok' => true];
        }

        if ($mod === 'editar') {
            if ($Qf_ini === '' || $Qdia === '') {
                return ['_error' => _('Debe llenar todos los campos que tengan un (*)')];
            }

            $o = $repo->findById($Qid_item_h);
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
            if ($repo->Guardar($o) === false) {
                return ['_error' => _('hay un error, no se ha guardado') . "\n" . $repo->getErrorTxt()];
            }

            return ['ok' => true];
        }

        return ['_error' => _('acción no válida')];
    }
}
