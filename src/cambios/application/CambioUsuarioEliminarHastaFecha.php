<?php

namespace src\cambios\application;

use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;

/**
 * Caso de uso: elimina los `CambioUsuario` con fecha anterior o igual a la
 * indicada.
 *
 * Sucesor de la rama `que=eliminar_fecha` del dispatcher
 * `apps/cambios/controller/avisos_generar_ajax.php`.
 */
final class CambioUsuarioEliminarHastaFecha
{
    public function __construct(
        private CambioUsuarioRepositoryInterface $cambioUsuarioRepository,
    ) {
    }

    /**
     * @param array{f_fin?: string} $input
     * @return array{ok: bool, mensaje: string}
     */
    public function execute(array $input): array
    {
        $f_fin = (string)($input['f_fin'] ?? '');
        if ($f_fin === '') {
            return [
                'ok' => false,
                'mensaje' => (string)_("debe indicar la fecha"),
            ];
        }
        $rta = $this->cambioUsuarioRepository->eliminarHastaFecha($f_fin);
        if ($rta === false) {
            return [
                'ok' => false,
                'mensaje' => (string)_("Hay un error al eliminar los cambios hasta la fecha indicada"),
            ];
        }
        return ['ok' => true, 'mensaje' => ''];
    }
}
