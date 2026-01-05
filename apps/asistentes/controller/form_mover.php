<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\PosiblesCa;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_nom = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
}

$Qid_activ_old = (integer)filter_input(INPUT_POST, 'id_activ');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_pau');

// Asistencia para saber si puedo modificar:
$service = $GLOBALS['container']->get(AsistenteActividadService::class);
$AsistenteRepositoryInterface = $service->getRepoAsistente($Qid_nom, $Qid_activ_old);
$AsistenteRepository = $GLOBALS['container']->get($AsistenteRepositoryInterface);
$oAsistente = $AsistenteRepository->findById($Qid_activ_old, $Qid_nom);
if ($oAsistente->perm_modificar() === FALSE) {
    $aviso_txt = _("los datos de asistencia los modifica la dl del asistente");

    $a_campos = [
        'oPosicion' => $oPosicion,
        'aviso_txt' => $aviso_txt,
    ];
} else {
    $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
    $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
    $ActividadPlazasRepository = $GLOBALS['container']->get(ActividadPlazasRepositoryInterface::class);
    $service = $GLOBALS['container']->get(AsistenteActividadService::class);
    $mi_dele = ConfigGlobal::mi_delef();
    $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $mi_dele]);
    $oDelegacion = $cDelegaciones[0];
    $id_dl = $oDelegacion->getIdDlVo()->value();

    $oPosiblesCa = new PosiblesCa();
    //borrar el actual y poner la nueva
    $propietario = '';
    if (!empty($Qid_activ_old) && !empty($Qid_nom)) {
        $mod = "mover";

        //del mismo tipo que la anterior
        $oActividad = $ActividadAllRepository->findById($Qid_activ_old);
        $id_tipo = $oActividad->getId_tipo_activ();

        // IMPORTANT: Propietario del a plaza
        // si es de la sf quito la 'f'
        $dl = preg_replace('/f$/', '', $oActividad->getDl_org());
        $propietario = "$dl>$mi_dele";

        $oTipoActiv = new web\TiposActividades($id_tipo);
        $ssfsv = $oTipoActiv->getSfsvText();
        $sasistentes = $oTipoActiv->getAsistentesText();
        $sactividad = $oTipoActiv->getActividadText();

        //periodo
        switch ($sactividad) {
            case 'ca':
            case 'cv':
                $any = $_SESSION['oConfig']->any_final_curs('est');
                $oInicurs = core\curso_est("inicio", $any, "est");
                $oFincurs = core\curso_est("fin", $any, "est");
                break;
            case 'crt':
                $any = $_SESSION['oConfig']->any_final_curs('crt');
                $oInicurs = core\curso_est("inicio", $any, "crt");
                $oFincurs = core\curso_est("fin", $any, "crt");
                break;
        }
        $inicurs_iso = $oInicurs->format('Y-m-d');
        $fincurs_iso = $oFincurs->format('Y-m-d');

        //Actividades a las que afecta
        $aWhere = [];
        $aOperador = [];
        $aWhere['f_ini'] = "'$inicurs_iso','$fincurs_iso'";
        $aOperador['f_ini'] = 'BETWEEN';

        $aWhere['id_tipo_activ'] = '^' . $id_tipo;
        $aOperador['id_tipo_activ'] = '~';
        $aWhere['status'] = StatusId::ACTUAL;
        $aWhere['_ordre'] = 'f_ini';

        // todas las posibles.
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);

        if (ConfigGlobal::is_app_installed('actividadplazas')) {
            //primero las que se han pedido
            $cActividadesPreferidas = [];
            //Miro los actuales
            $PlazaPeticionRepository = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
            $cPlazasPeticion = $PlazaPeticionRepository->getPlazasPeticion(array('id_nom' => $Qid_nom, 'tipo' => $sactividad, '_ordre' => 'orden'));
            $sid_activ = '';
            foreach ($cPlazasPeticion as $oPlazaPeticion) {
                $id_activ = $oPlazaPeticion->getId_activ();
                $oActividad = $ActividadAllRepository->findById($id_activ);
                // Asegurar que es una actividad actual (No terminada)
                if ($oActividad->getStatus() !== StatusId::ACTUAL) {
                    continue;
                }
                // Asegurar que es una actividad del periodo
                $oF_ini = $oActividad->getF_ini();
                if ($oF_ini < $oInicurs) {
                    continue;
                }
                $cActividadesPreferidas[] = $oActividad;
            }

            if (!empty($cActividadesPreferidas)) {
                $cActividades = array_merge($cActividadesPreferidas, array('--------'), $cActividades);
            }
        }


        $propio = "t"; //valor por defecto
        $falta = "f"; //valor por defecto
        $est_ok = "f"; //valor por defecto
        $observ = ""; //valor por defecto
    }

    $aOpciones = [];
    $i = 0;
    foreach ($cActividades as $oActividad) {
        $i++;
        $id_activ = 0;
        $nom_activ = '--------------';
        $txt_plazas = '';
        $txt_creditos = '';
        // para el separador '-------'
        if (is_object($oActividad)) {
            $id_activ = $oActividad->getId_activ();
            if ($id_activ === $Qid_activ_old) {
                continue;
            }
            $nom_activ = $oActividad->getNom_activ();
            $dl_org = $oActividad->getDl_org();
            // plazas libres
            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $concedidas = 0;
                $cActividadPlazas = $ActividadPlazasRepository->getActividadesPlazas(array('id_dl' => $id_dl, 'id_activ' => $id_activ));
                foreach ($cActividadPlazas as $oActividadPlazas) {
                    $dl_tabla = $oActividadPlazas->getDl_tabla();
                    if ($dl_org === $dl_tabla) {
                        $concedidas = $oActividadPlazas->getPlazas();
                    }
                }
                $ocupadas = $service->getPlazasOcupadasPorDl($id_activ, $mi_dele);
                if ($ocupadas < 0) { // No se sabe
                    $libres = '-';
                } else {
                    $libres = $concedidas - $ocupadas;
                }
                if (!empty($concedidas)) {
                    $txt_plazas = sprintf(_("plazas libres/concedidas: %s/%s"), $libres, $concedidas);
                }
            }
            // creditos
            // por cada ca creo un array con las asignaturas y los créditos.
            $ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
            $aAsignaturasCa = $ActividadAsignaturaDlRepository->getAsignaturasCa($id_activ);

            $result = $oPosiblesCa->contar_creditos($Qid_nom, $aAsignaturasCa);
            $creditos = $result['suma'];
            if (!empty($creditos)) {
                $txt_creditos = sprintf(_("créditos: %s"), $creditos);
            }
        }
        $aOpciones[$id_activ] = "$nom_activ $txt_plazas  $txt_creditos";
    }

    $oDesplActividades = new Desplegable();
    $oDesplActividades->setNombre('id_activ');
    $oDesplActividades->setOpciones($aOpciones);

    $oHash = new Hash();
    $camposForm = 'observ!id_activ';
    $oHash->setCamposNo('falta!est_ok');
    $a_camposHidden = array(
        'id_nom' => $Qid_nom,
        'id_activ_old' => $Qid_activ_old,
        'mod' => $mod,
        'propio' => $propio,
        'plaza' => PlazaId::ASIGNADA,
        'propietario' => $propietario,
    );
    $oHash->setCamposForm($camposForm);
    $oHash->setArraycamposHidden($a_camposHidden);


    $a_campos = [
        'oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'oDesplActividades' => $oDesplActividades,
        'observ' => $observ,
        'aviso_txt' => '',
    ];
}


$oView = new ViewPhtml('asistentes\model');

$oView->renderizar('form_mover.phtml', $a_campos);