<?php

use core\ConfigGlobal;
use core\ViewTwig;
use encargossacd\model\EncargoFunciones;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargoSacd;
use personas\model\entity\GestorPersonaDl;
use web\DateTimeLocal;
use ubis\model\entity\CentroDl;
use ubis\model\entity\CentroEllas;
use ubis\model\entity\GestorCentroDl;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaGrupo;
use function core\strtoupper_dlb;

/* Listado de ateción sacd. según cr 9/05, Anexo2,9.4 c) 
*
*@package	delegacion
*@subpackage	des
*@author	Dani Serrabou
*@since		7/02/07.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oEncargoFunciones = new EncargoFunciones();

$any = $_SESSION['oConfig']->any_final_curs('crt');
$inicurs = core\curso_est("inicio", $any, "crt")->getFromLocal();
$fincurs = core\curso_est("fin", $any, "crt")->getFromLocal();

$cabecera_left = sprintf(_("Curso:  %s - %s"), $inicurs, $fincurs);
$cabecera_right = ConfigGlobal::mi_delef();
$cabecera_right_2 = _("ref. cr 1/14, 10, d)");

// ciudad de la dl
$oEncargoFunciones = new EncargoFunciones();
$poblacion = $oEncargoFunciones->getLugar_dl();
$oDateLocal = new DateTimeLocal();
$hoy_local = $oDateLocal->getFromLocal('.');
$lugar_fecha = "$poblacion, $hoy_local";

/* DESACTIVADO CARGOS
function oficial_dl($id_nom) {
	// Añadir los sacd que trabajan en la dl y en que departamento.
	$GesCargoCl = new GestorCargoCl();
	$cCargosCl = $GesCargoCl->getCargosCl(array('id_nom'=>$id_nom,'elencum'=>'8/6','f_cese'=>'null','_ordre'=>'cargo'),array('f_cese'=>'IS NULL'));
	$oficial = '';
	foreach($cCargosCl as $oCargoCl) {
		$cargo = $oCargoCl->getCargo();
		$oficial .= $cargo;
	}
	return $oficial;
}
*/

if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_sf = "si";
}


// creo un array con los nombres de los grupos (zonas geográficas)
$GesZonaGrupo = new GestorZonaGrupo();
$cZonasGrupos = $GesZonaGrupo->getZonasGrupo(array('_ordre' => 'orden'));
foreach ($cZonasGrupos as $oZonaGrupo) {
    $id_grupo = $oZonaGrupo->getId_grupo();
    $nombre_grupo = $oZonaGrupo->getNombre_grupo();
    $array_grupos[$id_grupo] = $nombre_grupo;
}

$Html_all = "<div class=salta_pag><table>";
// por cada zona
foreach ($cZonasGrupos as $oZonaGrupo) {
    $id_grupo = $oZonaGrupo->getId_grupo();
    $GesZonas = new GestorZona();
    $cZonas = $GesZonas->getZonas(array('id_grupo' => $id_grupo));
    //print_r($cZonas);
    $Html = '';
    $a_sacd = [];
    foreach ($cZonas as $oZona) {
        $id_zona = $oZona->getId_zona();
        $GesCentrosDl = new GestorCentroDl();
        $cCentrosDl = $GesCentrosDl->getCentros(array('id_zona' => $id_zona));
        foreach ($cCentrosDl as $oCentroDl) {
            $id_ubi = $oCentroDl->getId_ubi();
            $GesPersonas = new GestorPersonaDl();
            $cPersonas = $GesPersonas->getPersonas(array('id_ctr' => $id_ubi, 'situacion' => 'A', 'sacd' => 't', '_ordre' => 'apellido1,apellido2,nom'));
            // Bucle por cada sacd
            foreach ($cPersonas as $oPersonaNAgd) {
                $id_nom = $oPersonaNAgd->getId_nom();
                $nom_ap = $oPersonaNAgd->getNombreApellidosCrSin();
                $nom_orden = $oPersonaNAgd->getPrefApellidosNombre();
                $sv_txt = "";
                $sf_txt = "";
                $sssc_txt = "";
                $otros_txt = "";
                $sf_ctr = [];
                $sf_cgi = [];
                if (!empty($id_grupo)) {
                    $poblacion = $array_grupos[$id_grupo];
                } else {
                    $poblacion = _("otros");
                }
                /* busco los datos del encargo que se tengan */
                $GesTareasSacd = new GestorEncargoSacd();
                $aWhereT['id_nom'] = $id_nom;
                $aWhereT['f_fin'] = 'null';
                $aOperadorT['f_fin'] = 'IS NULL';
                $aWhereT['_ordre'] = 'modo';
                $cTareasSacd = $GesTareasSacd->getEncargosSacd($aWhereT, $aOperadorT);
                foreach ($cTareasSacd as $oTareaSacd) {
                    $modo = $oTareaSacd->getModo();
                    $id_enc = $oTareaSacd->getId_enc();
                    $oEncargo = new Encargo($id_enc);
                    $id_tipo_enc = $oEncargo->getId_tipo_enc();
                    $id_ubi_enc = $oEncargo->getId_ubi();
                    if (empty($id_ubi_enc)) {
                        $nombre_ubi = ''; // no tine encargo en ctr: descanso, estudio...
                    } else {
                        $iid = (string)$id_ubi_enc;
                        if ($iid[0] == 2) {
                            $oCentroEnc = new CentroEllas($id_ubi_enc);
                        } else {
                            $oCentroEnc = new CentroDl($id_ubi_enc);
                        }
                        $nombre_ubi = $oCentroEnc->getNombre_ubi();
                    }
                    // horario
                    $dedicacion_txt = $oEncargoFunciones->dedicacion($id_nom, $id_enc);
                    switch ($modo) {
                        case 1:
                            $modo_txt = "coordinador de";
                            break;
                        case 2:
                            $modo_txt = "cl de";
                            break;
                        case 3:
                            $modo_txt = "atención de";
                            break;
                        case 4:
                            $modo_txt = "suplente de";
                            // Si es suplente no hay que poner horario.
                            $dedicacion_txt = '';
                            break;
                        case 5:
                            $modo_txt = "colaborador de";
                            break;
                    }
                    switch ($id_tipo_enc) {
                        case 1100:
                            $sv_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                            break;
                        case 1200:
                            if ($permiso_sf == "si") {
                                $sf_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                            } else {
                                switch ($modo) {
                                    case 3:
                                        $sf_ctr[3]++;
                                        break;
                                    case 4:
                                    case 5:
                                        $sf_ctr[4]++;
                                        break;
                                }
                            }
                            break;
                        case 1300:
                            $sssc_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                            break;
                        case 2100:
                            $sv_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                            break;
                        case 2200:
                            if ($permiso_sf == "si") {
                                $sf_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                            } else {
                                switch ($modo) {
                                    case 3:
                                        $sf_cgi[3]++;
                                        break;
                                    case 4:
                                    case 5:
                                        $sf_cgi[4]++;
                                        break;
                                }
                            }
                            break;
                        case 3000:
                            $sv_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                            break;
                        case 5020:
                            $sv_txt .= trim(", estudio: $nombre_ubi $dedicacion_txt");
                            break;
                        case 5030:
                            $sv_txt .= trim(", descanso: $nombre_ubi $dedicacion_txt");
                            break;
                    }
                }
                $sv_txt = substr($sv_txt, 2);
                $sssc_txt = substr($sssc_txt, 2);
                /* DESACTIVADO CARGOS
                // oficiales de dl.
                if ($of=oficial_dl($id_nom)) $sv_txt.="<br>".ConfigGlobal::$dele.": $of";
                */
                // sf
                if ($permiso_sf == "si") {
                    $sf_txt = substr($sf_txt, 2);
                } else {
                    if ($sf_ctr[3] == 1) {
                        $sf_txt .= ", " . sprintf(_("%s centro sf"), $sf_ctr[3]);
                    } elseif ($sf_ctr[3] > 1) {
                        $sf_txt .= ", " . sprintf(_("%s centros sf"), $sf_ctr[3]);
                    }
                    if ($sf_ctr[4] == 1) {
                        $sf_txt .= ", " . sprintf(_("suplente de %s centro sf"), $sf_ctr[4]);
                    } elseif ($sf_ctr[4] > 1) {
                        $sf_txt .= ", " . sprintf(_("suplente de %s centros sf"), $sf_ctr[4]);
                    }
                    if ($sf_cgi[3] == 1) {
                        $sf_txt .= ", " . sprintf(_("atiende %s colegio sf"), $sf_cgi[3]);
                    } elseif ($sf_cgi[3] > 1) {
                        $sf_txt .= ", " . sprintf(_("atiende %s colegios sf"), $sf_cgi[3]);
                    }
                    if ($sf_cgi[4] == 1) {
                        $sf_txt .= ", " . sprintf(_("colabora con %s colegio sf"), $sf_cgi[4]);
                    } elseif ($sf_cgi[4] > 1) {
                        $sf_txt .= ", " . sprintf(_("colabora con %s colegios sf"), $sf_cgi[4]);
                    }
                    $sf_txt = substr($sf_txt, 2);
                }
                // para ordenar por apellidos, pero en toda la zona (no sólo el ctr), lo pongo en un array.
                $a_sacd[$nom_orden] = "<tr><td class=centro>$nom_ap</td></tr><tr><td>$sv_txt<br>$sssc_txt</td><td class=sf>$sf_txt</td></tr>";
            }
        }
    }
    uksort($a_sacd, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.
    foreach ($a_sacd as $nom_orden => $html) {
        $Html .= $html;
    }

    if (!empty($id_grupo)) {
        $poblacion = $array_grupos[$id_grupo];
    } else {
        $poblacion = _("otros");
    }
    $titulo_2 = strtoupper_dlb($poblacion);
    if (!empty($titulo_2)) echo "<tr><td class=poblacion colspan=2>$titulo_2</td></tr>";
    $Html_all .= $Html;
}

$a_campos = ['oPosicion' => $oPosicion,
    'cabecera_left' => $cabecera_left,
    'cabecera_right' => $cabecera_right,
    'cabecera_right_2' => $cabecera_right_2,
    'Html' => $Html_all,
];

$oView = new ViewTwig('encargossacd/controller');
$oView->renderizar('listas.html.twig', $a_campos);
