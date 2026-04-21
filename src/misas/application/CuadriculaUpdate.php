<?php

namespace src\misas\application;

use DateTimeZone;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\misas\domain\value_objects\EncargoDiaTend;
use src\misas\domain\value_objects\EncargoDiaTstart;
use src\misas\domain\value_objects\PlantillaConfig;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use web\TiposActividades;

/**
 * Use case del endpoint `cuadricula_update` (migracion de
 * `apps/misas/controller/cuadricula_update.php` al Slice 6a).
 *
 * Hace dos cosas en la misma transaccion logica:
 *
 *  1. Upsert / delete de un `EncargoDia` para un dia + encargo concretos,
 *     en funcion de `key` (si esta vacio, se borra; si trae `id_nom`, se
 *     guarda o actualiza).
 *  2. Recalcula el bloque `meta` que la UI usa para pintar colores y textos
 *     (disponibilidad del sacd anterior y del nuevo, numero de misas del
 *     dia, conflictos con primera hora, etc.).
 *
 * El codigo es una traduccion casi literal del controlador original para
 * minimizar riesgo de regresion: la logica de negocio en si no cambia en
 * este slice; lo unico que cambia es donde vive.
 */
class CuadriculaUpdate
{
    /**
     * @return array{error: string, meta: array}
     */
    public static function execute(
        string $uuid_item,
        string $key,
        string $tstart,
        string $tend,
        string $observ,
        int $id_enc,
        string $dia_iso,
        string $tipo_plantilla,
        int $id_zona,
    ): array {
        if (empty($uuid_item)) {
            return ['error' => _('Falta el id_item'), 'meta' => []];
        }

        $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
        $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
        $InicialesSacdService = $GLOBALS['container']->get(InicialesSacdService::class);

        $Uuid = new EncargoDiaId($uuid_item);
        $oEncargoDia = $EncargoDiaRepository->findById($Uuid);
        if ($oEncargoDia === null) {
            $oEncargoDia = new EncargoDia();
            $oEncargoDia->setUuidItemVo($Uuid);
            $oEncargoDia->setId_enc($id_enc);
        }
        $id_sacd_anterior = $oEncargoDia->getId_nom();
        $estado = $oEncargoDia->getStatus();

        // Color de la celda (misa) segun estado, solo en vista `plan de misas`.
        $color_misa = '';
        if (trim($tipo_plantilla) === PlantillaConfig::PLAN_DE_MISAS) {
            if ($estado === EncargoDiaStatus::STATUS_PROPUESTA) {
                $color_misa = 'rojoclaro';
            }
            if ($estado === EncargoDiaStatus::STATUS_COMUNICADO_SACD) {
                $color_misa = 'amarilloclaro';
            }
            if ($estado === EncargoDiaStatus::STATUS_COMUNICADO_CTR) {
                $color_misa = 'verdeclaro';
            }
        }

        $error_txt = '';
        $id_nom = '';
        if (empty($key)) {
            // Sin sacd → eliminar la asignacion.
            if ($EncargoDiaRepository->Eliminar($oEncargoDia) === false) {
                $error_txt .= $EncargoDiaRepository->getErrorTxt();
            }
        } else {
            // `key` = "iniciales#id_nom".
            $porciones = explode('#', $key);
            $id_nom = $porciones[1] ?? '';
            $oEncargoDia->setId_nom($id_nom);
            $oEncargoDia->setTstart(new EncargoDiaTstart($dia_iso, $tstart));
            $oEncargoDia->setTend(new EncargoDiaTend($dia_iso, $tend));
            $oEncargoDia->setObserv($observ);

            if ($EncargoDiaRepository->Guardar($oEncargoDia) === false) {
                $error_txt .= $EncargoDiaRepository->getErrorTxt();
            }
        }

        // Datos de la zona: sacds y en que dias de la semana estan.
        $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_zona' => $id_zona], []);
        $lista_sacd = [];
        $esta_en_zona = [];
        foreach ($cZonaSacd as $oZonaSacd) {
            $id_nom_aux = $oZonaSacd->getId_nom();
            $lista_sacd[$id_nom_aux] = $InicialesSacdService->obtenerNombreConIniciales($id_nom_aux);
            $esta_en_zona[$id_nom_aux] = [
                '',
                $oZonaSacd->isDw1(),
                $oZonaSacd->isDw2(),
                $oZonaSacd->isDw3(),
                $oZonaSacd->isDw4(),
                $oZonaSacd->isDw5(),
                $oZonaSacd->isDw6(),
                $oZonaSacd->isDw7(),
            ];
        }

        // Disponibilidad del sacd anterior y del nuevo segun actividades y ausencias.
        [$esta_sacd_anterior, $donde_esta_sacd_anterior] = self::computeDisponibilidadSacd(
            $id_sacd_anterior,
            $dia_iso,
            $ActividadCargoRepository,
            $ActividadRepository,
            $EncargoSacdHorarioRepository,
            $EncargoRepository,
        );
        [$esta_sacd, $donde_esta_sacd] = self::computeDisponibilidadSacd(
            $id_nom,
            $dia_iso,
            $ActividadCargoRepository,
            $ActividadRepository,
            $EncargoSacdHorarioRepository,
            $EncargoRepository,
        );

        // Dia de la semana (ISO 1-7) del dia afectado.
        $dia = new DateTimeLocal($dia_iso, new DateTimeZone('Europe/Madrid'));
        $dws = (int)$dia->format('N');

        // Conteo de misas del dia para el sacd anterior (para pintar el
        // estado final de la fila del sacd anterior).
        [$texto_anterior, $color_fondo_anterior, $texto_sacd_anterior, $comprobacion_prev] = self::computeMetaSacdDia(
            $id_sacd_anterior,
            $dia_iso,
            $id_zona,
            $dws,
            $esta_en_zona,
            $esta_sacd_anterior,
            $donde_esta_sacd_anterior,
            $EncargoDiaRepository,
            $EncargoRepository,
        );

        // Idem para el sacd nuevo (ya asignado).
        [$texto, $color_fondo, $texto_sacd, $comprobacion_new] = self::computeMetaSacdDia(
            $id_nom,
            $dia_iso,
            $id_zona,
            $dws,
            $esta_en_zona,
            $esta_sacd,
            $donde_esta_sacd,
            $EncargoDiaRepository,
            $EncargoRepository,
        );

        $comprobacion = trim($comprobacion_prev . ' ----- ' . $comprobacion_new);

        if (!empty($error_txt)) {
            return ['error' => 'ERROR: ' . $error_txt, 'meta' => []];
        }

        return [
            'error' => '',
            'meta' => [
                'color_misa' => $color_misa,
                'id_sacd_anterior' => $id_sacd_anterior,
                'texto_anterior' => $texto_anterior,
                'color_fondo_anterior' => $color_fondo_anterior,
                'texto_sacd_anterior' => $texto_sacd_anterior,
                'texto' => $texto,
                'color_fondo' => $color_fondo,
                'texto_sacd' => $texto_sacd,
                'comprobacion' => $comprobacion,
            ],
        ];
    }

    /**
     * Replica la logica del controlador original: mira si el sacd tiene
     * actividad que lo saque de la zona ese dia y/o si tiene un encargo con
     * horario (tipos 7 o 4) bloqueandolo.
     *
     * @return array{0: int, 1: string} [$esta, $donde_esta]
     *   `$esta` sigue la convencion del codigo original:
     *     1 = disponible (default)
     *     2 = tiene actividad que empieza hoy
     *    -1 = actividad que termina hoy
     *     0 = dia intermedio de una actividad
     *   El valor final es el ultimo asignado para ese dia (los bucles solo
     *   sobreescriben, igual que en el original).
     */
    private static function computeDisponibilidadSacd(
        string $id_sacd,
        string $dia_iso,
        ActividadCargoRepositoryInterface $ActividadCargoRepository,
        ActividadRepositoryInterface $ActividadRepository,
        EncargoSacdHorarioRepositoryInterface $EncargoSacdHorarioRepository,
        EncargoRepositoryInterface $EncargoRepository,
    ): array {
        if (empty($id_sacd)) {
            return [1, ''];
        }

        $esta = 1;
        $donde = '';

        // Actividades con el sacd como asistente en el dia.
        $aWhereAct = [
            'f_ini' => $dia_iso,
            'f_fin' => $dia_iso,
            'status' => StatusId::ACTUAL,
        ];
        $aOperadorAct = [
            'f_ini' => '<=',
            'f_fin' => '>=',
        ];
        $cAsistentes = $ActividadCargoRepository->getAsistenteCargoDeActividad(
            ['id_nom' => $id_sacd],
            [],
            $aWhereAct,
            $aOperadorAct,
        );
        foreach ($cAsistentes as $aAsistente) {
            $id_activ = $aAsistente['id_activ'];
            $aWhereAct['id_activ'] = $id_activ;
            $cActividades = $ActividadRepository->getActividades($aWhereAct, $aOperadorAct);
            if (!is_array($cActividades) || count($cActividades) === 0) {
                continue;
            }
            $oActividad = $cActividades[0];
            $oTipoActividad = new TiposActividades($oActividad->getId_tipo_activ());
            // Mantenemos la misma heuristica que el original.
            $esta = $esta === 1 ? 2 : $esta;
            unset($oTipoActividad);
            $donde = $oActividad->getNom_activ();
        }

        // Ausencias (encargos horario) del sacd en el dia.
        $cAusencias = $EncargoSacdHorarioRepository->getEncargoSacdHorarios(
            [
                'id_nom' => $id_sacd,
                'f_ini' => "'$dia_iso'",
                'f_fin' => "'$dia_iso'",
            ],
            [
                'f_ini' => '<=',
                'f_fin' => '>=',
            ],
        );
        foreach ($cAusencias as $oTareaHorarioSacd) {
            $id_enc_aux = $oTareaHorarioSacd->getId_enc();
            $oEncargo = $EncargoRepository->findById($id_enc_aux);
            if ($oEncargo === null) {
                continue;
            }
            // Solo consideramos tipos 7 y 4 (los que bloquean).
            $id_tipo = (string)$oEncargo->getId_tipo_enc();
            if ($id_tipo === '' || ((int)$id_tipo[0] !== 7 && (int)$id_tipo[0] !== 4)) {
                continue;
            }
            $oF_ini = $oTareaHorarioSacd->getF_ini();
            $oF_fin = $oTareaHorarioSacd->getF_fin();
            $ini = (string)$oF_ini->getFromLocal();
            $fi = (string)$oF_fin->getFromLocal();
            $nom = $oEncargo->getDesc_enc();
            $nom .= ($ini !== $fi) ? " ($ini-$fi)" : " ($ini)";

            $esta = $esta === 1 ? 2 : $esta;
            $donde = $nom;
        }

        return [$esta, $donde];
    }

    /**
     * Calcula color + texto + contador de misas para una celda (sacd, dia).
     *
     * @return array{0: string, 1: string, 2: string, 3: string}
     *   [$texto, $color_fondo, $texto_sacd, $comprobacion]
     */
    private static function computeMetaSacdDia(
        string $id_sacd,
        string $dia_iso,
        int $id_zona,
        int $dws,
        array $esta_en_zona,
        int $esta_sacd,
        string $donde_esta_sacd,
        EncargoDiaRepositoryInterface $EncargoDiaRepository,
        EncargoRepositoryInterface $EncargoRepository,
    ): array {
        if (empty($id_sacd)) {
            return ['', 'verdeclaro', '--', ''];
        }

        $inicio_dia = $dia_iso . ' 00:00:00';
        $fin_dia = $dia_iso . ' 23:59:59';

        $cEncargosDia = $EncargoDiaRepository->getEncargoDias(
            [
                'id_nom' => $id_sacd,
                'tstart' => "'$inicio_dia', '$fin_dia'",
                '_ordre' => 'tstart',
            ],
            ['tstart' => 'BETWEEN'],
        );

        $misas_dia = 0;
        $misas_1a_hora = 0;
        $misas_dia_zona = 0;
        $misas_1a_hora_zona = 0;
        foreach ($cEncargosDia as $oEncargoDia) {
            $oEncargo = $EncargoRepository->findById($oEncargoDia->getId_enc());
            if ($oEncargo === null) {
                continue;
            }
            $id_tipo_enc = (string)$oEncargo->getId_tipo_enc();
            $id_zona_enc = (int)$oEncargo->getId_zona();
            if (strlen($id_tipo_enc) < 2) {
                continue;
            }
            $digito_hora = (int)$id_tipo_enc[1];

            if ($digito_hora === 1) {
                $misas_dia++;
                $misas_1a_hora++;
                if ($id_zona === $id_zona_enc) {
                    $misas_dia_zona++;
                    $misas_1a_hora_zona++;
                }
            } elseif ($digito_hora === 2) {
                $misas_dia++;
                if ($id_zona === $id_zona_enc) {
                    $misas_dia_zona++;
                }
            }
        }

        $esta_en_zona_flag = !empty($esta_en_zona[$id_sacd][$dws]);
        $color_fondo = 'verdeclaro';
        $texto = '';

        if ($misas_dia > 2) {
            $texto = _('Este día tiene más de dos Misas');
            $color_fondo = 'rojo';
        } elseif ($misas_dia === 2) {
            $texto = _('Este día tiene dos Misas');
            $color_fondo = 'amarillo';
        } elseif ($misas_dia === 0 && $esta_en_zona_flag) {
            $texto = _('Este día no tiene ninguna Misa');
            $color_fondo = 'verde';
        } elseif ($misas_dia === 0 && !$esta_en_zona_flag) {
            $texto = _('Este día no tiene ninguna Misa');
            $color_fondo = 'azulclaro';
        }

        if ($misas_1a_hora === 2) {
            $texto = _('Tiene dos Misas a primera hora');
            $color_fondo = 'rojo';
        }

        if ($esta_en_zona_flag) {
            $texto_sacd = 'SI';
        } else {
            if ($misas_1a_hora_zona > 0) {
                $color_fondo = 'rojo';
                $texto = _('No está en la zona y tiene Misa a primera hora');
            }
            $texto_sacd = 'NO';
        }

        if ($esta_sacd < 1) {
            if ($misas_1a_hora_zona > 0) {
                $color_fondo = 'rojo';
            }
            $texto = _('Está en ') . $donde_esta_sacd;
            $texto_sacd = '--';
        }

        $comprobacion = 'MD:' . $misas_dia . ' M1h:' . $misas_1a_hora
            . 'MDZ:' . $misas_dia_zona . 'Z:' . ($esta_en_zona_flag ? '1' : '0');

        return [$texto, $color_fondo, $texto_sacd, $comprobacion];
    }
}
