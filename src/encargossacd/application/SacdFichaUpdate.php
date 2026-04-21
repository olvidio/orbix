<?php

namespace src\encargossacd\application;

use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdObserv;

/**
 * Mutacion de la ficha de encargos de un SACD
 * (`sacd_ficha_ajax?que=update`).
 *
 * Porta la logica del antiguo controlador frontend, haciendo la misma
 * actualizacion de dedicaciones por modulo y de observaciones.
 */
final class SacdFichaUpdate
{
    /**
     * @param array<string, mixed> $post
     * @return array{error: string, mensajes: string}
     */
    public static function execute(array $post): array
    {
        $id_nom = (int)($post['id_nom'] ?? 0);
        $enc_num = (int)($post['enc_num'] ?? 0);
        $observ = (string)($post['observ'] ?? '');

        $aId_tipo_enc = is_array($post['id_tipo_enc'] ?? null) ? $post['id_tipo_enc'] : [];
        $aId_enc = is_array($post['id_enc'] ?? null) ? $post['id_enc'] : [];
        $aDedic_m = is_array($post['dedic_m'] ?? null) ? $post['dedic_m'] : [];
        $aDedic_t = is_array($post['dedic_t'] ?? null) ? $post['dedic_t'] : [];
        $aDedic_v = is_array($post['dedic_v'] ?? null) ? $post['dedic_v'] : [];

        if ($id_nom <= 0) {
            return ['error' => _("id_nom no valido"), 'mensajes' => ''];
        }

        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $EncargoSacdObservRepository = $GLOBALS['container']->get(EncargoSacdObservRepositoryInterface::class);
        $oAplicacion = new EncargoAplicacionService();

        $mensajes = '';
        for ($i = 1; $i <= $enc_num; $i++) {
            $id_tipo_enc = (int)($aId_tipo_enc[$i] ?? 0);
            $id_enc = (int)($aId_enc[$i] ?? 0);
            $dedic_m = (string)($aDedic_m[$i] ?? '');
            $dedic_t = (string)($aDedic_t[$i] ?? '');
            $dedic_v = (string)($aDedic_v[$i] ?? '');

            if ($id_tipo_enc === 5020 || $id_tipo_enc === 5030 || $id_tipo_enc === 6000) {
                if ($id_enc === 0) {
                    $cEncargos = $EncargoRepository->getEncargos(['id_tipo_enc' => $id_tipo_enc]);
                    if (is_array($cEncargos) && empty($cEncargos)) {
                        $desc_enc = match ($id_tipo_enc) {
                            5020 => 'estudio',
                            5030 => 'descanso',
                            6000 => 'otros',
                            default => '',
                        };
                        $id_enc = (int)$oAplicacion->crear_encargo($id_tipo_enc, 1, '', '', $desc_enc, '', '', '');
                    } elseif (is_array($cEncargos) && !empty($cEncargos)) {
                        $id_enc = (int)$cEncargos[0]->getId_enc();
                    }
                }
                if ($dedic_m === '' && $dedic_t === '' && $dedic_v === '') {
                    $oAplicacion->delete_sacd($id_enc, $id_nom, 2);
                } else {
                    $oAplicacion->insert_sacd($id_enc, $id_nom, 2);
                }
            }

            if ($id_enc <= 0) {
                continue;
            }

            $aWhere = [
                'id_nom' => $id_nom,
                'id_enc' => $id_enc,
                'modo' => '(2|3|5)',
                'f_fin' => 'x',
            ];
            $aOperador = [
                'f_fin' => 'IS NULL',
                'modo' => '~',
            ];
            $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd($aWhere, $aOperador);
            if (empty($cEncargosSacd)) {
                continue;
            }
            if (count($cEncargosSacd) > 1) {
                $mensajes .= _("Error con las tareas") . "\n";
            }

            $id_item_t_sacd = null;
            foreach ($cEncargosSacd as $oEncargoSacd) {
                $id_item_t_sacd = $oEncargoSacd->getId_item();
            }

            $oAplicacion->modificar_horario_sacd($id_item_t_sacd, $id_enc, $id_nom, 'm', $dedic_m);
            $oAplicacion->modificar_horario_sacd($id_item_t_sacd, $id_enc, $id_nom, 't', $dedic_t);
            $oAplicacion->modificar_horario_sacd($id_item_t_sacd, $id_enc, $id_nom, 'v', $dedic_v);
        }

        $cEncargoSacdObserv = $EncargoSacdObservRepository->getEncargoSacdObservs(['id_nom' => $id_nom]);
        $oEncargoSacdObserv = is_array($cEncargoSacdObserv) && !empty($cEncargoSacdObserv)
            ? $cEncargoSacdObserv[0]
            : null;

        if ($oEncargoSacdObserv !== null) {
            if ($observ === '') {
                if ($EncargoSacdObservRepository->Eliminar($oEncargoSacdObserv) === false) {
                    $mensajes .= _("hay un error, no se ha eliminado") . "\n";
                }
            } else {
                $oEncargoSacdObserv->setObserv($observ);
                if ($EncargoSacdObservRepository->Guardar($oEncargoSacdObserv) === false) {
                    $mensajes .= _("hay un error, no se ha guardado") . "\n";
                }
            }
        } elseif ($observ !== '') {
            $newId = $EncargoSacdObservRepository->getNewId();
            $oEncargoSacdObserv = new EncargoSacdObserv();
            $oEncargoSacdObserv->setId_item($newId);
            $oEncargoSacdObserv->setId_nom($id_nom);
            $oEncargoSacdObserv->setObserv($observ);
            if ($EncargoSacdObservRepository->Guardar($oEncargoSacdObserv) === false) {
                $mensajes .= _("hay un error, no se ha guardado") . "\n";
                $mensajes .= $EncargoSacdObservRepository->getErrorTxt();
            }
        }

        return ['error' => '', 'mensajes' => $mensajes];
    }
}
