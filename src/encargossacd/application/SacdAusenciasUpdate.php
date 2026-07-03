<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Guarda/modifica las ausencias de un SACD
 * (`frontend/encargossacd/controller/sacd_ausencias_update.php`).
 */
final class SacdAusenciasUpdate
{

    public function __construct(
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return array{error: bool, mensajes: string}
     */
    public function execute(array $data): array
    {
        $enc_num = \src\shared\domain\helpers\FuncTablasSupport::inputInt($data, 'enc_num');
        $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($data, 'id_nom');

        $a_inicio = is_array($data['inicio'] ?? null) ? $data['inicio'] : [];
        $a_fin = is_array($data['fin'] ?? null) ? $data['fin'] : [];
        $a_id_enc = is_array($data['id_enc'] ?? null) ? $data['id_enc'] : [];
        $a_id_item = is_array($data['id_item'] ?? null) ? $data['id_item'] : [];

        $mensajes = '';
        for ($i = 0; $i < $enc_num; $i++) {
            $f_ini = $this->arrayStringAt($a_inicio, $i);
            $f_fin = $this->arrayStringAt($a_fin, $i);
            if ($f_fin === '') {
                $f_fin = $f_ini;
            }
            $id_enc = $this->arrayIntAt($a_id_enc, $i);
            $id_item = $this->arrayIntAt($a_id_item, $i);

            if ($id_item === 0) {
                $mensajes .= $this->insertar($id_enc, $id_nom, 2, $f_ini, $f_fin);
            } else {
                $mensajes .= $this->modificar($id_item, $id_enc, $id_nom, $f_ini, $f_fin);
            }
        }

        return [
            'error' => $mensajes !== '',
            'mensajes' => $mensajes,
        ];
    }

    private function modificar(int $id_item, int $id_enc, int $id_nom, string $f_ini, string $f_fin): string
    {
        $mensajes = '';
        $oEncargoSacd = $this->encargoSacdRepository->findById($id_item);
        if ($oEncargoSacd === null) {
            return _('no se ha encontrado el encargo del sacd') . "\n";
        }

        if ($f_ini === '' && $f_fin === '') {
            if ($this->encargoSacdRepository->Eliminar($oEncargoSacd) === false) {
                $mensajes .= _('hay un error, no se ha eliminado') . "\n"
                    . $this->encargoSacdRepository->getErrorTxt() . "\n";
            }

            return $mensajes;
        }

        $oEncargoSacd->setF_ini($this->toDate($f_ini));
        $oEncargoSacd->setF_fin($this->toDate($f_fin));
        if ($this->encargoSacdRepository->Guardar($oEncargoSacd) === false) {
            $mensajes .= _('hay un error, no se ha guardado') . "\n"
                . $this->encargoSacdRepository->getErrorTxt() . "\n";
        }

        $cHorario = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios([
            'id_enc' => $id_enc,
            'id_nom' => $id_nom,
            'id_item_tarea_sacd' => $id_item,
        ]);
        foreach ($cHorario as $oHorario) {
            $oHorario->setF_ini($this->toDate($f_ini));
            $oHorario->setF_fin($this->toDate($f_fin));
            if ($this->encargoSacdHorarioRepository->Guardar($oHorario) === false) {
                $mensajes .= _('hay un error, no se ha guardado') . "\n"
                    . $this->encargoSacdHorarioRepository->getErrorTxt() . "\n";
            }
        }

        return $mensajes;
    }

    private function insertar(int $id_enc, int $id_nom, int $modo, string $f_ini, string $f_fin): string
    {
        $mensajes = '';

        $newId = $this->encargoSacdRepository->getNewId();
        $oEncargoSacd = new EncargoSacd();
        $oEncargoSacd->setId_item($newId);
        $oEncargoSacd->setId_enc($id_enc);
        $oEncargoSacd->setId_nom($id_nom);
        $oEncargoSacd->setModo($modo);
        $oEncargoSacd->setF_ini($this->toDate($f_ini));
        $oEncargoSacd->setF_fin($this->toDate($f_fin));
        if ($this->encargoSacdRepository->Guardar($oEncargoSacd) === false) {
            $mensajes .= _('hay un error, no se ha guardado') . "\n"
                . $this->encargoSacdRepository->getErrorTxt() . "\n";
        }
        $id_item_tarea_sacd = (int) $oEncargoSacd->getId_item();

        $oHorario = $this->encargoSacdHorarioRepository->findById($id_item_tarea_sacd);
        if ($oHorario === null) {
            $newIdH = $this->encargoSacdHorarioRepository->getNewId();
            $oHorario = new EncargoSacdHorario();
            $oHorario->setId_item($newIdH);
        }
        $oHorario->setId_item_tarea_sacd($id_item_tarea_sacd);
        $oHorario->setId_enc($id_enc);
        $oHorario->setId_nom($id_nom);
        $oHorario->setF_ini($this->toDate($f_ini));
        $oHorario->setF_fin($this->toDate($f_fin));
        if ($this->encargoSacdHorarioRepository->Guardar($oHorario) === false) {
            $mensajes .= _('hay un error, no se ha guardado') . "\n"
                . $this->encargoSacdHorarioRepository->getErrorTxt() . "\n";
        }

        return $mensajes;
    }

    private function toDate(string $value): ?DateTimeLocal
    {
        return $value === '' ? null :  DateTimeLocal::createFromLocal($value);
    }

    /**
     * @param array<int|string, mixed> $values
     */
    private function arrayStringAt(array $values, int $index): string
    {
        if (!isset($values[$index]) || !is_scalar($values[$index])) {
            return '';
        }

        return (string) $values[$index];
    }

    /**
     * @param array<int|string, mixed> $values
     */
    private function arrayIntAt(array $values, int $index): int
    {
        if (!isset($values[$index]) || !is_numeric($values[$index])) {
            return 0;
        }

        return (int) $values[$index];
    }
}
