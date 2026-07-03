<?php

namespace src\actividades\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Marca como publicadas las actividades seleccionadas.
 * Sustituye la lógica del antiguo case `publicar` de actividad_update.php.
 */
final class ActividadPublicar
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $error_txt = '';
        $a_sel = FuncTablasSupport::inputStringList($input, 'sel');

        if ($a_sel === []) {
            return $error_txt;
        }

        foreach ($a_sel as $id) {
            $id_activ = (int) strtok($id, '#');
            $oActividad = $this->actividadAllRepository->findById($id_activ);
            if ($oActividad === null) {
                continue;
            }
            $oActividad->setPublicado(true);
            if ($this->actividadAllRepository->Guardar($oActividad) === false) {
                $error_txt .= _("hay un error, no se ha guardado");
                $error_txt .= "\n" . $this->actividadAllRepository->getErrorTxt();
            }
        }

        return $error_txt;
    }
}
