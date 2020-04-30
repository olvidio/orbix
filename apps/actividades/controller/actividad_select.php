<?php
/**
 * Esta página muestra una tabla con las actividades que cumplen con la condicion.
 * He quitado la posibilidad de buscar por sacd i por ctr. Quedan las opciones:
 *
 *@param 	$que
 *        	$status por defecto = 2
 *        	$id_tipo_activ
 *        	$id_ubi
 *        	$periodo
 *        	$year
 *        	$dl_org
 *        	$empiezamin
 * 		 	$empiezamax
 *
 * Si el resultado es más de 200, pregunta si quieres seguir.
 *
 *@package	delegacion
 *@subpackage	actividades
 *@author	Daniel Serrabou
 *@since		15/5/02.
 *@ajax		23/8/2007.
 *
 */

use actividades\model\entity\Actividad;
use actividades\model\entity\GestorActividad;
use actividades\model\entity\GestorActividadPub;
use actividades\model\entity\GestorImportada;
use actividadescentro\model\entity\GestorCentroEncargado;
use actividadtarifas\model\entity\TipoTarifa;
use core\ConfigGlobal;
use permisos\model\PermisosActividadesTrue;
use procesos\model\entity\GestorActividadProcesoTarea;
use usuarios\model\entity as usuarios;
use web\DateTimeLocal;
use web\Periodo;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


// Declarción de variables ******************************************************
$num_max_actividades = 200;

$mi_dele = core\ConfigGlobal::mi_delef();
$mi_sfsv = core\ConfigGlobal::mi_sfsv();

$oPosicion->recordar();

$Qcontinuar = (string)  filter_input(INPUT_POST, 'continuar');
// Sólo sirve para esta pagina: importar, publicar, duplicar
$QGstack = (integer)  filter_input(INPUT_POST, 'Gstack');
if (isset($_POST['stack'])) {
    $stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
} else {
    $stack = '';
}

//Si vengo de vuelta con el parámetro 'continuar', los datos no están en el POST,
// sino en $Posicion. Le paso la referecia del stack donde está la información.
if (!empty($Qcontinuar) && $Qcontinuar == 'si' && ($QGstack != '')) {
    $oPosicion->goStack($QGstack);
    $Qmodo = $oPosicion->getParametro('modo');
    //	$Qque = $oPosicion->getParametro('que');
    $Qstatus = $oPosicion->getParametro('status');
    $Qid_tipo_activ = $oPosicion->getParametro('id_tipo_activ');
    $Qfiltro_lugar = $oPosicion->getParametro('filtro_lugar');
    $Qid_ubi= $oPosicion->getParametro('id_ubi');
    $Qperiodo=$oPosicion->getParametro('periodo');
    $Qyear=$oPosicion->getParametro('year');
    $Qdl_org=$oPosicion->getParametro('dl_org');
    $Qempiezamin=$oPosicion->getParametro('empiezamin');
    $Qempiezamax=$oPosicion->getParametro('empiezamax');
    $Qfases_on =  $oPosicion->getParametro('fases_on');
    $Qfases_off =  $oPosicion->getParametro('fases_off');
    $Qid_sel=$oPosicion->getParametro('id_sel');
    $Qscroll_id = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($QGstack); //limpio todos los estados hacia delante.
    
    // valores por defeccto
    if (empty($Qperiodo)) {
        $Qperiodo = 'actual';
    }
    
} else { //si vengo de vuelta y tengo los parametros en el $_POST
    $Qid_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qscroll_id = (string) \filter_input(INPUT_POST, 'scroll_id');
    //Si vengo por medio de Posicion, borro la última
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel=$oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
    $Qmodo = (string) \filter_input(INPUT_POST, 'modo');
    $Qstatus = (integer) \filter_input(INPUT_POST, 'status');
    $Qid_tipo_activ = (string) \filter_input(INPUT_POST, 'id_tipo_activ');
    $Qfiltro_lugar = (string) \filter_input(INPUT_POST, 'filtro_lugar');
    $Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
    $Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
    $Qyear = (string) \filter_input(INPUT_POST, 'year');
    $Qdl_org = (string) \filter_input(INPUT_POST, 'dl_org');
    $Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');
    $Qfases_on = (array)  \filter_input(INPUT_POST, 'fases_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qfases_off = (array)  \filter_input(INPUT_POST, 'fases_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    
    // valores por defeccto
    if (empty($Qperiodo)) {
        $Qperiodo = 'actual';
    }
    
    $Qstatus = empty($Qstatus)? Actividad::STATUS_ACTUAL : $Qstatus;
    
    $aGoBack = array (
        'modo'=>$Qmodo,
        'id_tipo_activ'=>$Qid_tipo_activ,
        'id_ubi'=>$Qid_ubi,
        'periodo'=>$Qperiodo,
        'year'=>$Qyear,
        'dl_org'=>$Qdl_org,
        'status'=>$Qstatus,
        'empiezamin'=>$Qempiezamin,
        'empiezamax'=>$Qempiezamax ,
        'fases_on'=>$Qfases_on,
        'fases_off'=>$Qfases_off,
    );
    $oPosicion->setParametros($aGoBack,1);
}

// Condiciones de búsqueda.
$aWhere = array();
$aOperador = array();
// Status
if ($Qstatus != 9) {
    $aWhere['status'] = $Qstatus;
}
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
    $Qssfsv = (string) \filter_input(INPUT_POST, 'ssfsv');
    $Qsasistentes = (string) \filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string) \filter_input(INPUT_POST, 'sactividad');
    //$Qsnom_tipo = (string) \filter_input(INPUT_POST, 'snom_tipo');
    
    if (empty($Qssfsv)) {
        if ($mi_sfsv == 1) $Qssfsv = 'sv';
        if ($mi_sfsv == 2) $Qssfsv = 'sf';
    }
    $sasistentes = empty($Qsasistentes)? '.' : $Qsasistentes;
    $sactividad = empty($Qsactividad)? '.' : $Qsactividad;
    //$snom_tipo = empty($Qsnom_tipo)? '...' : $Qsnom_tipo;
    $oTipoActiv= new web\TiposActividades();
    $oTipoActiv->setSfsvText($Qssfsv);
    $oTipoActiv->setAsistentesText($sasistentes);
    $oTipoActiv->setActividadText($sactividad);
    $Qid_tipo_activ=$oTipoActiv->getId_tipo_activ();
} else {
    $oTipoActiv= new web\TiposActividades($Qid_tipo_activ);
    $ssfsv=$oTipoActiv->getSfsvText();
    $sasistentes=$oTipoActiv->getAsistentesText();
    $sactividad=$oTipoActiv->getActividadText();
    //$nom_tipo=$oTipoActiv->getNom_tipoText();
}
if ($Qid_tipo_activ!='......') {
    $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
    $aOperador['id_tipo_activ'] = '~';
}
// Lugar
if (!empty($Qid_ubi)) {
    $aWhere['id_ubi']=$Qid_ubi;
}

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);


$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();
$aWhere['f_ini'] = "'$inicioIso','$finIso'";
$aOperador['f_ini'] = 'BETWEEN';

// dl Organizadora.
if (!empty($Qdl_org)) {
    $aWhere['dl_org'] = $Qdl_org;
}
// Publicar
if (!empty($Qmodo) && $Qmodo == 'publicar') {
    $aWhere['publicado'] = 'f';
}

// miro que rol tengo. Si soy casa, sólo veo la mía
$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());

if (!empty($Qmodo) && $Qmodo != 'buscar') {
    $a_botones = [];
    if ($Qmodo == 'importar') {
        $a_botones[] = array( 'txt' => _("importar"),
            'click' =>"jsForm.update(\"#seleccionados\",\"importar\")" );
    }
    if ($Qmodo == 'publicar') {
        $a_botones[] = array( 'txt' => _("datos"),
            'click' =>"jsForm.mandar(\"#seleccionados\",\"datos\")" );
        $a_botones[] = array( 'txt' => _("publicar"),
            'click' =>"jsForm.update(\"#seleccionados\",\"publicar\")" );
    }
    if (core\configGlobal::is_app_installed('asignaturas')) {
        if ($_SESSION['oPerm']->have_perm_oficina('est')) {
            $a_botones[]=array( 'txt'=> _("asignaturas"), 'click'=>"jsForm.mandar(\"#seleccionados\",\"asig\")");
        }
    }
} else {
    if ($oMiUsuario->isRolePau('cdc') || $oMiUsuario->isRole('CentroSf')) {
        $a_botones=array( array( 'txt' => _("datos"), 'click' =>"jsForm.mandar(\"#seleccionados\",\"datos\")" ) ,);
    } else {
        $a_botones[] = array( 'txt' => _("datos"), 'click' =>"jsForm.mandar(\"#seleccionados\",\"datos\")" );
        if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) or ($_SESSION['oPerm']->have_perm_oficina('des'))) {
            $duplicar=1; //condición de duplicar
            $a_botones[]=array( 'txt'=> _("duplicar"), 'click' =>"jsForm.update(\"#seleccionados\",\"duplicar\")");
            // Ahora lo generalizo para todas. (no sólo proyecto). 17.X.2011
            $eliminar=1; //condición de eliminable
            $a_botones[]=array( 'txt'=> _("borrar"), 'click'=>"fnjs_borrar(\"#seleccionados\",\"eliminar\")");
        }
        
        if (core\configGlobal::is_app_installed('actividadcargos')) {
            $a_botones[] = array( 'txt' => _("cargos"), 'click' =>"jsForm.mandar(\"#seleccionados\",\"carg\")");
            $a_botones[] = array( 'txt' => _("lista cl"), 'click' =>"jsForm.mandar(\"#seleccionados\",\"listcl\")");
        }
        if (core\configGlobal::is_app_installed('asistentes')) {
            $a_botones[] = array( 'txt' => _("asistentes"), 'click' =>"jsForm.mandar(\"#seleccionados\",\"asis\")");
            $a_botones[] = array( 'txt' => _("lista"), 'click' =>"jsForm.mandar(\"#seleccionados\",\"list\")");
            //$a_botones[] = array( 'txt' => _("transferir sasistentes a históricos"), 'click' =>"jsForm.mandar(\"#seleccionados\",\"historicos\")");
        }
        if (core\configGlobal::is_app_installed('actividadplazas')) {
            $a_botones[] = array( 'txt' => _("plazas"), 'click' =>"jsForm.mandar(\"#seleccionados\",\"plazas\")");
        }
        if (core\configGlobal::is_app_installed('procesos')) {
            $a_botones[] = array( 'txt' => _("proceso"), 'click' =>"jsForm.mandar(\"#seleccionados\",\"proceso\")");
        }
        
        if (core\configGlobal::is_app_installed('asignaturas')) {
            if ($_SESSION['oPerm']->have_perm_oficina('est')) {
                $a_botones[]=array( 'txt'=> _("asignaturas"), 'click'=>"jsForm.mandar(\"#seleccionados\",\"asig\")");
            }
            if (($_SESSION['oPerm']->have_perm_oficina('est'))
                or ($_SESSION['oPerm']->have_perm_oficina('agd'))
                or ($_SESSION['oPerm']->have_perm_oficina('sm'))) {
                    $a_botones[]=array( 'txt'=>_("plan estudios"), 'click'=>"jsForm.mandar(\"#seleccionados\",\"plan_estudios\")");
                }
                if ($_SESSION['oPerm']->have_perm_oficina('est')) {
                    $a_botones[]=array( 'txt'=>_("listas de clase"), 'click'=>"jsForm.mandar(\"#seleccionados\",\"lista_clase\")");
                    $a_botones[]=array( 'txt'=>_("posibles asignaturas"), 'click'=>"jsForm.mandar(\"#seleccionados\",\"posibles_asignaturas\")");
                }
        }
    }
}

$a_cabeceras=array(
    array('name'=>_("inicio"),'width'=>40,'class'=>'fecha'),
    array('name'=>_("fin"),'width'=>40,'class'=>'fecha'),
    array('name'=>ucfirst(_("actividad")),'width'=>300,'formatter'=>'clickFormatter'),
    array('name'=>_("hora ini"),'width'=>40,'class'=>'fecha'),
    array('name'=>_("hora fin"),'width'=>40,'class'=>'fecha')
);
if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) or ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $a_cabeceras[]= array('name'=>_("sf/sv"),'width'=>40);
}
$a_cabeceras[]= array('name'=>_("tar."),'width'=>40);
if ( !$oMiUsuario->isRolePau('ctr') ) {
    $a_cabeceras[]= array('name'=>ucfirst(_("sacd")),'width'=>200);
    $a_cabeceras[]= array('name'=>_("dl org"),'width'=>50);
}
$a_cabeceras[]= ucfirst(_("centro"));
$a_cabeceras[]= ucfirst(_("observaciones"));

if (!empty($Qmodo) && $Qmodo == 'importar') {
    // actividades publicadas
    $mod = 'importar';
    $GesActividades = new GestorActividadPub();
    if (empty($Qdl_org)) {
        $aWhere['dl_org'] = $mi_dele;
        $aOperador['dl_org'] = '!=';
    }
    $GesImportada = new GestorImportada();
    $obj_pau = 'ActividadPub';
} else {
    //actividades de la dl más las importadas
    $mod = '';
    $GesActividades = new GestorActividad();
    $obj_pau = 'Actividad';
}

$aWhere['_ordre'] = 'f_ini';
$cActividades = $GesActividades->getActividades($aWhere,$aOperador);
$num_activ = count($cActividades);
if ($num_activ > $num_max_actividades && empty($Qcontinuar)) {
    $go_avant=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_select.php?'.http_build_query(array('continuar'=>'si','Gstack'=>$oPosicion->getStack())));
    $go_atras=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_que.php?'.http_build_query(array('stack'=>$oPosicion->getStack())));
    echo "<h2>".sprintf(_("son %s actividades a mostrar. ¿Seguro que quiere continuar?."),$num_activ).'</h2>';
    echo "<input type='button' onclick=fnjs_update_div('#main','".$go_avant."') value="._("continuar").">";
    echo "<input type='button' onclick=fnjs_update_div('#main','".$go_atras."') value="._("volver").">";
    die();
}

$i=0;
$sin=0;
$a_valores=array();
$sPrefs = '';
$id_usuario= core\ConfigGlobal::mi_id_usuario();
$tipo = 'tabla_presentacion';
$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>$tipo));
$sPrefs=$oPref->getPreferencia();
foreach($cActividades as $oActividad) {
    $id_activ = $oActividad->getId_activ();
    $id_tipo_activ = $oActividad->getId_tipo_activ();
    $nom_activ = $oActividad->getNom_activ();
    $dl_org = $oActividad->getDl_org();
    $oF_ini = $oActividad->getF_ini();
    $f_ini = $oActividad->getF_ini()->getFromLocal();
    $f_fin = $oActividad->getF_fin()->getFromLocal();
    $h_ini = $oActividad->getH_ini();
    $h_fin = $oActividad->getH_fin();
    $tarifa = $oActividad->getTarifa();
    $observ = $oActividad->getObserv();
    // Si es para importar, quito las que ya están importadas
    // y no miro permisos de procesos
    //echo "nom: $nom_activ<br>";
    if (!empty($Qmodo) && $Qmodo == 'importar') {
        $cImportadas = $GesImportada->getImportadas(array('id_activ'=>$id_activ));
        if ($cImportadas != false && count($cImportadas) > 0) continue;
        $oPermActividades = new PermisosActividadesTrue(core\ConfigGlobal::mi_id_usuario());
        $oPermActiv = $oPermActividades->getPermisoActual('datos');
        $oPermSacd =  $oPermActividades->getPermisoActual('sacd');
    } else {
        // mirar permisos.
        if(core\ConfigGlobal::is_app_installed('procesos')) {
            //mirar por la seleccion
            if (!empty($Qfases_on) OR !empty($Qfases_off)) {
                $gesActividadProcesoTarea = new GestorActividadProcesoTarea();
                $aFasesCompletadas = $gesActividadProcesoTarea->getFasesCompletadas($id_activ);
                if (!empty($Qfases_on)) {
                    foreach ($Qfases_on as $id_fase) {
                        if (!in_array($id_fase, $aFasesCompletadas)) {
                            // falta una fase -> otra actividad:
                            continue 2;
                        }
                    }
                }
                if (!empty($Qfases_off)) {
                    foreach ($Qfases_off as $id_fase) {
                        if (in_array($id_fase, $aFasesCompletadas)) {
                            // Hay un fase on que debería estar off -> otra actividad:
                            continue 2;
                        }
                    }
                }
            }
            $_SESSION['oPermActividades']->setActividad($id_activ,$id_tipo_activ,$dl_org);
            $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
            $oPermSacd = $_SESSION['oPermActividades']->getPermisoActual('sacd');
        }
    }
    $i++;
    
    $oTipoActividad= new web\TiposActividades($id_tipo_activ);
    $isfsv=$oTipoActividad->getSfsvId();
    // para ver el nombre en caso de la otra sección
    if ($mi_sfsv != $isfsv && !($_SESSION['oPerm']->have_perm_oficina('des')) ) {
        $ssfsv=$oTipoActividad->getSfsvText();
        $sactividad=$oTipoActividad->getActividadText();
        $nom_activ="$ssfsv $sactividad";
    }
    
    $ssfsv=$oTipoActividad->getSfsvText();
    $sasistentes=$oTipoActividad->getAsistentesText();
    $sactividad=$oTipoActividad->getActividadText();
    $nom_tipo=$oTipoActividad->getNom_tipoText();
    if (core\ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('ocupado') === false) { 
        // no tiene permisos ni para ver.
        $sin++;
        continue;
    }
    if (core\ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('ver') === false) {
        // sólo puede ver que està ocupado
        $a_valores[$i]['sel']='';
        $a_valores[$i]['select']='';
        $a_valores[$i][1]=$f_ini;
        $a_valores[$i][2]=$f_fin;
        $a_valores[$i][3]=sprintf(_( 'ocupado %s (%s-%s)'),$ssfsv,$f_ini,$f_fin);
        //$a_valores[$i][1]= array( 'ira'=>'x', 'valor'=>'ocupado');
        $a_valores[$i][4]='';
        $a_valores[$i][5]='';
        if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) or ($_SESSION['oPerm']->have_perm_oficina('des'))) {
            $a_valores[$i][6]=$ssfsv;
        }
        $a_valores[$i][7]='';
        if ( !$oMiUsuario->isRolePau('ctr') ) {
            $a_valores[$i][8]='';
            $a_valores[$i][9]='';
            $a_valores[$i][10]='';
            $a_valores[$i][11]='';
        } else {
            $a_valores[$i][8]='';
            $a_valores[$i][9]='';
        }
        
    } else {
        if (strlen($h_ini)) {$h_ini=substr($h_ini,0, (strlen($h_ini)-3));}
        if (strlen($h_fin)) {$h_fin=substr($h_fin,0, (strlen($h_fin)-3));}
        
        $oTarifa = new TipoTarifa($tarifa);
        $tarifa_letra= $oTarifa->getLetra();
        
        $sacds="";
        if(core\ConfigGlobal::is_app_installed('actividadessacd')) {
            // sólo si tiene permiso
            $aprobado = TRUE;
            if (ConfigGlobal::mi_sfsv() == 2) {
                $gesActividadProcesoTarea = new GestorActividadProcesoTarea();
                $aprobado = $gesActividadProcesoTarea->getSacdAprobado($id_activ);
            }
            if (!core\ConfigGlobal::is_app_installed('procesos')
                OR ($oPermSacd->have_perm_activ('ver') === true && $aprobado) )
            {
                $gesCargosActividad=new actividadcargos\model\entity\GestorActividadCargo();
                foreach($gesCargosActividad->getActividadSacds($id_activ) as $oPersona) {
                    $sacds.=$oPersona->getApellidosNombre()."# "; // la coma la utilizo como separador de apellidos, nombre.
                }
                $sacds=substr($sacds,0,-2);
            }
        }
        
        $ctrs = "";
        if(core\ConfigGlobal::is_app_installed('actividadescentro')) {
            $oEnc = new GestorCentroEncargado();
            $n = 0;
            foreach($oEnc->getCentrosEncargadosActividad($id_activ) as $oEncargado) {
                $n++;
                $ctrs .= $oEncargado->getNombre_ubi().", ";
            }
            $ctrs = (!empty($n))? substr($ctrs,0,-2) : '';
        }
        
        $a_valores[$i]['sel']="$id_activ#$nom_activ";
        // pongo un '*' al final del nombre si es una actividad de sg coincidente con sf.
        $con = '';
        $flag = 0;
        if(preg_match("/^[12][45]/",$id_tipo_activ)) {
            if(preg_match("/^[12][45]1/",$id_tipo_activ)) { // para los crt, sólo si es entre semana.
                /*
                 list($dini_0,$mini_0,$aini_0) = preg_split('/[\.\/-]/', $f_ini);
                 $w = date ('w',mktime(0,0,0,$mini_0,$dini_0,$aini_0));
                 */
                $w = $oF_ini->format('w');
                if ($w < 4) { // de domingo a miercoles.
                    $flag = 0;
                } else {
                    $flag = 1;
                }
            }
            if (empty($flag)) {
                $coincide = $GesActividades->getCoincidencia($oActividad,'bool');
                $con = ($coincide)? '*' : '';
            }
        }
        $a_valores[$i][1]=$f_ini;
        $a_valores[$i][2]=$f_fin;
        
        if ($Qmodo != 'importar') {
            if ($sPrefs == 'html') {
                $pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>'a','id_pau'=>$id_activ,'obj_pau'=>$obj_pau)));
                $a_valores[$i][3]= array( 'ira'=>$pagina, 'valor'=>$nom_activ.$con);
            } else {
                $pagina='jsForm.mandar("#seleccionados","dossiers")';
                $a_valores[$i][3]= array( 'script'=>$pagina, 'valor'=>$nom_activ.$con);
            }
        } else {
            $a_valores[$i][3] = $nom_activ.$con;
        }
        $a_valores[$i][4]=$h_ini;
        $a_valores[$i][5]=$h_fin;
        if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) or ($_SESSION['oPerm']->have_perm_oficina('des'))) {
            $a_valores[$i][6]=$ssfsv;
        }
        $a_valores[$i][7]=$tarifa_letra;
        if ( !$oMiUsuario->isRolePau('ctr') ) {
            $a_valores[$i][8]=$sacds;
            $a_valores[$i][9]=$dl_org;
            $a_valores[$i][10]=$ctrs;
            $a_valores[$i][11]=$observ;
        } else {
            $a_valores[$i][8]=$ctrs;
            $a_valores[$i][9]=$observ;
        }
    }
}
$num=$i;
if (!empty($a_valores)) {
    if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
    if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }
}

if (core\ConfigGlobal::is_app_installed('procesos')) {
    $resultado = sprintf( _("%s actividades encontradas (%s sin permiso)"),$num,$sin);
} else {
    $resultado = sprintf( _("%s actividades encontradas"),$num);
}
// Convertir las fechas inicio y fin a formato local:
$oF_qini = new DateTimeLocal($inicioIso);
$QinicioLocal = $oF_qini->getFromLocal();
$oF_qfin = new DateTimeLocal($finIso);
$QfinLocal = $oF_qfin->getFromLocal();
$resultado .= ' ' . sprintf(_("entre %s y %s"),$QinicioLocal,$QfinLocal);

$oHash = new web\Hash();
$oHash->setUrl('apps/actividades/controller/actividad_que.php');
$a_camposHidden = array(
    'modo' => $Qmodo,
    'id_tipo_activ' => $Qid_tipo_activ,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'status' => $Qstatus,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'filtro_lugar' => $Qfiltro_lugar,
    'fases_on' => $Qfases_on,
    'fases_off' => $Qfases_off,
);
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposNo('!modo!id_tipo_activ!id_ubi!periodo!year!dl_org!status!empiezamin!empiezamax!filtro_lugar!fases_on!fases_off');

$oHashSel = new web\Hash();
$oHashSel->setcamposForm('!mod!queSel!id_dossier');
$oHashSel->setcamposNo('continuar!sel!scroll_id!fases_on!fases_off');
$a_camposHiddenSel = array(
    'obj_pau' =>$obj_pau,
    'pau' =>'a',
    'permiso' =>'3',
    'Gstack' => $oPosicion->getStack(),
);
$oHashSel->setArraycamposHidden($a_camposHiddenSel);

$oTabla = new web\Lista();
$oTabla->setId_tabla('actividad_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$perm_nueva = FALSE;
if ( !$oMiUsuario->isRolePau('cdc') && !$oMiUsuario->isRolePau('ctr') ) {
    $perm_nueva = TRUE;
}

$aTiposActiv = [];
if (!empty($Qid_tipo_activ)) {
    $oTipoActiv2 = new web\TiposActividades($Qid_tipo_activ);
    $aTiposActiv = $oTipoActiv2->getArrayAsistentesIndividual();
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHashSel' => $oHashSel,
    'aTiposActiv' => $aTiposActiv,
    'resultado' => $resultado,
    'perm_nueva' => $perm_nueva,
    'mod' => $mod,
    'oTabla' => $oTabla,
];

$oView = new core\View('actividades/controller');
echo $oView->render('actividad_select.phtml',$a_campos);