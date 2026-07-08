<?php

namespace src\cambios\application;

use DateTimeZone;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Data builder: lista de `CambioUsuario` del usuario solicitado (con
 * `avisado=false`) para la pantalla `avisos_generar`.
 *
 * URLs y fragmentos hash de eliminación: {@see \frontend\cambios\helpers\AvisosGenerarListaRender}.
 *
 * Sucesor del backend de `apps/cambios/controller/avisos_generar.php`.
 */
final class AvisosGenerarListaData
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository,
        private PreferenciaRepositoryInterface $preferenciaRepository,
        private CambioUsuarioRepositoryInterface $cambioUsuarioRepository,
        private CambioParaAvisoLookup $cambioParaAvisoLookup,
        private CambioAvisoTxtBuilder $cambioAvisoTxtBuilder,
    ) {
    }

    /**
     * @param array{id_usuario?: int|string, aviso_tipo?: int|string, is_admin?: bool} $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $is_admin = (bool)($input['is_admin'] ?? false);
        if ($is_admin) {
            $id_usuario = (int)($input['id_usuario'] ?? 0);
            $aviso_tipo = (int)($input['aviso_tipo'] ?? 0);
        } else {
            $id_usuario = (int)ConfigGlobal::mi_id_usuario();
            $aviso_tipo = AvisoTipoId::TIPO_LISTA;
        }

        $aOpcionesUsuarios = $this->usuarioRepository->getArrayUsuarios();
        $aOpcionesAvisoTipo = AvisoTipoId::getArrayAvisoTipo();

        $a_valores = [];

        $baseOut = static function (array $extra) use ($aOpcionesUsuarios, $aOpcionesAvisoTipo, $id_usuario, $aviso_tipo): array {
            /** @var array<string, mixed> $out */
            $out = array_merge(
                [
                    'error' => '',
                    'a_valores' => [],
                    'aOpcionesUsuarios' => $aOpcionesUsuarios,
                    'aOpcionesAvisoTipo' => $aOpcionesAvisoTipo,
                    'effective_id_usuario' => $id_usuario,
                    'effective_aviso_tipo' => $aviso_tipo,
                ],
                $extra
            );
            if ($id_usuario === 0) {
                return $out;
            }

            $out['paths'] = [
                'eliminar' => 'src/cambios/cambio_usuario_eliminar',
                'eliminar_fecha' => 'src/cambios/cambio_usuario_eliminar_hasta_fecha',
            ];
            $out['hash_eliminar'] = [
                'campos_no' => 'sel',
            ];
            $out['hash_eliminar_fecha'] = [
                'campos_form' => 'f_fin',
            ];

            return $out;
        };

        if ($id_usuario === 0) {
            return $baseOut([]);
        }

        $oPreferencia = $this->preferenciaRepository->findById($id_usuario, 'zona_horaria');
        $zona_horaria = $oPreferencia?->getPreferencia() ?? '';

        $DateTimeZone = new DateTimeZone('UTC');
        if ($zona_horaria !== '') {
            try {
                $DateTimeZone = new DateTimeZone($zona_horaria);
            } catch (\Throwable) {
                $DateTimeZone = new DateTimeZone('UTC');
            }
        }

        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $dele = ConfigGlobal::mi_dele();
        /** @var array<int, string> $aSecciones */
        $aSecciones = [1 => $dele, 2 => $dele . 'f'];

        $aWhere = [
            'id_usuario' => $id_usuario,
            'sfsv' => $mi_sfsv,
            'aviso_tipo' => $aviso_tipo,
            'avisado' => 'false',
        ];
        $cCambiosUsuario = $this->cambioUsuarioRepository->getCambiosUsuario($aWhere);

        $i = 0;
        foreach ($cCambiosUsuario as $oCambioUsuario) {
            $id_item_cmb = $oCambioUsuario->getId_item_cambio();
            $id_schema_cmb = $oCambioUsuario->getId_schema_cambio();
            $oCambio = $this->cambioParaAvisoLookup->find($id_schema_cmb, $id_item_cmb);
            if ($oCambio === null) {
                continue;
            }
            $quien_cambia = $oCambio->getQuien_cambia();
            $sfsv_quien_cambia = $oCambio->getSfsv_quien_cambia();
            $oTimestamp_cambio_GMT = $oCambio->getTimestamp_cambio();
            if (!$oTimestamp_cambio_GMT instanceof DateTimeLocal) {
                continue;
            }
            $timestamp_cambio = (clone $oTimestamp_cambio_GMT)->setTimezone($DateTimeZone)->getFromLocalHora();
            $timestamp_orden = $oTimestamp_cambio_GMT->format('YmdHis');

            $aviso_txt = $this->cambioAvisoTxtBuilder->build($oCambio);
            if ($aviso_txt === false) {
                continue;
            }
            $i++;
            if ($sfsv_quien_cambia === $mi_sfsv && $quien_cambia !== null) {
                $oUsuarioCmb = $this->usuarioRepository->findById($quien_cambia);
                $quien = $oUsuarioCmb?->getUsuarioAsString() ?? '';
            } else {
                $quien = $aSecciones[$sfsv_quien_cambia] ?? '';
            }
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
