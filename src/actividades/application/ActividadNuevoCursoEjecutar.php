<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\is_true;

/**
 * Caso de uso: crear actividades del nuevo curso tomando como base las del
 * curso de referencia, solo para la delegacion actual.
 *
 * Devuelve:
 *   - html (string)  Fragmento con los mensajes de borrado, creacion y solapes
 *                    listos para mostrar en la vista.
 *   - copiadas (int) Numero de actividades efectivamente copiadas.
 */
final class ActividadNuevoCursoEjecutar
{
    public function __construct(
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private RepeticionRepositoryInterface $repeticionRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{html: string, copiadas: int}
     */
    public function ejecutar(array $input): array
    {
        $Qyear_ref = input_int($input, 'year_ref');
        $Qyear = input_int($input, 'year');
        $ver_lista = !empty($input['ver_lista']);

        $oNuevoCurso = new ActividadNuevoCurso(
            $this->repeticionRepository,
            $this->actividadDlRepository,
            $this->centroEncargadoRepository,
            $this->actividadProcesoTareaRepository,
        );
        $oNuevoCurso->setRegistrarCambios(false);
        $oNuevoCurso->setVer_lista($ver_lista);
        $oNuevoCurso->setYear($Qyear);
        $oNuevoCurso->setYear_ref($Qyear_ref);

        $inicio_iso = $Qyear . '-1-1';
        $fin_iso = $Qyear . '-12-31';
        $txt_borrar = $oNuevoCurso->borrar_actividades_periodo($inicio_iso, $fin_iso);

        $inicio_org = $Qyear_ref . '-1-1';
        $fin_org = $Qyear_ref . '-12-31';
        $ActividadDlRepository = $this->actividadDlRepository;
        $aWhere = [];
        $aOperador = [];
        $aWhere['dl_org'] = "'" . ConfigGlobal::mi_dele() . "','" . ConfigGlobal::mi_dele() . "f'";
        $aOperador['dl_org'] = 'IN';
        // No las de proyecto(1) ni borrables(4) => 2 y 3
        $aWhere['status'] = "2,3";
        $aOperador['status'] = 'IN';
        $aWhere['f_ini'] = "'$inicio_org','$fin_org'";
        $aOperador['f_ini'] = 'BETWEEN';
        $aWhere['_ordre'] = 'f_ini';
        $cActividades = $ActividadDlRepository->getActividades($aWhere, $aOperador);

        $html = '';
        if (is_true($ver_lista)) {
            $html .= _("tipo repetición => fechas_new :: nom_activ_new") . "<br>";
        }

        $txt_crear = '';
        $i = 0;
        foreach ($cActividades as $oActividadOrg) {
            $rta = $oNuevoCurso->crear_actividad($oActividadOrg);
            if (empty($rta)) {
                $i++;
            }
            $txt_crear .= $rta;
        }
        $txt_solapes = $oNuevoCurso->comprobar_solapes($inicio_iso, $fin_iso);

        $html .= "<h3>" . sprintf(_("%s actividades copiadas"), $i) . "</h3>";
        if (!empty($txt_borrar)) {
            $html .= "<h3>" . _("incidencias al borrar") . "</h3>";
            $html .= $txt_borrar;
        }
        if (!empty($txt_crear)) {
            $html .= "<h3>" . _("errores al crear") . "</h3>";
            $html .= $txt_crear;
        }
        if (!empty($txt_solapes)) {
            $html .= "<h3>" . _("solapes") . "</h3>";
            $html .= $txt_solapes;
        }
        $avisosProceso = $oNuevoCurso->consumirAvisosProceso();
        if ($avisosProceso !== []) {
            $html .= '<h3>' . _('avisos') . '</h3>';
            $html .= '<ul class="avisos">';
            foreach ($avisosProceso as $aviso) {
                $html .= '<li>' . htmlspecialchars((string) $aviso, ENT_QUOTES, 'UTF-8') . '</li>';
            }
            $html .= '</ul>';
        }

        return [
            'html' => $html,
            'copiadas' => $i,
        ];
    }
}
