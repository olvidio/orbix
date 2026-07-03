<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Alta/edición/baja de horario de encargo sacd (`encargo_sacd_horario`).
 */
final class EncargoSacdHorarioUpdate
{

    public function __construct(
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository
    ) {
    }

    /**
     * @param array<string, mixed> $post
     * @return array{ok: true}|array{_error: string}
     */
    public function ejecutar(array $post): array
    {
        $mod = FuncTablasSupport::inputString($post, 'mod');
        if ($mod === 'eliminar') {
            $id_item = FuncTablasSupport::inputInt($post, 'id_item');
            if ($id_item <= 0) {
                return ['_error' => _('acción no válida')];
            }
            $o = $this->encargoSacdHorarioRepository->findById($id_item);
            if ($o === null) {
                return ['_error' => _('registro no encontrado')];
            }
            if ($this->encargoSacdHorarioRepository->eliminarExcepcionesPorHorarioId($id_item) === false) {
                return ['_error' => $this->encargoSacdHorarioRepository->getErrorTxt()];
            }
            if ($this->encargoSacdHorarioRepository->Eliminar($o) === false) {
                return ['_error' => $this->encargoSacdHorarioRepository->getErrorTxt()];
            }

            return ['ok' => true];
        }

        $Qid_nom = FuncTablasSupport::inputInt($post, 'id_nom');
        $Qid_enc = FuncTablasSupport::inputInt($post, 'id_enc');
        $Qid_item = FuncTablasSupport::inputInt($post, 'id_item');
        $Qdia = FuncTablasSupport::inputString($post, 'dia');
        $Qf_ini = FuncTablasSupport::inputString($post, 'f_ini');
        $Qf_fin = FuncTablasSupport::inputString($post, 'f_fin');
        $Qdia_ref = FuncTablasSupport::inputString($post, 'dia_ref');
        $Qdia_num = FuncTablasSupport::inputInt($post, 'dia_num');
        $Qmas_menos = FuncTablasSupport::inputString($post, 'mas_menos');
        $Qdia_inc = FuncTablasSupport::inputInt($post, 'dia_inc');
        $Qh_ini = FuncTablasSupport::inputString($post, 'h_ini');
        $Qh_fin = FuncTablasSupport::inputString($post, 'h_fin');

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

            $newId = $this->encargoSacdHorarioRepository->getNewId();
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
            if ($this->encargoSacdHorarioRepository->Guardar($o) === false) {
                return ['_error' => _('hay un error, no se ha guardado') . "\n" . $this->encargoSacdHorarioRepository->getErrorTxt()];
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

            $o = $this->encargoSacdHorarioRepository->findById($Qid_item);
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

            if ($this->encargoSacdHorarioRepository->Guardar($o) === false) {
                return ['_error' => _('hay un error, no se ha guardado') . "\n" . $this->encargoSacdHorarioRepository->getErrorTxt()];
            }

            return ['ok' => true];
        }

        return ['_error' => _('acción no válida')];
    }
}
