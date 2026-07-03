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

    public function __construct(
        private EncargoAplicacionService $aplicacionService,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdObservRepositoryInterface $encargoSacdObservRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository
    ) {
    }

    /**
     * @param array<string, mixed> $post
     * @return array{error: string, mensajes: string}
     */
    public function execute(array $post): array
    {
        $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_nom');
        $enc_num = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'enc_num');
        $observ = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'observ');

        $aId_tipo_enc = is_array($post['id_tipo_enc'] ?? null) ? $post['id_tipo_enc'] : [];
        $aId_enc = is_array($post['id_enc'] ?? null) ? $post['id_enc'] : [];
        $aDedic_m = is_array($post['dedic_m'] ?? null) ? $post['dedic_m'] : [];
        $aDedic_t = is_array($post['dedic_t'] ?? null) ? $post['dedic_t'] : [];
        $aDedic_v = is_array($post['dedic_v'] ?? null) ? $post['dedic_v'] : [];

        if ($id_nom <= 0) {
            return ['error' => _("id_nom no valido"), 'mensajes' => ''];
        }

        $oAplicacion = $this->aplicacionService;

        $mensajes = '';
        for ($i = 0; $i < $enc_num; $i++) {
            $id_tipo_enc = $this->arrayIntAt($aId_tipo_enc, $i);
            $id_enc = $this->arrayIntAt($aId_enc, $i);
            $dedic_m = $this->arrayStringAt($aDedic_m, $i);
            $dedic_t = $this->arrayStringAt($aDedic_t, $i);
            $dedic_v = $this->arrayStringAt($aDedic_v, $i);

            if (in_array($id_tipo_enc, [5020, 5030, 6000], true)) {
                if ($id_enc === 0) {
                    $cEncargos = $this->encargoRepository->getEncargos(['id_tipo_enc' => $id_tipo_enc]);
                    if ($cEncargos === []) {
                        $desc_enc = match ($id_tipo_enc) {
                            5020 => 'estudio',
                            5030 => 'descanso',
                            default => 'otros',
                        };
                        $id_enc = $oAplicacion->crear_encargo($id_tipo_enc, 1, 0, 0, $desc_enc, '', '', '');
                    } elseif ($cEncargos !== []) {
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
            $cEncargosSacd = $this->encargoSacdRepository->getEncargosSacd($aWhere, $aOperador);
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

        $cEncargoSacdObserv = $this->encargoSacdObservRepository->getEncargosSacdObservs(['id_nom' => $id_nom]);
        $oEncargoSacdObserv = $cEncargoSacdObserv[0] ?? null;

        if ($oEncargoSacdObserv !== null) {
            if ($observ === '') {
                if ($this->encargoSacdObservRepository->Eliminar($oEncargoSacdObserv) === false) {
                    $mensajes .= _("hay un error, no se ha eliminado") . "\n";
                }
            } else {
                $oEncargoSacdObserv->setObserv($observ);
                if ($this->encargoSacdObservRepository->Guardar($oEncargoSacdObserv) === false) {
                    $mensajes .= _("hay un error, no se ha guardado") . "\n";
                }
            }
        } elseif ($observ !== '') {
            $newId = $this->encargoSacdObservRepository->getNewId();
            $oEncargoSacdObserv = new EncargoSacdObserv();
            $oEncargoSacdObserv->setId_item($newId);
            $oEncargoSacdObserv->setId_nom($id_nom);
            $oEncargoSacdObserv->setObserv($observ);
            if ($this->encargoSacdObservRepository->Guardar($oEncargoSacdObserv) === false) {
                $mensajes .= _("hay un error, no se ha guardado") . "\n";
                $mensajes .= $this->encargoSacdObservRepository->getErrorTxt();
            }
        }

        return ['error' => '', 'mensajes' => $mensajes];
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
