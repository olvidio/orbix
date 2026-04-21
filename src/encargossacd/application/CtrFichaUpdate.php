<?php

namespace src\encargossacd\application;

use src\encargossacd\application\traits\EncargoFunciones;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Mutacion de la ficha de atencion sacerdotal de un centro.
 *
 * Puerto de `frontend/encargossacd/controller/ctr_ficha_update.php`.
 *
 * Devuelve siempre `['error' => string]` (vacio = exito). El controlador HTTP
 * convierte ese resultado en JSON `{success, mensaje}` (el proxy legacy en
 * `frontend/` preserva el contrato "alert(rta_txt)" reemitiendo `mensaje`).
 */
final class CtrFichaUpdate
{
    /**
     * @param array<string, mixed> $post
     * @return array{error: string}
     */
    public static function execute(array $post): array
    {
        $e = (int)($post['e'] ?? 0);
        $mod = (string)($post["mod_$e"] ?? '');
        $id_enc = (int)($post["id_enc_$e"] ?? 0);
        $sacd_num = (int)($post['sacd_num'] ?? 0);
        $id_ubi = (int)($post["id_ubi_$e"] ?? 0);
        $tipo_centro = (string)($post["tipo_centro_$e"] ?? '');

        $n_sacd = (int)($post['n_sacd'] ?? 0);
        $n_sacd = $n_sacd === 0 ? 1 : $n_sacd;

        $id_sacd_titular = (int)($post['id_sacd_titular'] ?? 0);
        $id_sacd_suplente = (int)($post['id_sacd_suplente'] ?? 0);
        $observ = (string)($post['observ'] ?? '');
        $cl = !empty($post['cl']);
        $num_alum = (int)($post['num_alum'] ?? 0);
        $dedic_ctr_m = (string)($post['dedic_ctr_m'] ?? '');
        $dedic_ctr_t = (string)($post['dedic_ctr_t'] ?? '');
        $dedic_ctr_v = (string)($post['dedic_ctr_v'] ?? '');

        $Aid_sacd = is_array($post['id_sacd'] ?? null) ? $post['id_sacd'] : [];
        $Adedic_m = is_array($post['dedic_m'] ?? null) ? $post['dedic_m'] : [];
        $Adedic_t = is_array($post['dedic_t'] ?? null) ? $post['dedic_t'] : [];
        $Adedic_v = is_array($post['dedic_v'] ?? null) ? $post['dedic_v'] : [];

        $oF_fin = new DateTimeLocal();
        $oEncargoFunciones = new EncargoFunciones();

        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);

        switch ($mod) {
            case 'nuevo':
                return self::nuevo(
                    $oEncargoFunciones,
                    $EncargoRepository,
                    $EncargoTipoRepository,
                    $tipo_centro,
                    $id_ubi,
                    $id_sacd_titular,
                    $id_sacd_suplente,
                    $observ,
                    $cl,
                    $sacd_num,
                    $n_sacd,
                    $dedic_ctr_m,
                    $dedic_ctr_t,
                    $dedic_ctr_v,
                    $Aid_sacd,
                    $Adedic_m,
                    $Adedic_t,
                    $Adedic_v,
                    $num_alum,
                );
            case 'editar':
                return self::editar(
                    $oEncargoFunciones,
                    $EncargoRepository,
                    $tipo_centro,
                    $id_enc,
                    $id_sacd_titular,
                    $id_sacd_suplente,
                    $observ,
                    $cl,
                    $sacd_num,
                    $n_sacd,
                    $dedic_ctr_m,
                    $dedic_ctr_t,
                    $dedic_ctr_v,
                    $Aid_sacd,
                    $Adedic_m,
                    $Adedic_t,
                    $Adedic_v,
                    $oF_fin,
                );
            default:
                return ['error' => ''];
        }
    }

    /**
     * @param array<int, mixed> $Aid_sacd
     * @param array<int, mixed> $Adedic_m
     * @param array<int, mixed> $Adedic_t
     * @param array<int, mixed> $Adedic_v
     * @return array{error: string}
     */
    private static function nuevo(
        EncargoFunciones $oEncargoFunciones,
        EncargoRepositoryInterface $EncargoRepository,
        EncargoTipoRepositoryInterface $EncargoTipoRepository,
        string $tipo_centro,
        int $id_ubi,
        int $id_sacd_titular,
        int $id_sacd_suplente,
        string $observ,
        bool $cl,
        int $sacd_num,
        int $n_sacd,
        string $dedic_ctr_m,
        string $dedic_ctr_t,
        string $dedic_ctr_v,
        array $Aid_sacd,
        array $Adedic_m,
        array $Adedic_t,
        array $Adedic_v,
        int $num_alum,
    ): array {
        if ($tipo_centro !== 'of' && $id_sacd_titular === 0) {
            return ['error' => _('Debe nombrar un sacerdote tirular') . '<br>'];
        }

        $id_ubi_txt = (string)$id_ubi;
        $first = (int)($id_ubi_txt[0] ?? 0);
        $sf_sv = 0;
        $nombre_ubi = '';
        $tipo_ctr = '';
        $id_tipo_enc = 0;
        if ($first === 2) {
            $sf_sv = 2;
            $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
            $oCentroSf = $CentroEllasRepository->findById($id_ubi);
            if ($oCentroSf !== null) {
                $nombre_ubi = (string)$oCentroSf->getNombre_ubi();
                $tipo_ctr = (string)$oCentroSf->getTipo_ctr();
            }
            $id_tipo_enc = match ($tipo_ctr) {
                'cgioc', 'oc' => 2200,
                default => 1200,
            };
        } elseif ($first === 1) {
            $sf_sv = 1;
            $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            $oCentroDl = $CentroDlRepository->findById($id_ubi);
            if ($oCentroDl !== null) {
                $nombre_ubi = (string)$oCentroDl->getNombre_ubi();
                $tipo_ctr = (string)$oCentroDl->getTipo_ctr();
            }
            $id_tipo_enc = match ($tipo_ctr) {
                'cgioc', 'oc' => 2100,
                'igloc' => 3000,
                'ss' => 1300,
                default => 1100,
            };
        }

        $oEncargoTipo = $EncargoTipoRepository->findById($id_tipo_enc);
        $tipo_enc = $oEncargoTipo !== null ? (string)$oEncargoTipo->getTipo_enc() : '';
        $desc_enc = $tipo_enc . " ($nombre_ubi)";

        $newId = $EncargoRepository->getNewId();
        $oEncargo = new Encargo();
        $oEncargo->setId_enc($newId);
        $oEncargo->setId_tipo_enc($id_tipo_enc);
        $oEncargo->setGrupoEncargoVo(EncargoGrupo::fromNullableInt($sf_sv));
        $oEncargo->setId_ubi($id_ubi);
        $oEncargo->setDesc_enc($desc_enc);
        $oEncargo->setObserv($observ);
        if ($EncargoRepository->Guardar($oEncargo) === false) {
            return ['error' => _('hay un error, no se ha guardado') . "\n" . $EncargoRepository->getErrorTxt()];
        }

        $id_enc = (int)$oEncargo->getId_enc();

        if ($dedic_ctr_m !== '') {
            $oEncargoFunciones->insert_horario_ctr($id_enc, 'm', $dedic_ctr_m, $n_sacd);
        }
        if ($dedic_ctr_t !== '') {
            $oEncargoFunciones->insert_horario_ctr($id_enc, 't', $dedic_ctr_t, $n_sacd);
        }
        if ($dedic_ctr_v !== '') {
            $oEncargoFunciones->insert_horario_ctr($id_enc, 'v', $dedic_ctr_v, $n_sacd);
        }

        for ($i = 0; $i < $sacd_num; $i++) {
            if ($i > 0) {
                $modo = 5;
            } else {
                $modo = $cl ? 2 : 3;
                $Aid_sacd[0] = $id_sacd_titular;
            }
            $oEncargoSacd = $oEncargoFunciones->insert_sacd($id_enc, (int)($Aid_sacd[$i] ?? 0), $modo);
            $id_item_t_sacd = (int)$oEncargoSacd->getId_item();

            $this_id_sacd = (int)($Aid_sacd[$i] ?? 0);
            if (!empty($Adedic_m[$i])) {
                $oEncargoFunciones->insert_horario_sacd($id_item_t_sacd, $id_enc, $this_id_sacd, 'm', $Adedic_m[$i]);
            }
            if (!empty($Adedic_t[$i])) {
                $oEncargoFunciones->insert_horario_sacd($id_item_t_sacd, $id_enc, $this_id_sacd, 't', $Adedic_t[$i]);
            }
            if (!empty($Adedic_v[$i])) {
                $oEncargoFunciones->insert_horario_sacd($id_item_t_sacd, $id_enc, $this_id_sacd, 'v', $Adedic_v[$i]);
            }
        }

        if ($id_sacd_suplente !== 0) {
            $oEncargoFunciones->insert_sacd($id_enc, $id_sacd_suplente, 4);
        }
        if (str_contains($tipo_centro, 'cgi')) {
            $oEncargoFunciones->grabar_alumnos($id_ubi, $num_alum);
        }

        return ['error' => ''];
    }

    /**
     * @param array<int, mixed> $Aid_sacd
     * @param array<int, mixed> $Adedic_m
     * @param array<int, mixed> $Adedic_t
     * @param array<int, mixed> $Adedic_v
     * @return array{error: string}
     */
    private static function editar(
        EncargoFunciones $oEncargoFunciones,
        EncargoRepositoryInterface $EncargoRepository,
        string $tipo_centro,
        int $id_enc,
        int $id_sacd_titular,
        int $id_sacd_suplente,
        string $observ,
        bool $cl,
        int $sacd_num,
        int $n_sacd,
        string $dedic_ctr_m,
        string $dedic_ctr_t,
        string $dedic_ctr_v,
        array $Aid_sacd,
        array $Adedic_m,
        array $Adedic_t,
        array $Adedic_v,
        DateTimeLocal $oF_fin,
    ): array {
        $oF_ini = new DateTimeLocal();
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);

        if ($tipo_centro !== 'of') {
            if ($id_sacd_titular === 0) {
                if ($id_enc !== 0) {
                    $oEncargo = $EncargoRepository->findById($id_enc);
                    if ($oEncargo !== null) {
                        $EncargoRepository->Eliminar($oEncargo);
                    }

                    return ['error' => ''];
                }

                return ['error' => _('Debe nombrar un sacerdote titular') . "\n"];
            }

            if ($id_sacd_titular === $id_sacd_suplente) {
                return ['error' => _('El sacd titular y suplente deben ser distintos')];
            }

            $oEncargo = $EncargoRepository->findById($id_enc);
            if ($oEncargo !== null) {
                $oEncargo->setObserv($observ);
                if ($EncargoRepository->Guardar($oEncargo) === false) {
                    return ['error' => _('hay un error, no se ha guardado') . "\n" . $EncargoRepository->getErrorTxt()];
                }
            }

            $oEncargoFunciones->modificar_horario_ctr($id_enc, 'm', $dedic_ctr_m, $n_sacd);
            $oEncargoFunciones->modificar_horario_ctr($id_enc, 't', $dedic_ctr_t, $n_sacd);
            $oEncargoFunciones->modificar_horario_ctr($id_enc, 'v', $dedic_ctr_v, $n_sacd);
        }

        $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd(
            ['id_enc' => $id_enc, 'modo' => '5', 'f_fin' => 'x'],
            ['f_fin' => 'IS NULL'],
        );
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $oEncargoSacd->setF_fin($oF_fin);
            if ($EncargoSacdRepository->Guardar($oEncargoSacd) === false) {
                return ['error' => _('hay un error, no se ha guardado') . "\n" . $EncargoSacdRepository->getErrorTxt()];
            }
            $id_nom = (int)$oEncargoSacd->getId_nom();
            $oEncargoFunciones->finalizar_horario_sacd($id_enc, $id_nom, $oF_fin);
        }

        for ($i = 0; $i < $sacd_num; $i++) {
            if ($i > 0 || $tipo_centro === 'of') {
                $sacd_id = (int)($Aid_sacd[$i] ?? 0);
                if ($sacd_id !== 0) {
                    $oEncargoFunciones->insert_sacd($id_enc, $sacd_id, 5);
                }
            } else {
                $cTitular = $EncargoSacdRepository->getEncargosSacd(
                    [
                        'id_enc' => $id_enc,
                        'modo' => '(2|3)',
                        'f_fin' => 'x',
                    ],
                    ['f_fin' => 'IS NULL', 'modo' => '~'],
                );
                $actual_id_sacd_titular = 0;
                $actual_modo = 0;
                $oActualTitular = null;
                foreach ($cTitular as $oT) {
                    $actual_id_sacd_titular = (int)$oT->getId_nom();
                    $actual_modo = (int)$oT->getModo();
                    $oActualTitular = $oT;
                }

                $modo = $cl ? 2 : 3;
                if ($actual_id_sacd_titular !== $id_sacd_titular) {
                    $oEncargoFunciones->insert_sacd($id_enc, $id_sacd_titular, $modo);
                    $oEncargoFunciones->finalizar_horario_sacd($id_enc, $actual_id_sacd_titular, $oF_fin);
                    if ($actual_id_sacd_titular !== 0) {
                        $oEncargoFunciones->finalizar_sacd($id_enc, $actual_id_sacd_titular, $actual_modo, $oF_fin);
                    }
                } elseif ($actual_modo !== $modo && $oActualTitular !== null) {
                    $cExistente = $EncargoSacdRepository->getEncargosSacd([
                        'id_enc' => $id_enc,
                        'id_nom' => $actual_id_sacd_titular,
                        'modo' => $modo,
                        'f_ini' => $oF_ini->getIso(),
                    ]);
                    foreach ($cExistente as $oEx) {
                        if ($EncargoSacdRepository->Eliminar($oEx) === false) {
                            return ['error' => _('hay un error, no se ha eliminado') . "\n" . $EncargoSacdRepository->getErrorTxt()];
                        }
                    }
                    $oActualTitular->setModo($modo);
                    if ($EncargoSacdRepository->Guardar($oActualTitular) === false) {
                        return ['error' => _('hay un error, no se ha guardado') . "\n" . $oActualTitular->getErrorTxt()];
                    }
                }
                $Aid_sacd[0] = $id_sacd_titular;
            }

            $sacd_id = (int)($Aid_sacd[$i] ?? 0);
            if ($sacd_id !== 0) {
                $cItem = $EncargoSacdRepository->getEncargosSacd(
                    [
                        'id_nom' => $sacd_id,
                        'id_enc' => $id_enc,
                        'modo' => '(2|3|5)',
                        'f_fin' => 'x',
                    ],
                    ['f_fin' => 'IS NULL', 'modo' => '~'],
                );
                $id_item_t_sacd = 0;
                foreach ($cItem as $oIt) {
                    $id_item_t_sacd = (int)$oIt->getId_item();
                }

                $oEncargoFunciones->modificar_horario_sacd($id_item_t_sacd, $id_enc, $sacd_id, 'm', (string)($Adedic_m[$i] ?? ''));
                $oEncargoFunciones->modificar_horario_sacd($id_item_t_sacd, $id_enc, $sacd_id, 't', (string)($Adedic_t[$i] ?? ''));
                $oEncargoFunciones->modificar_horario_sacd($id_item_t_sacd, $id_enc, $sacd_id, 'v', (string)($Adedic_v[$i] ?? ''));
            }
        }

        if ($id_sacd_suplente !== 0) {
            $cSupl = $EncargoSacdRepository->getEncargosSacd(
                ['id_enc' => $id_enc, 'modo' => '4', 'f_fin' => 'x'],
                ['f_fin' => 'IS NULL'],
            );
            if (!is_array($cSupl) || count($cSupl) === 0) {
                $oEncargoFunciones->insert_sacd($id_enc, $id_sacd_suplente, 4);
            } else {
                foreach ($cSupl as $oS) {
                    $actual_id_sacd_suplente = (int)$oS->getId_nom();
                    if ($actual_id_sacd_suplente !== $id_sacd_suplente) {
                        $oS->setF_fin($oF_fin);
                        if ($oS->DBGuardar() === false) {
                            return ['error' => _('hay un error, no se ha guardado') . "\n" . $oS->getErrorTxt()];
                        }
                        $oEncargoFunciones->insert_sacd($id_enc, $id_sacd_suplente, 4);
                    }
                }
            }
        } else {
            $cSupl = $EncargoSacdRepository->getEncargosSacd(['id_enc' => $id_enc, 'modo' => 4]);
            foreach ($cSupl as $oS) {
                $oS->setF_fin($oF_fin);
                if ($EncargoSacdRepository->Guardar($oS) === false) {
                    return ['error' => _('hay un error, no se ha guardado') . "\n" . $EncargoSacdRepository->getErrorTxt()];
                }
            }
        }

        return ['error' => ''];
    }
}
