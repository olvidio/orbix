<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;

/**
 * Guarda/modifica las ausencias de un SACD
 * (`frontend/encargossacd/controller/sacd_ausencias_update.php`).
 *
 * Devuelve ['error' => bool, 'mensajes' => string] donde `mensajes`
 * acumula los errores de guardado/eliminacion para mostrar al usuario.
 */
final class SacdAusenciasUpdate
{
    /**
     * @param array<string, mixed> $data
     * @return array{error: bool, mensajes: string}
     */
    public static function execute(array $data): array
    {
        $enc_num = (int)($data['enc_num'] ?? 0);
        $id_nom = (int)($data['id_nom'] ?? 0);

        $a_inicio = is_array($data['inicio'] ?? null) ? $data['inicio'] : [];
        $a_fin = is_array($data['fin'] ?? null) ? $data['fin'] : [];
        $a_id_enc = is_array($data['id_enc'] ?? null) ? $data['id_enc'] : [];
        $a_id_item = is_array($data['id_item'] ?? null) ? $data['id_item'] : [];

        $mensajes = '';
        for ($i = 0; $i < $enc_num; $i++) {
            $f_ini = (string)($a_inicio[$i] ?? '');
            $f_fin = (string)($a_fin[$i] ?? '');
            if ($f_fin === '') {
                $f_fin = $f_ini;
            }
            $id_enc = (int)($a_id_enc[$i] ?? 0);
            $id_item = (int)($a_id_item[$i] ?? 0);

            if ($id_item === 0) {
                $mensajes .= self::insertar($id_enc, $id_nom, 2, $f_ini, $f_fin);
            } else {
                $mensajes .= self::modificar($id_item, $id_enc, $id_nom, $f_ini, $f_fin);
            }
        }

        return [
            'error' => $mensajes !== '',
            'mensajes' => $mensajes,
        ];
    }

    private static function modificar(int $id_item, int $id_enc, int $id_nom, string $f_ini, string $f_fin): string
    {
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);

        $mensajes = '';
        $oEncargoSacd = $EncargoSacdRepository->findById($id_item);
        if ($oEncargoSacd === null) {
            return _('no se ha encontrado el encargo del sacd') . "\n";
        }

        if ($f_ini === '' && $f_fin === '') {
            if ($EncargoSacdRepository->Eliminar($oEncargoSacd) === false) {
                $mensajes .= _('hay un error, no se ha eliminado') . "\n"
                    . $EncargoSacdRepository->getErrorTxt() . "\n";
            }
            return $mensajes;
        }

        $oEncargoSacd->setF_ini($f_ini);
        $oEncargoSacd->setF_fin($f_fin);
        if ($EncargoSacdRepository->Guardar($oEncargoSacd) === false) {
            $mensajes .= _('hay un error, no se ha guardado') . "\n"
                . $EncargoSacdRepository->getErrorTxt() . "\n";
        }

        $cHorario = $EncargoSacdHorarioRepository->getEncargoSacdHorarios([
            'id_enc' => $id_enc,
            'id_nom' => $id_nom,
            'id_item_tarea_sacd' => $id_item,
        ]) ?: [];
        foreach ($cHorario as $oHorario) {
            $oHorario->setF_ini($f_ini);
            $oHorario->setF_fin($f_fin);
            if ($EncargoSacdHorarioRepository->Guardar($oHorario) === false) {
                $mensajes .= _('hay un error, no se ha guardado') . "\n"
                    . $oHorario->getErrorTxt() . "\n";
            }
        }

        return $mensajes;
    }

    private static function insertar(int $id_enc, int $id_nom, int $modo, string $f_ini, string $f_fin): string
    {
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);

        $mensajes = '';

        $newId = $EncargoSacdRepository->getNewId();
        $oEncargoSacd = new EncargoSacd();
        $oEncargoSacd->setId_item($newId);
        $oEncargoSacd->setId_enc($id_enc);
        $oEncargoSacd->setId_nom($id_nom);
        $oEncargoSacd->setModo($modo);
        $oEncargoSacd->setF_ini($f_ini);
        $oEncargoSacd->setF_fin($f_fin);
        if ($EncargoSacdRepository->Guardar($oEncargoSacd) === false) {
            $mensajes .= _('hay un error, no se ha guardado') . "\n"
                . $EncargoSacdRepository->getErrorTxt() . "\n";
        }
        $id_item_tarea_sacd = (int)$oEncargoSacd->getId_item();

        $oHorario = $EncargoSacdHorarioRepository->findById($id_item_tarea_sacd);
        if ($oHorario === null) {
            $newIdH = $EncargoSacdHorarioRepository->getNewId();
            $oHorario = new EncargoSacdHorario();
            $oHorario->setId_item($newIdH);
        }
        $oHorario->setId_item_tarea_sacd($id_item_tarea_sacd);
        $oHorario->setId_enc($id_enc);
        $oHorario->setId_nom($id_nom);
        $oHorario->setF_ini($f_ini);
        $oHorario->setF_fin($f_fin);
        if ($EncargoSacdHorarioRepository->Guardar($oHorario) === false) {
            $mensajes .= _('hay un error, no se ha guardado') . "\n"
                . $EncargoSacdHorarioRepository->getErrorTxt() . "\n";
        }

        return $mensajes;
    }
}
