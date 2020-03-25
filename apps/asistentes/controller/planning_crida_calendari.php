<?php
use actividadcargos\model\GestorCargoOAsistente;
use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use personas\model\entity as personas;
use ubis\model\entity as ubis;
use web\Periodo;
/**
 * Esta página tiene la misión de realizar la llamada a calendario php;
 * y lo hace con distintos valores, en función de las páginas anteriores
 *
 *@param string $tipo planning-> de un grupo de personas n o agd.
 *					p_de_paso-> de un grupo de personas de paso.
 *					ctr-> de las personas de un ctr.
 *					planning_ctr->  de las personas de un ctr.
 *					planning_cdc-> actividades que se realizan en una casa del a dl.
 *
 *@package	delegacion
 *@subpackage	actividades
 *@author	Daniel Serrabou
 *@since		15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$aid_nom = array();
if (!empty($a_sel)) { //vengo de un checkbox
    // puede ser más de uno
    if (is_array($a_sel) && count($a_sel) > 1) {
        foreach ($a_sel as $nom_sel) {
            $aid_nom[] = $nom_sel;
        }
    } else {
        $aid_nom[] = $a_sel[0];
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel',$a_sel,1);
        $scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id',$scroll_id,1);
    }
}

$Qmodelo = (integer) \filter_input(INPUT_POST, 'modelo');
switch($Qmodelo) {
    case 2:
        $print = 1;
    case 1:
        include_once(core\ConfigGlobal::$dir_estilos.'/calendario.css.php');
        //include_once('apps/web/calendario.php');
        break;
    case 3:
        include_once(core\ConfigGlobal::$dir_estilos.'/calendario_grid.css.php');
        include_once('apps/web/calendario_grid.php');
        break;
}
// para los estilos. Las variables están en la página css.
$oPlanning = new web\Planning();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);

$Qcdc_sel = (integer) \filter_input(INPUT_POST, 'cdc_sel');
$Qtipo = (string) \filter_input(INPUT_POST, 'tipo');
$Qdd = (integer) \filter_input(INPUT_POST, 'dd');
$Qyear = (integer) \filter_input(INPUT_POST, 'year');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicio_iso = $oPeriodo->getF_ini_iso();
$fin_iso = $oPeriodo->getF_fin_iso();
$oIniPlanning = $oPeriodo->getF_ini();
$oFinPlanning = $oPeriodo->getF_fin();
$inicio_local = $oIniPlanning->getFromLocal();

// valores por defecto.
//divisiones por día
if (empty($Qdd) || (($Qdd<>1) AND ($Qdd<>3))) {
    $Qdd=3;
}
$mod=0; // 0 u otro valor (1 ver, 2 modificar, 3 eliminar..) el valor se pasa a la página link.
$nueva=0; // 0 o 1 para asignar una nueva actividad.
// mostrar encabezados arriba y abajo; derecha e izquierda.
if (empty($print)) { $doble=1; } else { $doble=0; }
// si es sólo un mes tampoco pongo doble (cabecera y pie)
$interval = $oFinPlanning->diff($oIniPlanning)->format('%m');
if ($interval < 2) $doble=0;

switch ($Qtipo) {
    case 'planning':
    case 'p_de_paso':
        $aWhere = [];
        $aOperador = [];
        $cabecera=ucfirst(_("persona seleccionada"));
        $oGesPersonas = new personas\GestorPersonaDl();
        $aWhere['id_nom'] = implode(',',$aid_nom);
        $aOperador['id_nom'] = 'OR';
        $cPersonas = $oGesPersonas->getPersonas($aWhere,$aOperador);
        break;
    case 'ctr':
        $aWhereP = [];
        $Qid_ubi = (string) \filter_input(INPUT_POST, 'id_ubi');
        if (!empty($Qid_ubi)) {
            $id_ubi = (integer) strtok($Qid_ubi,'#');
            $nombre_ubi = (string) strtok('#');
            $cabecera=ucfirst(sprintf(_("personas de: %s"),$nombre_ubi));
            $GesPersonas = new personas\GestorPersonaDl();
            $aWhereP['id_ctr'] = $id_ubi;
            $cPersonas = $GesPersonas->getPersonasDl($aWhereP);
        }
        break;
    case 'planning_ctr':
        $aWhere = [];
        $aWhereP = array('situacion'=>'A');
        $Qsacd = (string) \filter_input(INPUT_POST, 'sacd');
        $Qctr = (string) \filter_input(INPUT_POST, 'ctr');
        if (empty($Qsacd)) { $aWhereP['sacd']='f'; }
        if (!empty($Qctr)) {
            $nom_ubi = str_replace("+", "\+", $Qctr); // para los centros de la sss+
            $aWhere['nombre_ubi']='^'.$nom_ubi;
            $aOperador['nombre_ubi'] = 'sin_acentos';
            $GesCentros = new ubis\GestorCentroDl();
            $cCentros = $GesCentros->getCentros($aWhere,$aOperador);
            $cPersonas = []; // para unir todas las personas de más de un centro.
            $GesPersonas = new personas\GestorPersonaDl();
            foreach($cCentros as $oCentro) {
                $id_ubi = $oCentro->getId_ubi();
                $nombre_ubi = $oCentro->getNombre_ubi(); 
                $cabecera=ucfirst(sprintf(_("personas de: %s"),$nombre_ubi));
                $aWhereP['id_ctr'] = $id_ubi;
                $aWhereP['_ordre'] = 'apellido1';
                $cPersonas2 = $GesPersonas->getPersonas($aWhereP);
                if (is_array($cPersonas2) && count($cPersonas2)>=1) {
                    if (is_array($cPersonas)) {
                        $cPersonas = array_merge($cPersonas,$cPersonas2);
                    } else {
                        $cPersonas = $cPersonas2;
                    }
                }
            }
        } else {
            $cabecera=ucfirst(_("centros"));
            $Qtodos_n = (string) \filter_input(INPUT_POST, 'todos_n');
            $Qtodos_agd = (string) \filter_input(INPUT_POST, 'todos_agd');
            $Qtodos_s = (string) \filter_input(INPUT_POST, 'todos_s');
            // Pro defecto los 'n':
            $aWhereP['id_tabla']='n';
            if (!empty($Qtodos_n)) $aWhereP['id_tabla']='n';
            if (!empty($Qtodos_agd)) $aWhereP['id_tabla']='a';
            if (!empty($Qtodos_s)) $aWhereP['id_tabla']='s';
            $aWhereP['_ordre'] = 'id_ctr, apellido1';
            $GesPersonas = new personas\GestorPersonaDl();
            $cPersonas = $GesPersonas->getPersonas($aWhereP);
            $buscar_ctr=1;
            $aListaCtr=array();
        }
        break;
    case 'planning_cdc':
        $cabecera=ucfirst(_("planning de casas"));
        break;
    case 'casa':
        $cabecera=ucfirst(_("planning de casas"));
        break;
}

$GesActividades = new actividades\GestorActividad();

if ($Qtipo=='planning_cdc' || $Qtipo=='casa') {
    $Qsin_activ = (string) \filter_input(INPUT_POST, 'sin_activ');
    if (!empty($Qsin_activ) && $Qsin_activ == 1) { $sin_activ = 1; } else { $sin_activ = 0; } //Para dibujar caudricula aunque no tenga actividades.
    if ($Qcdc_sel < 10) { //Para buscar por casas.
        $aWhere=array();
        $aOperador=array();
        switch ($Qcdc_sel) {
            case 1:
                $aWhere['sv']='t';
                $aWhere['sf']='t';
                break;
            case 2:
                $aWhere['sv']='f';
                $aWhere['sf']='t';
                break;
            case 3: // casas comunes: cdr + dlb + sf +sv
                $aWhere['sv']='t';
                $aWhere['sf']='t';
                $aWhere['tipo_ubi']='cdcdl';
                $aWhere['tipo_casa']='cdc|cdr';
                $aOperador['tipo_casa']='~';
                break;
            case 4:
                $aWhere['sv']='t';
                break;
            case 5:
                $aWhere['sf']='t';
                break;
            case 6:
                $aWhere['sf']='t';
                // también los centros que son como cdc
                $GesCentrosSf = new ubis\GestorCentroEllas();
                $cCentrosSf = $GesCentrosSf->getCentrosSf(array('cdc'=>'t','_ordre'=>'nombre_ubi'));
                break;
            case 9:
                // posible selección múltiple de casas
                $a_id_cdc = (array)  \filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                if (!empty($a_id_cdc)) {
                    $aWhere['id_ubi'] = '^'. implode('$|^',$a_id_cdc) .'$';
                    $aOperador['id_ubi'] = '~';
                }
                break;
        }
        $aWhere['_ordre']='nombre_ubi';
        $GesCasaDl = new ubis\GestorCasaDl();
        $cCasasDl = $GesCasaDl->getCasas($aWhere,$aOperador);
        
        if ($Qcdc_sel==6) { //añado los ctr de sf
            foreach ($cCentrosSf as $oCentroSf) {
                array_push($cCasasDl, $oCentroSf);
            }
        }
        
        $p=0;
        $cdc = [];
        $a_actividades = [];
        foreach ($cCasasDl as $oCasaDl) {
            $a_cdc=array();
            $id_ubi=$oCasaDl->getId_ubi();
            $nombre_ubi=$oCasaDl->getNombre_ubi();
            
            $cdc[$p]="u#$id_ubi#$nombre_ubi";
            
            $a_cdc = $GesActividades->actividadesDeUnaCasa($id_ubi,$oIniPlanning,$oFinPlanning,$Qcdc_sel);
            if ($a_cdc !== false) {
                $a_actividades[$nombre_ubi]=array($cdc[$p]=>$a_cdc);
                $p++;
            } elseif ($sin_activ == 1) {
                $a_actividades[$nombre_ubi]=array($cdc[$p]=>array());
                $p++;
            }
        }
        ksort($a_actividades);
        /*
         lo que sigue es para que nos represente una linea en blanco al final:
         esto permite visualizar correctamente las 3 divisiones en los días
         en que todas las casas están ocupadas.
         */
        $cdc[$p+1]="##";
        $a_actividades[]=array($cdc[$p+1]=>array());
    } else { // cdc_sel > 10 Para buscar por actividades (todas).
        // busco todas las actividades del periodo y las agrupo por ubis.
        $oGesActividades = new actividades\GestorActividad();
        $aWhere=array();
        $aOperador=array();
        switch ($Qcdc_sel) {
            case 11:
                $aWhere['id_tipo_activ']='^1';
                $aOperador['id_tipo_activ']='~';
                break;
            case 12:
                $aWhere['id_tipo_activ']='^2';
                $aOperador['id_tipo_activ']='~';
                break;
        }
        $aWhere['f_ini']="'$fin_iso'";
        $aOperador['f_ini']='<=';
        $aWhere['f_fin']="'$inicio_iso'";
        $aOperador['f_fin']='>=';
        $aWhere['status']=4;
        $aOperador['status']='<';
        $aWhere['_ordre']= 'id_ubi';
        
        $aUbis = $oGesActividades->getUbis($aWhere,$aOperador);
        $p=0;
        $a_actividades=array();
        foreach ($aUbis as $id_ubi) {
            $a_cdc=array();
            if (empty($id_ubi)) {
                $nombre_ubi= _("por determinar");
                $cdc[$p]="u#2#$nombre_ubi"; // hay que poner un id_ubi para que vaya bien la función de dibujar el calendario.
            } elseif ($id_ubi == 1) {
                $nombre_ubi= _("otros lugares");
                $cdc[$p]="u#$id_ubi#$nombre_ubi";
            } else {
                $oCasa = ubis\Ubi::NewUbi($id_ubi);
                $id_ubi=$oCasa->getId_ubi();
                $nombre_ubi=$oCasa->getNombre_ubi();
                $cdc[$p]="u#$id_ubi#$nombre_ubi";
            }
            $a_cdc = $GesActividades->actividadesDeUnaCasa($id_ubi,$oIniPlanning,$oFinPlanning,$Qcdc_sel);
            if ($a_cdc !== false) {
                $a_actividades[$nombre_ubi]=array($cdc[$p]=>$a_cdc);
                $p++;
            } elseif ($sin_activ == 1) {
                $a_actividades[$nombre_ubi]=array($cdc[$p]=>array());
                $p++;
            }
        }
        ksort($a_actividades);
        /*
         lo que sigue es para que nos represente una linea en blanco al final:
         esto permite visualizar correctamente las 3 divisiones en los días
         en que todas las casas están ocupadas.
         */
        $cdc[$p+1]="##";
        $a_actividades[]=array($cdc[$p+1]=>array());
    }
} else {
    $GesActividadAsignaturas = new actividadestudios\GestorActividadAsignaturaDl();
    $aWhere = array('f_ini' => "'$inicio_iso','$fin_iso'");
    $aOperador = array('f_ini' => 'BETWEEN');
    $GesActividadAsignaturas->getActividadAsignaturas($aWhere,$aOperador);
    //por cada persona busco las actividades.
    $p=0;
    $persona = [];
    $a_actividades = [];
    $a_actividades2 = [];
    foreach ($cPersonas as $oPersona) {
        $aActivPersona=array();
        $id_nom=$oPersona->getId_nom();
        $nombre=$oPersona->getApellidosNombre();
        
        if (!empty($buscar_ctr)) {
            $id_ubi=$oPersona->getId_ctr();
            if (empty($id_ubi)) {
                $nombre_ubi="centro?";
            } elseif (!in_array($id_ubi,$aListaCtr)) {
                $oCentro = new ubis\CentroDl($id_ubi);
                $nombre_ubi = $oCentro->getNombre_ubi();
                $aListaCtr[$id_ubi]=$nombre_ubi;
            } else {
                $nombre_ubi=$aListaCtr[$id_ubi];
            }
            $persona[$p]="p#$id_nom#$nombre#$nombre_ubi";
        } else {
            $persona[$p]="p#$id_nom#$nombre";
        }
        
        // Seleccionar sólo las del periodo y actuales o terminadas
        $aWhere=array();
        $aOperador = [];
        $aWhere['f_ini']="'$fin_iso'";
        $aOperador['f_ini']='<=';
        $aWhere['f_fin']="'$inicio_iso'";
        $aOperador['f_fin']='>=';
        $aWhere['status']='2,3';
        $aOperador['status']='BETWEEN';
        
        if (core\ConfigGlobal::is_app_installed('actividadcargos')) {
            $GesCargoOAsistente = new GestorCargoOAsistente();
            $cCargoOAsistente = $GesCargoOAsistente->getCargoOAsistente($id_nom,$aWhere,$aOperador);
        } else {
            //$oGesAsistentes = new asistentes\GestorActividadCargo();
            echo "ja veurem...";
        }
        foreach ($cCargoOAsistente as $oCargoOAsistente) {
            $id_activ=$oCargoOAsistente->getId_activ();
            $propio = $oCargoOAsistente->getPropio();
            
            $aWhere['id_activ']=$id_activ;
            $cActividades = $GesActividades->getActividades($aWhere,$aOperador);
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
            
            $oTipoActividad = new web\TiposActividades($id_tipo_activ);
            $ssfsv=$oTipoActividad->getSfsvText();
            
            //para el caso de que la actividad comience antes
            //del periodo de inicio obligo a que tome una hora de inicio
            //en el entorno de las primeras del día (a efectos del planning
            //ya es suficiente con la 1:16 de la madrugada)
            if ($oIniPlanning>$oF_ini) {
                $ini=$inicio_local;
                $hini="1:16";
            } else {
                $ini=(string) $oF_ini->getFromLocal();
                $hini=(string) $h_ini;
            }
            $fi= (string) $oF_fin->getFromLocal();
            $hfi=(string) $h_fin;
            
            // mirar permisos.
            if(core\ConfigGlobal::is_app_installed('procesos')) {
                $_SESSION['oPermActividades']->setActividad($id_activ,$id_tipo_activ,$dl_org);
                $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                
                if ($oPermActiv->have_perm_activ('ocupado') === false) continue; // no tiene permisos ni para ver.
                if ($oPermActiv->have_perm_activ('ver') === false) { // sólo puede ver que està ocupado
                    $nom_curt= $ssfsv;
                    $nom_llarg= "$ssfsv ($ini-$fi)";
                } else {
                    $nom_curt=$oTipoActividad->getAsistentesText()." ".$oTipoActividad->getActividadText();
                    $nom_llarg=$nom_activ;
                }
            } else {
                $nom_curt=$oTipoActividad->getAsistentesText()." ".$oTipoActividad->getActividadText();
                $nom_llarg=$nom_activ;
            }
            
            $aActivPersona[]=array(
                'nom_curt'=>$nom_curt,
                'nom_llarg'=>$nom_llarg,
                'f_ini'=>$ini,
                'h_ini'=>$hini,
                'f_fi'=>$fi,
                'h_fi'=>$hfi,
                'id_tipo_activ'=>$id_tipo_activ,
                'pagina'=>'',
                'id_activ'=>$id_activ,
                'propio'=>$propio
            );
        }
        // En los profesores, añado las clases del stgr en actividades
        /*
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
        if (!empty($buscar_ctr)) {
            $a_actividades2[$nombre_ubi][]=array($persona[$p]=>$aActivPersona);
        } else {
            $a_actividades[]=array($persona[$p]=>$aActivPersona);
        }
        $p++;
    }
}//fin del else

    // En el caso de personas doy la opción de volver a los seleccionados.
    //if ($Qtipo=='planning' || $Qtipo=='p_de_paso' ) {
echo $oPosicion->mostrar_left_slide(1);

$oPlanning->setDd($Qdd);
$oPlanning->setCabecera($cabecera);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);
$oPlanning->setActividades($a_actividades);
$oPlanning->setMod($mod);
$oPlanning->setNueva($nueva);
$oPlanning->setDoble($doble);

// Listo varios centros.
if (!empty($buscar_ctr)) {
    $act=0;
    uksort($a_actividades2, "strnatcasecmp"); // case insensitive
    foreach( $a_actividades2 as $nombre_ubi=>$a_actividades ) {
        $cabecera=$nombre_ubi;
        /*
         lo que sigue es para que nos represente una linea en blanco al final:
         esto permite visualizar correctamente las 3 divisiones en los días
         en que todas las casas están ocupadas.
         */
        $a_actividades[]=array('###'=>array());
        $oPlanning->setCabecera($cabecera);
        $oPlanning->setActividades($a_actividades);
        echo $oPlanning->dibujar();
        $act++;
    }
} else {
    echo $oPlanning->dibujar();
}