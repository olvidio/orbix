<?php

use core\ConfigGlobal;
use src\encargossacd\application\traits\EncargoFunciones;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/* Listado de ateción sacd. según cr 9/20, 10 
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

$Qsf = (integer)filter_input(INPUT_POST, 'sf');

$any = $_SESSION['oConfig']->any_final_curs('crt');
$inicurs = core\curso_est("inicio", $any, "crt")->getFromLocal();
$fincurs = core\curso_est("fin", $any, "crt")->getFromLocal();

$cabecera_left = sprintf(_("Curso:  %s - %s"), $inicurs, $fincurs);
$cabecera_right = ConfigGlobal::mi_delef();
$cabecera_right_2 = _("ref. cr 9/20, 10)");

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

$permiso_sf = '';
if ($Qsf === 1 && (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des')))) {
    $permiso_sf = "si";
}

// creo un array con los nombres de los grupos (zonas geográficas)
$ZonaGrupoRepository = $GLOBALS['container']->get(ZonaGrupoRepositoryInterface::class);
$cZonasGrupos = $ZonaGrupoRepository->getZonasGrupo(array('_ordre' => 'orden'));
foreach ($cZonasGrupos as $oZonaGrupo) {
    $id_grupo = $oZonaGrupo->getId_grupo();
    $nombre_grupo = $oZonaGrupo->getNombre_grupo();
    $array_grupos[$id_grupo] = $nombre_grupo;
}

// por cada zona
$all = [];
$EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
$EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
$PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
$ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
foreach ($cZonasGrupos as $oZonaGrupo) {
    $id_grupo = $oZonaGrupo->getId_grupo();
    $cZonas = $ZonaRepository->getZonas(array('id_grupo' => $id_grupo));
    //print_r($cZonas);
    $Html = '';
    $a_sacd = [];
    foreach ($cZonas as $oZona) {
        $id_zona = $oZona->getId_zona();
        $GesCentrosDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentrosDl = $GesCentrosDl->getCentros(array('id_zona' => $id_zona));
        foreach ($cCentrosDl as $oCentroDl) {
            $id_ubi = $oCentroDl->getId_ubi();

            $cPersonas = $PersonaDlRepository->getPersonas(array('id_ctr' => $id_ubi, 'situacion' => 'A', 'sacd' => 't', '_ordre' => 'apellido1,apellido2,nom'));
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
                $a_dedicacion = [];
                if (!empty($id_grupo)) {
                    $poblacion = $array_grupos[$id_grupo];
                } else {
                    $poblacion = _("otros");
                }
                /* busco los datos del encargo que se tengan */
                $aWhereT['id_nom'] = $id_nom;
                $aWhereT['f_fin'] = 'null';
                $aOperadorT['f_fin'] = 'IS NULL';
                $aWhereT['_ordre'] = 'modo';
                $cTareasSacd = $EncargoSacdRepository->getEncargosSacd($aWhereT, $aOperadorT);
                foreach ($cTareasSacd as $oTareaSacd) {
                    $modo = $oTareaSacd->getModo();
                    $id_enc = $oTareaSacd->getId_enc();
                    $oEncargo = $EncargoRepository->findById($id_enc);
                    $id_tipo_enc = $oEncargo->getId_tipo_enc();
                    $id_ubi_enc = $oEncargo->getId_ubi();
                    if (empty($id_ubi_enc)) {
                        $nombre_ubi = ''; // no tine encargo en ctr: descanso, estudio...
                    } else {
                        $iid = (string)$id_ubi_enc;
                        if ($iid[0] == 2) {
                            $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                            $oCentroEnc = $CentroEllasRepository->findById($id_ubi);
                        } else {
                            $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                            $oCentroEnc = $CentroDlRepository->findById($id_ubi_enc);
                        }
                        $nombre_ubi = $oCentroEnc->getNombre_ubi();
                    }
                    // horario
                    $dedicacion_txt = $oEncargoFunciones->dedicacion_horas($id_nom, $id_enc);
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
                            continue 2;
                            break;
                        case 5:
                            $modo_txt = "colaborador de";
                            break;
                    }
                    // 2021-10-15 Agrupar por sv / sf

                    if (empty($Qsf)) {
                        switch ($id_tipo_enc) {
                            case 1100:
                                //$sv_txt.=trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                $sv_txt .= "<td>" . trim("$nombre_ubi") . "</td><td>" . trim("$dedicacion_txt") . "</td>";
                                $a_dedicacion[3][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt];
                                break;
                            case 1300:
                                //$sssc_txt.=trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                $sssc_txt .= "<td>" . trim("$nombre_ubi") . "</td><td>" . trim("$dedicacion_txt") . "</td>";
                                $a_dedicacion[5][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt];
                                break;
                            case 3000:
                                $sv_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                $a_dedicacion[8][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt];
                                break;
                            case 5020:
                                //$sv_txt.=trim(", estudio: $nombre_ubi $dedicacion_txt");
                                $sv_txt .= "<td>" . _("estudio") . "</td><td>" . trim("$dedicacion_txt") . "</td>";
                                $a_dedicacion[1][$id_enc] = ['labor' => 'estudio', 'horas' => $dedicacion_txt];
                                break;
                            case 5030:
                                //$sv_txt.=trim(", descanso: $nombre_ubi $dedicacion_txt");
                                $sv_txt .= "<td>" . _("descanso") . "</td><td>" . trim("$dedicacion_txt") . "</td>";
                                $a_dedicacion[2][$id_enc] = ['labor' => 'descanso', 'horas' => $dedicacion_txt];
                                break;
                            case 6000:
                                $a_dedicacion[9][$id_enc] = ['labor' => 'otros', 'horas' => $dedicacion_txt];
                                break;
                            case 2100:
                                //$sv_txt.=trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                $sv_txt .= "<td>" . trim("$nombre_ubi") . "</td><td>" . trim("$dedicacion_txt") . "</td>";
                                $a_dedicacion[6][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt];
                                break;
                        }
                    } else {
                        switch ($id_tipo_enc) {
                            case 1200:
                                if ($permiso_sf === "si") {
                                    $sf_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                    $a_dedicacion[4][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt];
                                } else {
                                    switch ($modo) {
                                        case 3:
                                            //$sf_ctr[3]++;
                                            break;
                                        case 4:
                                        case 5:
                                            //$sf_ctr[4]++;
                                            break;
                                    }
                                }
                                break;
                            case 2200:
                                if ($permiso_sf === "si") {
                                    $sf_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                    $a_dedicacion[7][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt];
                                } else {
                                    switch ($modo) {
                                        case 3:
                                            //$sf_cgi[3]++;
                                            break;
                                        case 4:
                                        case 5:
                                            //$sf_cgi[4]++;
                                            break;
                                    }
                                }
                                break;
                        }
                    }
                }
                /* DESACTIVADO CARGOS
				// oficiales de dl.
				if ($of=oficial_dl($id_nom)) $sv_txt.="<br>".ConfigGlobal::$dele.": $of";
                */

                /*
                // sf
                if ($permiso_sf=="si") {
                    $sf_txt=substr($sf_txt,2);
                } else {
                    if ($sf_ctr[3]==1) {
                        $sf_txt.=", ".sprintf(_("%s centro sf"),$sf_ctr[3]);
                    } elseif ($sf_ctr[3]>1) {
                        $sf_txt.=", ".sprintf(_("%s centros sf"),$sf_ctr[3]);
                    }
                    if ($sf_ctr[4]==1) {
                        $sf_txt.=", ".sprintf(_("suplente de %s centro sf"),$sf_ctr[4]);
                    } elseif ($sf_ctr[4]>1) {
                        $sf_txt.=", ".sprintf(_("suplente de %s centros sf"),$sf_ctr[4]);
                    }
                    if ($sf_cgi[3]==1) {
                        $sf_txt.=", ".sprintf(_("atiende %s colegio sf"),$sf_cgi[3]);
                    } elseif ($sf_cgi[3]>1) {
                        $sf_txt.=", ".sprintf(_("atiende %s colegios sf"),$sf_cgi[3]);
                    }
                    if ($sf_cgi[4]==1) {
                        $sf_txt.=", ".sprintf(_("colabora con %s colegio sf"),$sf_cgi[4]);
                    } elseif ($sf_cgi[4]>1) {
                        $sf_txt.=", ".sprintf(_("colabora con %s colegios sf"),$sf_cgi[4]);
                    }

                    $sf_txt=substr($sf_txt,2);
                }
                */

                ksort($a_dedicacion);
                // para ordenar por apellidos, pero en toda la zona (no sólo el ctr), lo pongo en un array.
                //$a_sacd[$nom_orden] = "<tr><td class=centro>$nom_ap</td>$sv_txt $sssc_txt<td class=sf>$sf_txt</td></tr>";
                if ($Qsf === 1) {
                    if (!empty($a_dedicacion)) {
                        $a_sacd[$nom_orden] = ['nom' => $nom_ap, 'poblacion' => $poblacion, 'dedicacion' => $a_dedicacion];
                    }
                } else {
                    $a_sacd[$nom_orden] = ['nom' => $nom_ap, 'poblacion' => $poblacion, 'dedicacion' => $a_dedicacion];
                }


            }
        }
    }
    uksort($a_sacd, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.
    $all[$id_grupo] = $a_sacd;
}

//----------------- html --------------------
?>
<table>
    <tr>
        <td class=izquierda><?= $cabecera_left ?></td>
    </tr>
</table>
<?php
$html = '<table>';
foreach ($all as $id_grupo => $a_sacd) {
    foreach ($a_sacd as $nom_orden => $fila) {
        $html .= "<tr><td>" . $fila['nom'] . "</td><td>" . $fila['poblacion'] . "</td>";
        foreach ($fila['dedicacion'] as $num_orden => $a_dedi) {
            foreach ($a_dedi as $id_enc => $a_dedic) {
                $html .= "<td>" . $a_dedic['labor'] . "</td><td>" . $a_dedic['horas'] . "</td>";
            }
        }
        $html .= "</tr>";
    }
}
$html .= '</table>';
echo $html;
?>
