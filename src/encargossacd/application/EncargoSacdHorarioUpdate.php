<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Alta/edición/baja de horario de encargo sacd (`encargo_sacd_horario`).
 */
final class EncargoSacdHorarioUpdate
{
    /**
     * @param array<string, mixed> $post
     * @return array{ok: true}|array{_error: string}
     */
    public static function ejecutar(array $post): array
    {
        $mod = trim((string)($post['mod'] ?? ''));
        $repo = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);

        if ($mod === 'eliminar') {
            $id_item = (int)($post['id_item'] ?? 0);
            if ($id_item <= 0) {
                return ['_error' => _('acción no válida')];
            }
            $o = $repo->findById($id_item);
            if ($o === null) {
                return ['_error' => _('registro no encontrado')];
            }
            if ($repo->eliminarExcepcionesPorHorarioId($id_item) === false) {
                return ['_error' => $repo->getErrorTxt()];
            }
            if ($repo->Eliminar($o) === false) {
                return ['_error' => $repo->getErrorTxt()];
            }

            return ['ok' => true];
        }

        $Qid_nom = (int)($post['id_nom'] ?? 0);
        $Qid_enc = (int)($post['id_enc'] ?? 0);
        $Qid_item = (int)($post['id_item'] ?? 0);
        $Qdia = (string)($post['dia'] ?? '');
        $Qf_ini = (string)($post['f_ini'] ?? '');
        $Qf_fin = (string)($post['f_fin'] ?? '');
        $Qdia_ref = isset($post['dia_ref']) ? (string)$post['dia_ref'] : '';
        $Qdia_num = isset($post['dia_num']) && $post['dia_num'] !== '' ? (int)$post['dia_num'] : 0;
        $Qmas_menos = (string)($post['mas_menos'] ?? '');
        $Qdia_inc = isset($post['dia_inc']) && $post['dia_inc'] !== '' ? (int)$post['dia_inc'] : 0;
        $Qh_ini = (string)($post['h_ini'] ?? '');
        $Qh_fin = (string)($post['h_fin'] ?? '');

        $oF_ini = $Qf_ini === '' ? null : new DateTimeLocal($Qf_ini);
        $oF_fin = $Qf_fin === '' ? null : new DateTimeLocal($Qf_fin);
        $oH_ini = $Qh_ini === '' ? null : TimeLocal::fromString($Qh_ini);
        $oH_fin = $Qh_fin === '' ? null : TimeLocal::fromString($Qh_fin);

        if ($Qmas_menos === '' || $Qmas_menos === '0') {
            $Qdia_ref = $Qdia;
        }

        if ($mod === '1' || $mod === '3') {
            if ($Qf_ini === '' || $Qdia === '') {
                return ['_error' => _('Debe llenar todos los campos que tengan un (*)')];
            }

            $newId = $repo->getNewId();
            $o = new EncargoSacdHorario();
            $o->setId_item((int)$newId);
            $o->setId_enc($Qid_enc);
            $o->setId_nom($Qid_nom);
            $o->setF_ini($oF_ini);
            $o->setF_fin($oF_fin);
            $o->setDia_ref($Qdia_ref === '' ? null : $Qdia_ref);
            $o->setDia_num($Qdia_num === 0 ? null : $Qdia_num);
            $o->setMas_menos($Qmas_menos === '' || $Qmas_menos === '0' ? null : $Qmas_menos);
            $o->setDia_inc($Qdia_inc);
            $o->setH_ini($oH_ini);
            $o->setH_fin($oH_fin);
            if ($repo->Guardar($o) === false) {
                return ['_error' => _('hay un error, no se ha guardado') . "\n" . $repo->getErrorTxt()];
            }

            return ['ok' => true];
        }

        if ($mod === '2') {
            if ($Qf_ini === '' || $Qdia === '') {
                return ['_error' => _('Debe llenar todos los campos que tengan un (*)')];
            }
            if ($Qid_item <= 0) {
                return ['_error' => _('acción no válida')];
            }

            $o = $repo->findById($Qid_item);
            if ($o === null) {
                return ['_error' => _('registro no encontrado')];
            }

            $o->setF_ini($oF_ini);
            $o->setF_fin($oF_fin);
            $o->setDia_ref($Qdia_ref === '' ? null : $Qdia_ref);
            $o->setDia_num($Qdia_num === 0 ? null : $Qdia_num);
            $o->setMas_menos($Qmas_menos === '' || $Qmas_menos === '0' ? null : $Qmas_menos);
            $o->setDia_inc($Qdia_inc);
            $o->setH_ini($oH_ini);
            $o->setH_fin($oH_fin);

            if ($repo->Guardar($o) === false) {
                return ['_error' => _('hay un error, no se ha guardado') . "\n" . $repo->getErrorTxt()];
            }

            return ['ok' => true];
        }

        return ['_error' => _('acción no válida')];
    }
}
