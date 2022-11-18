<?php

use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use notas\model\entity as notas;
use profesores\model\entity as profesores;
use web\Periodo;
use actividades\model\entity\ActividadAll;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/*
 * Esta página actualiza los datos del dossier "d_docencia_stgr"
 * con la información que se tiene de los ca.
 * Se cogen los ca marcados como terminados (así se copia el acta...)
 */

$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$continuar = (integer)filter_input(INPUT_POST, 'continuar');

if (empty($continuar)) {
    //Periodo
    $boton = "<input type='button' value='" . _("buscar") . "' onclick='fnjs_buscar()' >";
    $aOpciones = array(
        'tot_any' => _("todo el año"),
        'trimestre_1' => _("primer trimestre"),
        'trimestre_2' => _("segundo trimestre"),
        'trimestre_3' => _("tercer trimestre"),
        'trimestre_4' => _("cuarto trimestre"),
        'separador' => '---------',
        'curso_ca' => _("curso ca"),
        'separador1' => '---------',
        'otro' => _("otro")
    );
    $oFormP = new web\PeriodoQue();
    $oFormP->setFormName('que');
    $oFormP->setTitulo(core\strtoupper_dlb(_("periodo de selección de actividades")));
    $oFormP->setPosiblesPeriodos($aOpciones);
    $oFormP->setDesplAnysOpcion_sel($Qyear);
    $oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
    $oFormP->setEmpiezaMax($Qempiezamax);
    $oFormP->setEmpiezaMin($Qempiezamin);
    $oFormP->setBoton($boton);
    $oHashPeriodo = new web\Hash();
    $oHashPeriodo->setCamposForm('empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
    $oHashPeriodo->setCamposNo('!refresh');
    $a_camposHiddenP = array('continuar' => 1,
    );
    $oHashPeriodo->setArraycamposHidden($a_camposHiddenP);

    $a_campos = array('mod' => 'inicio',
        'oFormP' => $oFormP,
        'oHashPeriodo' => $oHashPeriodo,
    );

} else {
    // valores por defeccto
    if (empty($Qperiodo)) {
        $Qperiodo = 'curso_ca';
    }

    // periodo.
    $oPeriodo = new Periodo();
    $oPeriodo->setAny($Qyear);
    $oPeriodo->setEmpiezaMin($Qempiezamin);
    $oPeriodo->setEmpiezaMax($Qempiezamax);
    $oPeriodo->setPeriodo($Qperiodo);

    $inicioIso = $oPeriodo->getF_ini_iso();
    $finIso = $oPeriodo->getF_fin_iso();
    $txt_curso = $oPeriodo->getTxt_cusro();

    $txt_rta = sprintf(_("Se ha actualizado la docencia para el periodo: %s"), $txt_curso);

    $aWhere = [];
    $aOperador = [];
    $aWhere['f_ini'] = "'$inicioIso','$finIso'";
    $aOperador['f_ini'] = 'BETWEEN';
    $aWhere['status'] = ActividadAll::STATUS_TERMINADA;

    $mi_sfsv = core\ConfigGlobal::mi_sfsv();
    //$id_tipo='^'.$mi_sfsv.'[123][23]';   // OJO AÑADO sem inv.
    $id_tipo = '^' . $mi_sfsv . '[123][23]';
    $id_tipo_inv = '^' . $mi_sfsv . '325';
    $aWhere['id_tipo_activ'] = $id_tipo;
    $aOperador['id_tipo_activ'] = '~';
    //$GesActividades = new actividades\GestorActividadDl();
    // Lo cambio para actividades de todas las dl.
    $GesActividades = new actividades\GestorActividad();
    $cActividades = $GesActividades->getActividades($aWhere, $aOperador);
    $ini_d = $_SESSION['oConfig']->getDiaIniStgr();
    $ini_m = $_SESSION['oConfig']->getMesIniStgr();
    // busco los profesores que han dado alguna asignatura en actividad.
    $GesProfesorDocencia = new profesores\GestorProfesorDocenciaStgr();
    foreach ($cActividades as $oActividad) {
        $id_activ = $oActividad->getId_activ();
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $oFini = $oActividad->getF_ini();
        $mes = $oFini->format('m');
        $any = $oFini->format('Y');
        if ($mes < $ini_m) {
            $ini_a = $any - 1;
        } else {
            $ini_a = $any;
        }
        //$GesAsignaturasCa = new actividadestudios\GestorActividadAsignaturaDl();
        $GesAsignaturasCa = new actividadestudios\GestorActividadAsignatura();
        $cActivAsignaturas = $GesAsignaturasCa->getActividadAsignaturas(array('id_activ' => $id_activ), array('id_profesor' => 'IS NOT NULL'));

        foreach ($cActivAsignaturas as $oActividadAsignatura) {
            $id_asignatura = $oActividadAsignatura->getId_asignatura();
            $id_profesor = $oActividadAsignatura->getId_profesor();
            if (empty($id_profesor)) {
                continue;
            }
            $tipo = $oActividadAsignatura->getTipo();
            // si no es con preceptor, pongo ca o inv
            if (empty($tipo)) {
                $tipo = actividadestudios\ActividadAsignatura::TIPO_CA;
                if (preg_match("/$id_tipo_inv/", $id_tipo_activ)) { // semestre de invierno (ca agd)
                    $tipo = actividadestudios\ActividadAsignatura::TIPO_INV;
                }
            }

            $GesActas = new notas\GestorActa();
            $cActas = $GesActas->getActas(array('id_activ' => $id_activ, 'id_asignatura' => $id_asignatura));
            if (is_array($cActas) && count($cActas) > 0) {
                $acta = '';
                foreach ($cActas as $oActa) {
                    $acta .= empty($acta) ? '' : ', ';
                    $acta .= $oActa->getActa();
                }
            } else {
                $acta = '';
            }

            // Puede que ya lo tenga:
            $aWhereDocencia = ['id_nom' => $id_profesor, 'id_activ' => $id_activ, 'id_asignatura' => $id_asignatura];
            $cProfesorDocencia = $GesProfesorDocencia->getProfesorDocenciasStgr($aWhereDocencia);
            if (is_array($cProfesorDocencia) && count($cProfesorDocencia) > 0) {
                $oProfesorDocencia = $cProfesorDocencia[0];
                $oProfesorDocencia->DBCarregar();
                $oProfesorDocencia->setCurso_inicio($ini_a);
                $oProfesorDocencia->setTipo($tipo);
                $oProfesorDocencia->setActa($acta);
                $oProfesorDocencia->DBGuardar();
            } else {
                $oProfesorDocencia = new profesores\ProfesorDocenciaStgr();
                $oProfesorDocencia->setId_activ($id_activ);
                $oProfesorDocencia->setId_asignatura($id_asignatura);
                $oProfesorDocencia->setId_nom($id_profesor);
                $oProfesorDocencia->setCurso_inicio($ini_a);
                $oProfesorDocencia->setTipo($tipo);
                $oProfesorDocencia->setActa($acta);
                $oProfesorDocencia->DBGuardar();
            }
        }
    }

    $a_campos = array('mod' => 'fin',
        'txt_rta' => $txt_rta,
    );
}

$oView = new core\View('actividadestudios/controller');
$oView->renderizar('actualizar_docencia.phtml', $a_campos);