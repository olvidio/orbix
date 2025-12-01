<?php

namespace planning\domain;

use actividadcargos\model\GestorCargoOAsistente;
use actividades\model\entity\GestorActividad;
use core\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\DateTimeLocal;
use web\TiposActividades;

class ActividadesDePersona
{

    /**
     * @param array|false $cPersonas
     * @param int $multiples_ctr
     * @param string $nombre_ubi
     * @param array $aListaCtr
     * @param string $fin_iso
     * @param array $aWhere
     * @param array $aOperador
     * @param string $inicio_iso
     * @param GestorActividad $GesActividades
     * @param DateTimeLocal $oIniPlanning
     * @param string $inicio_local
     * @return array
     */
    public static function actividadesPorPersona(array|false $cPersonas, string $fin_iso, string $inicio_iso, DateTimeLocal $oIniPlanning, string $inicio_local): array
    {
        $GesActividades = new GestorActividad();
        $aListaCtr = [];
        $p = 0;
        $persona = [];
        $a_actividades = [];
        $a_actividades2 = [];
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        foreach ($cPersonas as $oPersona) {
            $aActivPersona = [];
            $id_nom = $oPersona->getId_nom();
            $nombre = $oPersona->getPrefApellidosNombre();

            $id_ubi = $oPersona->getId_ctr();
            if (empty($id_ubi)) {
                $nombre_ubi = "centro?";
            } elseif (!in_array($id_ubi, $aListaCtr)) {
                $oCentroDl = $CentroDlRepository->findById($id_ubi);
                $nombre_ubi = $oCentroDl->getNombre_ubi();
                $aListaCtr[$id_ubi] = $nombre_ubi;
            } else {
                $nombre_ubi = $aListaCtr[$id_ubi];
            }
            $persona[$p] = "p#$id_nom#$nombre#$nombre_ubi";

            // Seleccionar sólo las del periodo y actuales o terminadas
            $aWhere = [];
            $aOperador = [];
            $aWhere['f_ini'] = "'$fin_iso'";
            $aOperador['f_ini'] = '<=';
            $aWhere['f_fin'] = "'$inicio_iso'";
            $aOperador['f_fin'] = '>=';
            $aWhere['status'] = '2,3';
            $aOperador['status'] = 'BETWEEN';

            if (ConfigGlobal::is_app_installed('actividadcargos')) {
                $GesCargoOAsistente = new GestorCargoOAsistente();
                $cCargoOAsistente = $GesCargoOAsistente->getCargoOAsistente($id_nom, $aWhere, $aOperador);
            } else {
                //$oGesAsistentes = new asistentes\GestorActividadCargo();
                echo "ja veurem...";
            }
            foreach ($cCargoOAsistente as $oCargoOAsistente) {
                $id_activ = $oCargoOAsistente->getId_activ();
                $propio = $oCargoOAsistente->getPropio();

                $aWhere['id_activ'] = $id_activ;
                $cActividades = $GesActividades->getActividades($aWhere, $aOperador);
                if (is_array($cActividades) && count($cActividades) == 0) continue;

                $oActividad = $cActividades[0]; // sólo debería haber una.
                $id_activ = $oActividad->getId_activ();
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $oF_ini = $oActividad->getF_ini();
                $h_ini = $oActividad->getH_ini();
                $oF_fin = $oActividad->getF_fin();
                $h_fin = $oActividad->getH_fin();
                $dl_org = $oActividad->getDl_org();
                $nom_activ = $oActividad->getNom_activ();

                $css = PlanningStyle::clase($id_tipo_activ, $propio, '', $oActividad->getStatus());

                $oTipoActividad = new TiposActividades($id_tipo_activ);
                $ssfsv = $oTipoActividad->getSfsvText();

                //para el caso de que la actividad comience antes
                //del periodo de inicio obligo a que tome una hora de inicio
                //en el entorno de las primeras del día (a efectos del planning
                //ya es suficiente con la 1:16 de la madrugada)
                if ($oIniPlanning > $oF_ini) {
                    $ini = $inicio_local;
                    $hini = "1:16";
                } else {
                    $ini = (string)$oF_ini->getFromLocal();
                    $hini = (string)$h_ini;
                }
                $fi = (string)$oF_fin->getFromLocal();
                $hfi = (string)$h_fin;

                // mirar permisos.
                if (ConfigGlobal::is_app_installed('procesos')) {
                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

                    if ($oPermActiv->have_perm_activ('ocupado') === false) continue; // no tiene permisos ni para ver.
                    if ($oPermActiv->have_perm_activ('ver') === false) { // sólo puede ver que està ocupado
                        $nom_curt = $ssfsv;
                        $nom_llarg = "$ssfsv ($ini-$fi)";
                    } else {
                        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                        $nom_llarg = $nom_activ;
                    }
                } else {
                    $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                    $nom_llarg = $nom_activ;
                }

                $aActivPersona[] = array(
                    'nom_curt' => $nom_curt,
                    'nom_llarg' => $nom_llarg,
                    'f_ini' => $ini,
                    'h_ini' => $hini,
                    'f_fi' => $fi,
                    'h_fi' => $hfi,
                    'id_tipo_activ' => $id_tipo_activ,
                    'pagina' => '',
                    'id_activ' => $id_activ,
                    'propio' => $propio,
                    'css' => $css,
                );
            }
            // En los profesores, añado las clases del stgr en actividades
            /*
            $GesActividadAsignaturas = new actividadestudios\GestorActividadAsignaturaDl();
            $aWhere = array('f_ini' => "'$inicio_iso','$fin_iso'");
            $aOperador = array('f_ini' => 'BETWEEN');
            $GesActividadAsignaturas->getActividadAsignaturas($aWhere, $aOperador);
             $cAsignaturas = $GesActividadAsignaturas->getActividadAsignaturasProfesor($id_nom);
             if ($cAsignaturas !== false) {
             foreach ($cAsignaturas as $oActividadAsignatura) {
             $id_activ = $oActividadAsignatura->getId_activ();
             $oActividad = new actividades\Actividad($id_activ);
             $nom_activ = $oActividad->getNom_activ();

             $f_ini = $oActividadAsignatura->getF_ini()->getFromLocal();
             $f_fin = $oActividadAsignatura->getF_fin()->getFromLocal();

             $nom_curt = _("clases stgr");
             $nom_llarg = $nom_curt." "._("en")." ".$nom_activ;
             $aActivPersona[]=array(
             'nom_curt'=>$nom_curt,
             'nom_llarg'=>$nom_llarg,
             'f_ini'=>$f_ini,
             'h_ini'=>'',
             'f_fi'=>$f_fin,
             'h_fi'=>'',
             'id_tipo_activ'=>'',
             'pagina'=>'',
             'id_activ'=>$id_activ,
             'propio'=>''
             );

             }
             }
             */

            $a_actividades2[$nombre_ubi][] = [$persona[$p] => $aActivPersona];
            $p++;
        }
        return $a_actividades2;
    }
}