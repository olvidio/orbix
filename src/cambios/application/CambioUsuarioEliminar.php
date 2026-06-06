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
    public function __construct(
        private CambioUsuarioRepositoryInterface $cambioUsuarioRepository,
    ) {
    }

    /**
     * @param array{sel?: list<string>} $input
     * @return array{ok: bool, mensaje: string}
     */
    public function execute(array $input): array
    {
        $sel = (array)($input['sel'] ?? []);
        if ($sel === []) {
            return ['ok' => true, 'mensaje' => ''];
        }

        $errores = [];
        foreach ($sel as $id) {
            $id_item_cmb = strtok((string)$id, '#');
            $id_usuario = strtok('#');
            $sfsv = strtok('#');
            $aviso_tipo = strtok('#');
            if ($id_item_cmb === false || $id_usuario === false || $sfsv === false || $aviso_tipo === false) {
                continue;
            }
            $aWhere = [
                'id_item_cambio' => (int)$id_item_cmb,
                'id_usuario' => (int)$id_usuario,
                'sfsv' => (int)$sfsv,
                'aviso_tipo' => (int)$aviso_tipo,
            ];
            $cCambiosUsuario = $this->cambioUsuarioRepository->getCambiosUsuario($aWhere);
            foreach ($cCambiosUsuario as $oCambioUsuario) {
                if ($this->cambioUsuarioRepository->Eliminar($oCambioUsuario) === false) {
                    $errores[] = $this->cambioUsuarioRepository->getErrorTxt();
                }
            }
        }
        if ($errores !== []) {
            return [
                'ok' => false,
                'mensaje' => (string)_("Hay un error, no se ha eliminado") . "\n" . implode("\n", $errores),
            ];
        }
        return ['ok' => true, 'mensaje' => ''];
    }
}
