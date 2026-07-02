<?php

namespace src\encargossacd\application\services;

use src\encargossacd\db\DBPropuestas;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Aplica las propuestas staging a `encargos_sacd` / `encargo_sacd_horario` y elimina tablas staging.
 */
final class PropuestasAprobarService
{
    public function __construct(
        private PropuestaEncargoSacdRepositoryInterface $propuestaEncargoSacdRepository,
        private PropuestaEncargoSacdHorarioRepositoryInterface $propuestaHorarioRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
    ) {
    }

    public function execute(): string
    {
        $f_iso = date('Y-m-d');
        $f_fin = new DateTimeLocal($f_iso);

        $propuestas = $this->propuestaEncargoSacdRepository->getPropuestasEncargoSacd();
        foreach ($propuestas as $oPropuesta) {
            $id_nom = $oPropuesta->getId_nom();
            $id_nom_new = (int) ($oPropuesta->getId_nom_new() ?? 0);
            $id_item = $oPropuesta->getId_item();

            if ($id_nom_new <= 0 && $id_nom > 0) {
                $this->finEncargo($id_item, $f_fin);
                continue;
            }
            if ($id_nom <= 0 && $id_nom_new > 0) {
                $this->newEncargo($oPropuesta, $f_fin);
                continue;
            }
            if ($id_nom > 0 && $id_nom_new > 0) {
                if ($id_nom === $id_nom_new) {
                    $this->comprobarHorario($id_item, $f_fin);
                } else {
                    $this->finEncargo($id_item, $f_fin);
                    $this->newEncargo($oPropuesta, $f_fin);
                }
            }
        }

        (new DBPropuestas())->eliminarAll();

        return _('Hecho!');
    }

    private function comprobarHorario(int $id_item, DateTimeLocal $f_ini): void
    {
        $cPropuestaHorarios = $this->propuestaHorarioRepository->getEncargoSacdHorarios(
            ['id_item_tarea_sacd' => $id_item, 'id_nom' => 'x'],
            ['id_nom' => 'IS NOT NULL'],
        );
        foreach ($cPropuestaHorarios as $oPropuestaHorario) {
            $dia_ref = (string) ($oPropuestaHorario->getDia_ref() ?? '');
            $dia_inc = (int) ($oPropuestaHorario->getDia_inc() ?? 0);
            $cActualHorario = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios(
                [
                    'id_item_tarea_sacd' => $id_item,
                    'dia_ref' => $dia_ref,
                    'id_nom' => 'x',
                    'f_fin' => 'x',
                ],
                ['id_nom' => 'IS NOT NULL', 'f_fin' => 'IS NULL'],
            );
            if ($cActualHorario !== []) {
                $oEncargoSacdHorario = $cActualHorario[0];
                $dia_inc_actual = (int) ($oEncargoSacdHorario->getDia_inc() ?? 0);
                if ($dia_inc !== $dia_inc_actual) {
                    $oEncargoSacdHorario->setDia_inc($dia_inc);
                    $this->encargoSacdHorarioRepository->Guardar($oEncargoSacdHorario);
                }
            } else {
                $oNewHorario = new EncargoSacdHorario();
                $oNewHorario->setId_item($this->encargoSacdHorarioRepository->getNewId());
                $oNewHorario->setId_enc($oPropuestaHorario->getId_enc());
                $oNewHorario->setId_nom($oPropuestaHorario->getId_nom());
                $oNewHorario->setF_ini($f_ini);
                $oNewHorario->setDia_ref($dia_ref);
                $oNewHorario->setDia_inc($dia_inc);
                $oNewHorario->setId_item_tarea_sacd($id_item);
                $this->encargoSacdHorarioRepository->Guardar($oNewHorario);
            }
        }
    }

    private function newEncargo(PropuestaEncargoSacd $oPropuesta, DateTimeLocal $f_ini): void
    {
        $id_nom_new = (int) ($oPropuesta->getId_nom_new() ?? 0);
        $id_item = $oPropuesta->getId_item();
        $id_enc = $oPropuesta->getId_enc();

        $oEncargoSacd = new EncargoSacd();
        $oEncargoSacd->setId_item($this->encargoSacdRepository->getNewId());
        $oEncargoSacd->setId_enc($id_enc);
        $oEncargoSacd->setId_nom($id_nom_new);
        $oEncargoSacd->setModoVo($oPropuesta->getModoVo());
        $oEncargoSacd->setF_ini($f_ini);
        $oEncargoSacd->setF_fin(null);
        $this->encargoSacdRepository->Guardar($oEncargoSacd);
        $id_item_new = $oEncargoSacd->getId_item();

        $this->newHorario($id_item, $id_enc, $id_nom_new, $f_ini, $id_item_new);
    }

    private function newHorario(int $id_item, int $id_enc, int $id_nom_new, DateTimeLocal $f_ini, int $id_item_new): void
    {
        $cPropuestaHorarios = $this->propuestaHorarioRepository->getEncargoSacdHorarios(
            ['id_item_tarea_sacd' => $id_item, 'id_nom' => 'x'],
            ['id_nom' => 'IS NOT NULL'],
        );
        foreach ($cPropuestaHorarios as $oPropuestaHorario) {
            $dia_ref = (string) ($oPropuestaHorario->getDia_ref() ?? '');
            $dia_inc = (int) ($oPropuestaHorario->getDia_inc() ?? 0);
            $oNewHorario = new EncargoSacdHorario();
            $oNewHorario->setId_item($this->encargoSacdHorarioRepository->getNewId());
            $oNewHorario->setId_enc($id_enc);
            $oNewHorario->setId_nom($id_nom_new);
            $oNewHorario->setF_ini($f_ini);
            $oNewHorario->setDia_ref($dia_ref);
            $oNewHorario->setDia_inc($dia_inc);
            $oNewHorario->setId_item_tarea_sacd($id_item_new);
            $this->encargoSacdHorarioRepository->Guardar($oNewHorario);
        }
    }

    private function finEncargo(int $id_item, DateTimeLocal $f_fin): void
    {
        $oEncargoSacd = $this->encargoSacdRepository->findById($id_item);
        if ($oEncargoSacd === null) {
            return;
        }
        $oEncargoSacd->setF_fin($f_fin);
        $this->encargoSacdRepository->Guardar($oEncargoSacd);
        $this->finHorarioPropuesta($id_item, $f_fin);
    }

    private function finHorarioPropuesta(int $id_item, DateTimeLocal $f_fin): void
    {
        $cPropuestaHorarios = $this->propuestaHorarioRepository->getEncargoSacdHorarios(
            ['id_item_tarea_sacd' => $id_item, 'id_nom' => 'x'],
            ['id_nom' => 'IS NOT NULL'],
        );
        foreach ($cPropuestaHorarios as $oPropuestaHorario) {
            $oPropuestaHorario->setF_fin($f_fin);
            $this->propuestaHorarioRepository->Guardar($oPropuestaHorario);
        }
    }
}
