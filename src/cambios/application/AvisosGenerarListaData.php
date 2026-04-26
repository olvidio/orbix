<?php

namespace src\cambios\application;

use src\shared\config\ConfigGlobal;
use DateTimeZone;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use web\Hash;

/**
 * Data builder: lista de `CambioUsuario` del usuario solicitado (con
 * `avisado=false`) para la pantalla `avisos_generar`.
 *
 * Sucesor del backend de `apps/cambios/controller/avisos_generar.php`.
 */
final class AvisosGenerarListaData
{
    /**
     * @param array{id_usuario?: int|string, aviso_tipo?: int|string} $input
     * @return array{
     *   error: string,
     *   a_valores: array,
     *   aOpcionesUsuarios: array,
     *   aOpcionesAvisoTipo: array,
     * }
     */
    public static function execute(array $input): array
    {
        $is_admin = (bool)($input['is_admin'] ?? false);
        if ($is_admin) {
            $id_usuario = (int)($input['id_usuario'] ?? 0);
            $aviso_tipo = (int)($input['aviso_tipo'] ?? 0);
        } else {
            $id_usuario = (int)ConfigGlobal::mi_id_usuario();
            $aviso_tipo = AvisoTipoId::TIPO_LISTA;
        }

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $aOpcionesUsuarios = $UsuarioRepository->getArrayUsuarios();
        $aOpcionesAvisoTipo = AvisoTipoId::getArrayAvisoTipo();

        $a_valores = [];

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $baseOut = static function (array $extra) use ($web, $aOpcionesUsuarios, $aOpcionesAvisoTipo, $id_usuario, $aviso_tipo): array {
            $out = array_merge(
                [
                    'error' => '',
                    'a_valores' => [],
                    'aOpcionesUsuarios' => $aOpcionesUsuarios,
                    'aOpcionesAvisoTipo' => $aOpcionesAvisoTipo,
                    'effective_id_usuario' => $id_usuario,
                    'effective_aviso_tipo' => $aviso_tipo,
                    'web' => $web,
                    'url_eliminar' => '',
                    'url_eliminar_fecha' => '',
                    'h_eliminar' => '',
                    'h_eliminar_fecha' => '',
                ],
                $extra
            );
            if (empty($id_usuario)) {
                return $out;
            }
            $url_eliminar = $web . '/src/cambios/cambio_usuario_eliminar';
            $url_eliminar_fecha = $web . '/src/cambios/cambio_usuario_eliminar_hasta_fecha';
            $oHashElim = new Hash();
            $oHashElim->setUrl($url_eliminar);
            $oHashElim->setCamposNo('sel');
            $h_eliminar = $oHashElim->linkSinValParams();
            $oHashElimF = new Hash();
            $oHashElimF->setUrl($url_eliminar_fecha);
            $oHashElimF->setCamposForm('f_fin');
            $h_eliminar_fecha = $oHashElimF->linkSinValParams();

            $out['url_eliminar'] = $url_eliminar;
            $out['url_eliminar_fecha'] = $url_eliminar_fecha;
            $out['h_eliminar'] = $h_eliminar;
            $out['h_eliminar_fecha'] = $h_eliminar_fecha;

            return $out;
        };

        if (empty($id_usuario)) {
            return $baseOut([]);
        }

        $PreferenciaRepository = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
        $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'zona_horaria');
        $zona_horaria = $oPreferencia?->getPreferencia() ?? '';

        $DateTimeZone = new DateTimeZone('UTC');
        if (!empty($zona_horaria)) {
            try {
                $DateTimeZone = new DateTimeZone($zona_horaria);
            } catch (\Throwable) {
                $DateTimeZone = new DateTimeZone('UTC');
            }
        }

        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $dele = ConfigGlobal::mi_dele();
        $aSecciones = [1 => $dele, 2 => $dele . 'f'];

        $aWhere = [
            'id_usuario' => $id_usuario,
            'sfsv' => $mi_sfsv,
            'aviso_tipo' => $aviso_tipo,
            'avisado' => 'false',
        ];
        $CambiosUsuarioRepository = $GLOBALS['container']->get(CambioUsuarioRepositoryInterface::class);
        $cCambiosUsuario = $CambiosUsuarioRepository->getCambiosUsuario($aWhere);

        $CambioRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
        $CambioDlRepository = $GLOBALS['container']->get(CambioDlRepositoryInterface::class);

        $i = 0;
        foreach ($cCambiosUsuario as $oCambioUsuario) {
            $id_item_cmb = $oCambioUsuario->getId_item_cambio();
            $id_schema_cmb = $oCambioUsuario->getId_schema_cambio();
            $oCambio = $id_schema_cmb === 3000
                ? $CambioRepository->findById($id_item_cmb)
                : $CambioDlRepository->findById($id_item_cmb);
            if ($oCambio === null) {
                continue;
            }
            $quien_cambia = $oCambio->getQuien_cambia();
            $sfsv_quien_cambia = $oCambio->getSfsv_quien_cambia();
            $oTimestamp_cambio_GMT = $oCambio->getTimestamp_cambio();
            $timestamp_cambio = $oTimestamp_cambio_GMT->setTimezone($DateTimeZone)->getFromLocalHora();
            $timestamp_orden = $oCambio->getTimestamp_cambio()->format('YmdHis');

            $aviso_txt = $oCambio->getAvisoTxt();
            if ($aviso_txt === false) {
                continue;
            }
            $i++;
            if ($sfsv_quien_cambia === $mi_sfsv) {
                $oUsuarioCmb = $UsuarioRepository->findById($quien_cambia);
                $quien = $oUsuarioCmb->getUsuario();
            } else {
                $quien = $aSecciones[$sfsv_quien_cambia] ?? '';
            }
            // +1000 por si hay dos iguales.
            $num_orden = $timestamp_orden . (1000 + $i);
            $a_valores[$num_orden] = [
                'sel' => "$id_item_cmb#$id_usuario#$mi_sfsv#$aviso_tipo",
                1 => $timestamp_cambio,
                2 => $quien,
                3 => $aviso_txt,
            ];
        }
        ksort($a_valores, SORT_STRING);

        return $baseOut([
            'a_valores' => $a_valores,
        ]);
    }
}
