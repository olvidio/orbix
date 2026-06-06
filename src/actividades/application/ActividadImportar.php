<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\Importada;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use function src\shared\domain\helpers\input_string_list;

/**
 * Importa actividades seleccionadas y regenera su proceso si aplica.
 * Sustituye la lógica del antiguo case `importar` de actividad_update.php.
 */
final class ActividadImportar
{
    public function __construct(
        private ImportadaRepositoryInterface $importadaRepository,
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error_txt: string, avisos: list<string>}
     */
    public function execute(array $input): array
    {
        $error_txt = '';
        $avisos = [];
        $a_sel = input_string_list($input, 'sel');

        if ($a_sel === []) {
            return ['error_txt' => $error_txt, 'avisos' => $avisos];
        }

        foreach ($a_sel as $id) {
            $id_activ = (int) strtok($id, '#');
            $oImportada = new Importada();
            $oImportada->setId_activ($id_activ);
            if ($this->importadaRepository->Guardar($oImportada) === false) {
                $error_txt .= _("hay un error, no se ha importado");
                $error_txt .= "\n" . $this->importadaRepository->getErrorTxt();
            }
            if (ConfigGlobal::is_app_installed('procesos')) {
                $this->actividadProcesoTareaRepository->generarProceso((string) $id_activ, ConfigGlobal::mi_sfsv(), true);
                foreach ($this->actividadProcesoTareaRepository->consumirAvisosGenerarProceso() as $aviso) {
                    $avisos[] = $aviso;
                }
            }
        }

        return ['error_txt' => $error_txt, 'avisos' => $avisos];
    }
}
