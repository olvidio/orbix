<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;

/**
 * Mutacion: elimina una `RelacionTarifaTipoActividad`.
 */
final class RelacionTarifaEliminar
{
    public function __construct(
        private RelacionTarifaTipoActividadRepositoryInterface $relacionTarifaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_item');
        if ($id_item === 0) {
            return (string) _("no sé cuál he de borrar");
        }

        $oRelacion = $this->relacionTarifaRepository->findById($id_item);
        if ($oRelacion === null) {
            return (string) _("no se encuentra la relación");
        }

        if ($this->relacionTarifaRepository->Eliminar($oRelacion) === false) {
            return (string) _("hay un error, no se ha borrado")
                . "\n" . $this->relacionTarifaRepository->getErrorTxt();
        }

        return '';
    }
}
