<?php

namespace src\actividades\application;

use frontend\actividades\helpers\ActividadTipo;

/**
 * Datos del bloque de tipo de actividad (desplegables) para la pantalla actividad_que.
 * El HTML lo genera {@see ActividadTipo::getHtml()} (render vía Twig); aquí se captura
 * con buffer de salida para exponerlo en JSON sin instanciar `ActividadTipo` en el frontend.
 */
final class ActividadQueDatos
{
    /**
     * @param array{
     *   perm_jefe?: bool,
     *   id_tipo_activ?: int|string,
     *   sfsv?: string,
     *   sasistentes?: string,
     *   sactividad?: string,
     *   sactividad2?: string,
     *   snom_tipo?: string,
     *   extendida?: bool,
     *   que?: string,
     *   para?: string,
     *   sfsv_all?: bool,
     *   evitar_procesos?: bool,
     * } $input
     * @return array{actividad_tipo_html: string}
     */
    public function execute(array $input): array
    {
        $extendida = !empty($input['extendida']);

        $oActividadTipo = new ActividadTipo();
        $oActividadTipo->setPerm_jefe(!empty($input['perm_jefe']));
        $idTipo = $input['id_tipo_activ'] ?? 0;
        if ($idTipo === '') {
            $oActividadTipo->setId_tipo_activ(0);
        } elseif (is_numeric((string)$idTipo)) {
            $oActividadTipo->setId_tipo_activ((int)$idTipo);
        } else {
            $oActividadTipo->setId_tipo_activ((string)$idTipo);
        }
        if (!empty($input['que'])) {
            $oActividadTipo->setQue((string)$input['que']);
        }
        if (!empty($input['para'])) {
            $oActividadTipo->setPara((string)$input['para']);
        }
        $oActividadTipo->setSfsv((string)($input['sfsv'] ?? ''));
        $oActividadTipo->setAsistentes((string)($input['sasistentes'] ?? ''));
        if ($extendida) {
            $oActividadTipo->setActividad2Digitos((string)($input['sactividad2'] ?? ''));
        } else {
            $oActividadTipo->setActividad((string)($input['sactividad'] ?? ''));
        }
        $oActividadTipo->setNom_tipo((string)($input['snom_tipo'] ?? ''));

        $sfsvAll = array_key_exists('sfsv_all', $input) ? !empty($input['sfsv_all']) : true;
        $oActividadTipo->setSfsvAll($sfsvAll);
        if (!empty($input['evitar_procesos'])) {
            $oActividadTipo->setEvitarProcesos(true);
        }

        ob_start();
        $oActividadTipo->getHtml($extendida);
        $html = ob_get_clean();

        return ['actividad_tipo_html' => $html === false ? '' : $html];
    }
}
