<?php

use core\ConfigGlobal;
use permisos\model\PermisosActividadesTrue;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use web\DateTimeLocal;
use web\Periodo;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/**
 * En teoría tendría que cambiar el orden de la lista de los centros encargados
 * de la actividad. Si orden és '+' (más importante), hago descender el orden un valor, y reordeno el resto de centros...
 */
function ordena($id_activ, $id_ubi, $orden): string
{
    $err_txt = '';
    $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
    $cCentrosEncargados = $CentroEncargadoRepository->getCentrosEncargados(array('id_activ' => $id_activ, '_ordre' => 'num_orden'));
    $i_max = count($cCentrosEncargados);
    for ($i = 0; $i < $i_max; $i++) {
        if ($cCentrosEncargados[$i]->getId_ubi() === $id_ubi) {
            $num_orden = $cCentrosEncargados[$i]->getNum_orden();
            switch ($orden) {
                case "mas":
                    if ($i >= 1) {
                        $oCentroEncargadoAnterior = $cCentrosEncargados[($i - 1)];
                        $anterior_num_orden = $oCentroEncargadoAnterior->getNum_orden();
                        $oCentroEncargadoAnterior->setNum_orden($num_orden);
                        if ($CentroEncargadoRepository->Guardar($oCentroEncargadoAnterior) === false) {
                            $err_txt .= _("Error al ordenar (1)");
                        }
                        $oCentroEncargadoActual = $cCentrosEncargados[($i)];
                        $oCentroEncargadoActual->setNum_orden($anterior_num_orden);
                        if ($CentroEncargadoRepository->Guardar($oCentroEncargadoActual) === false) {
                            $err_txt .= _("Error al ordenar (2)");
                        }
                    }
                    break;
                case "menos":
                    if ($i < ($i_max - 1)) {
                        $oCentroEncargadoPosterior = $cCentrosEncargados[($i + 1)];
                        $post_num_orden = $oCentroEncargadoPosterior->getNum_orden();
                        $oCentroEncargadoPosterior->setNum_orden($num_orden);
                        if ($CentroEncargadoRepository->Guardar($oCentroEncargadoPosterior) === false) {
                            $err_txt .= _("Error al ordenar (3)");
                        }
                        $oCentroEncargadoActual = $cCentrosEncargados[($i)];
                        $oCentroEncargadoActual->setNum_orden($post_num_orden);
                        if ($CentroEncargadoRepository->Guardar($oCentroEncargadoActual) === false) {
                            $err_txt .= _("Error al ordenar (4)");
                        }
                    }
                    break;
            }
        }
    }
    return $err_txt;
}

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');

$aWhere = [];
$aOperador = [];
$CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
switch ($Qque) {
    case "orden":
        $Qnum_orden = (string)filter_input(INPUT_POST, 'num_orden');
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        $error_txt = '';
        if ($Qnum_orden === "borrar") { //entonces es borrar:
            if ($Qid_activ && $Qid_ubi) {
                $oCentroEncargado = $CentroEncargadoRepository->findbyId($Qid_activ, $Qid_ubi);
                if ($CentroEncargadoRepository->Eliminar($oCentroEncargado) === false) {
                    $error_txt = _("hay un error, no se ha eliminado el centro");
                }
            } else {
                $error_txt = _("no sé cuál he de borrar");
            }
        } else {
            $error_txt = ordena($Qid_activ, $Qid_ubi, $Qnum_orden);
        }
        $error_txt = addslashes($error_txt ?? '');
        echo "{ \"que\": \"$Qque}\", \"txt\": \"\", \"error\": \"$error_txt\" }";
        break;
    case "get":
        // mirar permisos.
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
        $_SESSION['oPermActividades']->setActividad($Qid_activ, $Qid_tipo_activ, $Qdl_org);
        $oPermCtr = $_SESSION['oPermActividades']->getPermisoActual('ctr');

        $txt = '';
        if ($oPermCtr->have_perm_activ('ver') === true) { // sólo si tiene permiso
            // listado de centros encargados
            $cCtrsEncargados = $CentroEncargadoRepository->getCentrosEncargadosActividad($Qid_activ);
            $txt_ctr = '';

            foreach ($cCtrsEncargados as $oCentro) {
                $id_ubi = $oCentro->getId_ubi();
                $nombre_ubi = $oCentro->getNombre_ubi();
                $id_txt_ubi = $Qid_activ . "_" . $id_ubi;

                if ($oPermCtr->have_perm_activ('modificar') === true) { // sólo si tiene permiso para modificar
                    $txt_ctr .= "<span class=link id=$id_txt_ubi onclick=fnjs_cambiar_ctr(event,'$Qid_activ','$id_ubi')> $nombre_ubi;</span>";
                } else { // permiso para ver (si no tiene permisos ya estamos aqui)
                    $txt_ctr .= "<span> $nombre_ubi</span>";
                }
            }
            $txt_id = $Qid_activ . "_ctrs";
            $txt = "<td id=$txt_id>$txt_ctr</td>";
        }
        echo $txt;
        break;
    case "nuevo_sg":
        $Qinicio = (string)filter_input(INPUT_POST, 'inicio');
        $Qfin = (string)filter_input(INPUT_POST, 'fin');
        $Qf_ini_act = (string)filter_input(INPUT_POST, 'f_ini_act');

        $oDateIniAct = DateTimeLocal::createFromLocal($Qf_ini_act);
        $f_ini_act_iso = $oDateIniAct->getIso();

        $aWhere['active'] = 't';
        $aWhere['tipo_ctr'] = '^s[^s]*';
        $aWhere['_ordre'] = 'nombre_ubi';
        $aOperador['tipo_ctr'] = '~';
        $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
        $periodo = "f_ini BETWEEN '" . $Qinicio . "' AND '" . $Qfin . "'";
        $txt_ctr = '';
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            // número de actividades en periodo
            $cCtrsEncargados = $CentroEncargadoRepository->getActividadesDeCentros($id_ubi, $periodo);
            $num_activ = count($cCtrsEncargados);

            //próxima actividad
            $txt_dif = $CentroEncargadoRepository->getProximasActividadesDeCentro($id_ubi, $f_ini_act_iso);
            $txt_ctr .= "<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td>";
            $txt_ctr .= "<td>$num_activ</td><td>$txt_dif</td></tr>";
        }
        $txt = "<table><tr><td class=cabecera>" . _("centro") . "</td><td class=cabecera>" . _("num") . "</td><td class=cabecera>" . _("dif días") . "</td></tr>$txt_ctr</table>";
        echo $txt;
        break;
    case "nuevo_sr":
        $aWhere['_ordre'] = 'nombre_ubi';
        $aWhere['active'] = 't';
        $aWhere['tipo_labor'] = '512'; //sg -> 512
        $aOperador['tipo_labor'] = '&';
        $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
        $txt_ctr = '';
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $txt_ctr .= "<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
        }
        $txt = "<table><tr><td class=cabecera>" . _("centro") . "</td></tr>$txt_ctr</table>";
        echo $txt;
        break;
    case "nuevo_nagd":
        $aWhere['_ordre'] = 'nombre_ubi';
        $aWhere['active'] = 't';
        $aWhere['tipo_ctr'] = '^[na]';
        $aOperador['tipo_ctr'] = '~';
        $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
        $txt_ctr = '';
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $txt_ctr .= "<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
        }
        $txt = "<table><tr><td class=cabecera>" . _("centro") . "</td></tr>$txt_ctr</table>";
        echo $txt;
        break;
    case "nuevo_sssc":
        $aWhere['_ordre'] = 'nombre_ubi';
        $aWhere['active'] = 't';
        $aWhere['tipo_ctr'] = '^sss';
        $aOperador['tipo_ctr'] = '~';
        $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
        $txt_ctr = '';
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $txt_ctr .= "<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
        }
        $txt = "<table><tr><td class=cabecera>" . _("centro") . "</td></tr>$txt_ctr</table>";
        echo $txt;
        break;
    case "nuevo_sfsg":
        $aWhere['_ordre'] = 'nombre_ubi';
        $aWhere['active'] = 't';
        $aWhere['tipo_labor'] = '64'; //sg -> 64
        $aOperador['tipo_labor'] = '&';
        $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
        $txt_ctr = '';
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $txt_ctr .= "<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
        }
        $txt = "<table><tr><td class=cabecera>" . _("centro") . "</td></tr>$txt_ctr</table>";
        echo $txt;
        break;
    case "nuevo_sfsr":
        $aWhere['_ordre'] = 'nombre_ubi';
        $aWhere['active'] = 't';
        $aWhere['tipo_labor'] = '512'; //sg -> 512
        $aOperador['tipo_labor'] = '&';
        $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
        $txt_ctr = '';
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $txt_ctr .= "<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
        }
        $txt = "<table><tr><td class=cabecera>" . _("centro") . "</td></tr>$txt_ctr</table>";
        echo $txt;
        break;
    case "nuevo_sfnagd":
        $aWhere['_ordre'] = 'nombre_ubi';
        $aWhere['active'] = 't';
        $aWhere['tipo_ctr'] = '^[na]';
        $aOperador['tipo_ctr'] = '~';
        $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
        $txt_ctr = '';
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            //$txt_ctr.="<tr><td><span class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</span></td></tr>";
            $txt_ctr .= "<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
        }
        $txt = "<table><tr><td class=cabecera>" . _("centro") . "</td></tr>$txt_ctr</table>";
        echo $txt;
        break;
    case "asignar":
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        // miro si hay centros encargados, para poner num orden después.
        $aWhere['id_activ'] = $Qid_activ;
        $aWhere['_ordre'] = 'num_orden DESC';
        $cCentros = $CentroEncargadoRepository->getCentrosEncargados($aWhere);
        if (is_array($cCentros) && count($cCentros) >= 1) {
            $num_orden = $cCentros[0]->getNum_orden() + 1;
        } else {
            $num_orden = 0;
        }
        $oCentroEncargado = new CentroEncargado();
        $oCentroEncargado->setId_activ($Qid_activ);
        $oCentroEncargado->setId_ubi($Qid_ubi);
        $oCentroEncargado->setNum_orden($num_orden);
        $oCentroEncargado->setEncargo('organizador');
        if ($CentroEncargadoRepository->Guardar($oCentroEncargado) === false) {
            echo _("hay un error, no se ha guardado el cargo");
        }
        break;
    case 'lista_activ':

        $Qtipo = (string)filter_input(INPUT_POST, 'tipo');
        $Qyear = (integer)filter_input(INPUT_POST, 'year');
        $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
        $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
        $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

        // valores por defeccto
        if (empty($Qperiodo)) {
            $Qperiodo = 'actual';
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

        $aWhere['status'] = 3;
        $aOperador['status'] = "<";

        switch ($Qtipo) {
            case "sg":
                $aWhere['id_tipo_activ'] = '^1[45]';
                $aOperador['id_tipo_activ'] = '~';
                break;
            case "sr":
                $aWhere['id_tipo_activ'] = '^17';
                $aOperador['id_tipo_activ'] = '~';
                break;
            case "nagd":
                $aWhere['id_tipo_activ'] = '^1[13]';
                $aOperador['id_tipo_activ'] = '~';
                break;
            case "sfsg":
                $aWhere['id_tipo_activ'] = '^2[45]';
                $aOperador['id_tipo_activ'] = '~';
                break;
            case "sfsr":
                $aWhere['id_tipo_activ'] = '^27';
                $aOperador['id_tipo_activ'] = '~';
                break;
            case "sfnagd":
                $aWhere['id_tipo_activ'] = '^2[123]';
                $aOperador['id_tipo_activ'] = '~';
                break;
            case "sssc":
                $aWhere['id_tipo_activ'] = '^16';
                $aOperador['id_tipo_activ'] = '~';
                break;
        }
        $aWhere['_ordre'] = 'f_ini,nom_activ';

        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $cActividades = $ActividadDlRepository->getActividades($aWhere, $aOperador);

        $titulo = sprintf(_("listado de actividades %s"), $Qtipo);

        $a_cabeceras = [];
        $a_cabeceras[] = ucfirst(_("actividad"));
        $a_cabeceras[] = ucfirst(_("ctr encargados"));

        $CasaRepository = $GLOBALS['container']->get(CasaRepositoryInterface::class);
        $a_casas = $CasaRepository->getArrayCasas();

        $i = 0;
        $sin = 0;
        $a_valores = [];
        $a_NombreCasa = [];
        $a_FechaIni = [];
        foreach ($cActividades as $oActividad) {
            $i++;
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $dl_org = $oActividad->getDl_org();
            $nom_activ = $oActividad->getNom_activ();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();
            $id_ubi_actividad = $oActividad->getId_ubi();
            // mirar permisos.
            if (ConfigGlobal::is_app_installed('procesos')) {
                $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                $oPermCtr = $_SESSION['oPermActividades']->getPermisoActual('ctr');
            } else {
                $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
                $oPermActiv = $oPermActividades->getPermisoActual('datos');
                $oPermCtr = $oPermActividades->getPermisoActual('ctr');
            }

            if ($oPermActiv->have_perm_activ('ocupado') === false) {
                $sin++;
                continue;
            } // no tiene permisos ni para ver.
            if ($oPermActiv->have_perm_activ('ver') !== false) { // sólo puede ver que està ocupado
                $a_valores[$i][0] = $id_activ;
                $a_valores[$i][10] = $oPermCtr; // para no tener que recalcularlo después.

                $a_valores[$i][1] = $nom_activ;

                $cCtrsEncargados = $CentroEncargadoRepository->getCentrosEncargadosActividad($id_activ);
                $a_centros = [];
                if ($oPermCtr->have_perm_activ('ver') === true) { // sólo si tiene permiso
                    foreach ($cCtrsEncargados as $oCentro) {
                        $id_ubi = $oCentro->getId_ubi();
                        $nombre_ubi = $oCentro->getNombre_ubi();
                        $a_centros[] = array('nombre_ubi' => $nombre_ubi, 'id_ubi' => $id_ubi);
                    }
                }
                $a_valores[$i][2] = $a_centros;
                $a_valores[$i][3] = $f_ini;
                $a_valores[$i][4] = $f_fin;

                if (empty($id_ubi_actividad) || $id_ubi_actividad === 1) {
                    $nombre_ubi_actividad = 'z';
                } else {
                    $nombre_ubi_actividad = $a_casas[$id_ubi_actividad];
                }
                $a_NombreCasa[$i] = $nombre_ubi_actividad;
                $a_FechaIni[$i] = $oActividad->getF_ini()->getIso();
            }
        }
        // ordenar
        if (!empty($a_valores)) {
            array_multisort(
                    $a_FechaIni, SORT_STRING,
                    $a_NombreCasa, SORT_STRING,
                    $a_valores);
        }

        ?>

        <h3><?= $titulo ?></h3>
        <table>
            <tr>
                <?php
                foreach ($a_cabeceras as $cabecera) {
                    echo "<td>$cabecera</td>";
                }
                ?>
                <td id="lst_ctr">
                <td>
            </tr>
            <?php
            foreach ($a_valores as $valores) {
                $oPermCtr = $valores[10];
                $id_activ = $valores[0];
                $f_ini = $valores[3];
                $f_fin = $valores[4];
                $txt_ctr = "";
                if (is_array($valores[2])) {
                    foreach ($valores[2] as $a_centro) {
                        $id_ubi = $a_centro['id_ubi'];
                        $id_txt_ubi = $id_activ . "_" . $id_ubi;
                        if ($oPermCtr->have_perm_activ('modificar') === true) { // sólo si tiene permiso para modificar
                            $txt_ctr .= "<span class=link id=$id_txt_ubi onclick=fnjs_cambiar_ctr(event,'$id_activ','$id_ubi')> {$a_centro['nombre_ubi']};</span>";
                        } else { // permiso para ver (si no tiene permisos el valor($valores[2]) ya está en blanco)
                            $txt_ctr .= "<span> {$a_centro['nombre_ubi']};</span>";
                        }
                    }
                }
                $txt_id = $valores[0] . "_ctrs";
                if ($oPermCtr->have_perm_activ('crear') === true) { // sólo si tiene permiso para crear
                    $nuevo_txt = "<span class=link onclick=fnjs_nuevo_ctr(event,'$id_activ','$inicioIso','$finIso','$f_ini','$f_fin')>nuevo</span>";
                } else {
                    $nuevo_txt = '';
                }
                echo "<tr id=$valores[0]><td>$valores[1]</td><td id=$txt_id>$txt_ctr</td><td>$nuevo_txt</td></tr>";
            }
            ?>
        </table>
        <?php
        break;
}
