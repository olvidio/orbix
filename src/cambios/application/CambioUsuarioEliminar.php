<?php

namespace src\cambios\application;

use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;

/**
 * Caso de uso: elimina `CambioUsuario` por la clave compuesta seleccionada
 * en el listado (`id_item_cambio#id_usuario#sfsv#aviso_tipo`).
 *
 * Sucesor de la rama `que=eliminar` del dispatcher
 * `apps/cambios/controller/avisos_generar_ajax.php`.
 */
final class CambioUsuarioEliminar
{
    /**
     * @param array{sel?: array} $input
     * @return array{ok: bool, mensaje: string}
     */
    public static function execute(array $input): array
    {
        $sel = (array)($input['sel'] ?? []);
        if (empty($sel)) {
            return ['ok' => true, 'mensaje' => ''];
        }

        $CambioUsuarioRepository = $GLOBALS['container']->get(CambioUsuarioRepositoryInterface::class);
        $errores = [];
        foreach ($sel as $id) {
            $id_item_cmb = strtok((string)$id, '#');
            $id_usuario = strtok('#');
            $sfsv = strtok('#');
            $aviso_tipo = strtok('#');
            if ($id_item_cmb === false) {
                continue;
            }
            $aWhere = [
                'id_item_cambio' => (int)$id_item_cmb,
                'id_usuario' => (int)$id_usuario,
                'sfsv' => (int)$sfsv,
                'aviso_tipo' => (int)$aviso_tipo,
            ];
            $cCambiosUsuario = $CambioUsuarioRepository->getCambiosUsuario($aWhere);
            if (!is_array($cCambiosUsuario)) {
                continue;
            }
            foreach ($cCambiosUsuario as $oCambioUsuario) {
                if ($oCambioUsuario->DBEliminar() === false) {
                    $errores[] = $oCambioUsuario->getErrorTxt();
                }
            }
        }
        if (!empty($errores)) {
            return [
                'ok' => false,
                'mensaje' => (string)_("Hay un error, no se ha eliminado") . "\n" . implode("\n", $errores),
            ];
        }
        return ['ok' => true, 'mensaje' => ''];
    }
}
