<?php

use permisos\model\MyCrypt;
use procesos\model\PermAfectados;
use procesos\model\entity\GestorPermUsuarioActividad;
use procesos\model\entity\PermUsuarioActividad;
use usuarios\model\entity\GestorUsuario;
use usuarios\model\entity\Grupo;
use usuarios\model\entity\GrupoOUsuario;
use usuarios\model\entity\PermMenu;
use usuarios\model\entity\PermUsuarioCentro;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'perm_ctr_update':
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
        $Qid_ctr = (integer)filter_input(INPUT_POST, 'id_ctr');
        $Qperm_ctr = (integer)filter_input(INPUT_POST, 'perm_ctr');

        $oUsuarioPermCtr = new PermUsuarioCentro(array('id_item' => $Qid_item));
        $oUsuarioPermCtr->setId_usuario($Qid_usuario);
        $oUsuarioPermCtr->setId_ctr($Qid_ctr);
        $oUsuarioPermCtr->setPerm_ctr($Qperm_ctr);
        if ($oUsuarioPermCtr->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oUsuarioPermCtr->getErrorTxt();
        }
        break;
    case 'perm_ctr_eliminar':
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) { //vengo de un checkbox
            $Qid_usuario = (integer)strtok($a_sel[0], "#");
            $Qid_item = (integer)strtok("#");
        }
        $oUsuarioPermCtr = new PermUsuarioCentro(array('id_item' => $Qid_item));
        if ($oUsuarioPermCtr->DBEliminar() === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $oUsuarioPermCtr->getErrorTxt();
        }
        break;
    case 'perm_menu_update':
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
        $Qmenu_perm = (array)filter_input(INPUT_POST, 'menu_perm', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $oUsuarioPerm = new PermMenu(array('id_item' => $Qid_item));
        $oUsuarioPerm->setId_usuario($Qid_usuario);
        //cuando el campo es menu_perm, se pasa un array que hay que convertirlo en número.
        if (!empty($Qmenu_perm)) {
            $byte = 0;
            foreach ($Qmenu_perm as $bit) {
                $byte = $byte + $bit;
            }
            $oUsuarioPerm->setMenu_perm($byte);
        }
        if ($oUsuarioPerm->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oUsuarioPerm->getErrorTxt();
        }
        break;
    case 'perm_menu_eliminar':
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) { //vengo de un checkbox
            $Qid_usuario = (integer)strtok($a_sel[0], "#");
            $Qid_item = (integer)strtok("#");
        }
        $oUsuarioPerm = new PermMenu(array('id_item' => $Qid_item));
        if ($oUsuarioPerm->DBEliminar() === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $oUsuarioPerm->getErrorTxt();
        }
        break;
    case 'perm_eliminar':
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) { //vengo de un checkbox
            $Qid_usuario = (integer)strtok($a_sel[0], "#");
            $Qid_item = (integer)strtok("#");
        }
        $oUsuario = new GrupoOUsuario(array('id_usuario' => $Qid_usuario)); // La tabla y su heredada
        $oUsuarioPerm = new PermUsuarioActividad(array('id_item' => $Qid_item));
        if ($oUsuarioPerm->DBEliminar() === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $oUsuarioPerm->getErrorTxt();
        }
        break;
    case 'perm_update':
        $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
        $QaFase_ref = (array)filter_input(INPUT_POST, 'fase_ref', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $QaPerm_on = (array)filter_input(INPUT_POST, 'perm_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $QaPerm_off = (array)filter_input(INPUT_POST, 'perm_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $QaAfecta_a = (array)filter_input(INPUT_POST, 'afecta_a', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if (empty($Qid_tipo_activ)) {
            $Qisfsv_val = (string)filter_input(INPUT_POST, 'isfsv_val');
            $Qiasistentes_val = (string)filter_input(INPUT_POST, 'iasistentes_val');
            $Qiactividad_val = (string)filter_input(INPUT_POST, 'iactividad_val');
            $Qinom_tipo_val = (string)filter_input(INPUT_POST, 'inom_tipo_val');

            $sfsv_val = empty($Qisfsv_val) ? '.' : $Qisfsv_val;
            $asistentes_val = empty($Qiasistentes_val) ? '.' : $Qiasistentes_val;
            $actividad_val = empty($Qiactividad_val) ? '.' : $Qiactividad_val;
            $nom_tipo_val = empty($Qinom_tipo_val) ? '...' : $Qinom_tipo_val;
            $id_tipo_activ_txt = $sfsv_val . $asistentes_val . $actividad_val . $nom_tipo_val;
        } else {
            $id_tipo_activ_txt = $Qid_tipo_activ;
        }


        // afecta a:
        $oCuadros = new PermAfectados();
        $aAfecta_a = $oCuadros->getPermissions();
        $gesPermUsuarioActividad = new GestorPermUsuarioActividad();
        foreach ($aAfecta_a as $afecta_a) {
            $aWhere = [
                'id_usuario' => $Qid_usuario,
                'dl_propia' => $Qdl_propia,
                'id_tipo_activ_txt' => $id_tipo_activ_txt,
                'afecta_a' => $afecta_a,
            ];

            $fase_ref = '';
            $perm_on = '';
            $perm_off = '';
            // si tiene valor grabo, sino elimino:
            $eliminar = TRUE;
            if (in_array($afecta_a, $QaAfecta_a)) {
                $i = array_search($afecta_a, $QaAfecta_a);
                $fase_ref = $QaFase_ref[$i];
                // si no hay fase ref, hay que eliminar
                if (empty($fase_ref)) {
                    $eliminar = TRUE;
                } else {
                    $perm_off = empty($QaPerm_off[$i]) ? 0 : $QaPerm_off[$i];
                    $perm_on = empty($QaPerm_on[$i]) ? 0 : $QaPerm_on[$i];
                    $cPermUsuarioActividad = $gesPermUsuarioActividad->getPermUsuarioActividades($aWhere);
                    // Solo deberia haber uno???
                    if (count($cPermUsuarioActividad) == 1) {
                        $oUsuarioPerm = $cPermUsuarioActividad[0];
                        $oUsuarioPerm->DBCarregar();
                    } else {
                        $oUsuarioPerm = new PermUsuarioActividad();
                    }
                    $oUsuarioPerm->setId_usuario($Qid_usuario);
                    $oUsuarioPerm->setId_tipo_activ_txt($id_tipo_activ_txt);
                    $oUsuarioPerm->setDl_propia($Qdl_propia);
                    $oUsuarioPerm->setAfecta_a($afecta_a);
                    $oUsuarioPerm->setFase_ref($fase_ref);
                    $oUsuarioPerm->setperm_on($perm_on);
                    $oUsuarioPerm->setperm_off($perm_off);
                    if ($oUsuarioPerm->DBGuardar() === false) {
                        echo _("hay un error, no se ha guardado");
                        echo "\n" . $oUsuarioPerm->getErrorTxt();
                    }
                    $eliminar = FALSE;
                }
            }
            if ($eliminar == TRUE) {
                $cPermUsuarioActividad = $gesPermUsuarioActividad->getPermUsuarioActividades($aWhere);
                // Solo deberia haber uno???
                if (count($cPermUsuarioActividad) == 1) {
                    $oUsuarioPerm = $cPermUsuarioActividad[0];
                    $oUsuarioPerm->DBEliminar();
                }
            }
        }
        break;
    case "buscar":
        $Qusuario = (string)filter_input(INPUT_POST, 'usuario');

        $oUsuarios = new GestorUsuario();
        $oUser = $oUsuarios->getUsuarios(array('usuario' => $Qusuario));
        $oUsuario = $oUser[0];
        break;
    case "check_pwd":
        $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
        $Qusuario = (string)filter_input(INPUT_POST, 'usuario');
        $Qpassword = (string)filter_input(INPUT_POST, 'password');

        /*
        $Qusuario = rawUrlDecode($Qusuario_encoded);
        $Qpassword = rawUrlDecode($Qpassword_encoded);
        */

        if (!empty($Qusuario)) { // si es nuevo no tiene id
            $usuario = $Qusuario;
        } elseif (!empty($Qid_usuario)) {
            $oUsuario = new Usuario(array('id_usuario' => $Qid_usuario));
            $oUsuario->DBCarregar();
            $usuario = $oUsuario->getUsuario();
        }

        if (!empty($Qpassword)) {
            $oCrypt = new MyCrypt();
            $jsondata = $oCrypt->is_valid_password($usuario, $Qpassword);

            //Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsondata);
            exit();
        }
        break;
    case "guardar_pwd":
        $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
        $Qpassword = (string)filter_input(INPUT_POST, 'password');
        $Qpass = (string)filter_input(INPUT_POST, 'pass');

        $oUsuario = new Usuario(array('id_usuario' => $Qid_usuario));
        $oUsuario->DBCarregar();

        $usuario = $oUsuario->getUsuario();

        if (!empty($Qpassword)) {
            $oCrypt = new MyCrypt();
            $jsondata = $oCrypt->is_valid_password($usuario, $Qpassword);

            if ($jsondata['success'] === FALSE) {
                //Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsondata);
                exit();
            } else {
                $my_passwd = $oCrypt->encode($Qpassword);
                $oUsuario->setPassword($my_passwd);
            }
        } else {
            $oUsuario->setPassword($Qpass);
        }
        if ($oUsuario->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oUsuario->getErrorTxt();
        }
        break;
    case "guardar_mail":
        $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
        $Qemail = (string)filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        $oUsuario = new Usuario(array('id_usuario' => $Qid_usuario));
        $oUsuario->DBCarregar();

        $usuario = $oUsuario->getUsuario();
        $oUsuario->setEmail($Qemail);
        if ($oUsuario->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oUsuario->getErrorTxt();
        }
        break;
    case "guardar":
        $Qusuario = (string)filter_input(INPUT_POST, 'usuario');
        $Qquien = (string)filter_input(INPUT_POST, 'quien');

        if (empty($Qusuario)) {
            echo _("debe poner un nombre");
        }
        switch ($Qquien) {
            case 'usuario':
                $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
                $Qperm_activ = (array)filter_input(INPUT_POST, 'perm_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                $Qid_role = (integer)filter_input(INPUT_POST, 'id_role');
                $Qemail = (string)filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

                $Qnom_usuario = (string)filter_input(INPUT_POST, 'nom_usuario');
                $Qpassword = (string)filter_input(INPUT_POST, 'password');
                $Qpass = (string)filter_input(INPUT_POST, 'pass');
                $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
                $Qid_ctr = (integer)filter_input(INPUT_POST, 'id_ctr');
                $Qcasas = (array)filter_input(INPUT_POST, 'casas', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

                $oUsuario = new Usuario(array('id_usuario' => $Qid_usuario));
                $oUsuario->setUsuario($Qusuario);
                //cuando el campo es perm_activ, se pasa un array que hay que convertirlo en número.
                if (!empty($Qperm_activ)) {
                    $byte = 0;
                    foreach ($Qperm_activ as $bit) {
                        $byte = $byte + $bit;
                    }
                    $oUsuario->setPerm_activ($byte);
                }
                $oUsuario->setid_role($Qid_role);
                $oUsuario->setEmail($Qemail);
                $oUsuario->setNom_usuario($Qnom_usuario);
                if (!empty($Qpassword)) {
                    $oCrypt = new MyCrypt();
                    $my_passwd = $oCrypt->encode($Qpassword);
                    $oUsuario->setPassword($my_passwd);
                } else {
                    $oUsuario->setPassword($Qpass);
                }
                $oRole = new Role($Qid_role);
                $pau = $oRole->getPau();
                // sacd
                if (($pau == 'sacd' || $pau == 'nom') && !empty($Qid_nom)) {
                    $oUsuario->setId_pau($Qid_nom);
                }
                // centros (sv o sf)
                if (($pau == 'ctr') && !empty($Qid_ctr)) {
                    $oUsuario->setId_pau($Qid_ctr);
                }
                // casas
                if ($pau == 'cdc' && !empty($Qcasas)) {
                    $txt_casa = '';
                    $i = 0;
                    foreach ($Qcasas as $id_ubi) {
                        if (empty($id_ubi)) continue;
                        $i++;
                        if ($i > 1) $txt_casa .= ',';
                        $txt_casa .= $id_ubi;
                    }
                    $oUsuario->setId_pau($txt_casa);
                }
                break;
            case 'grupo':
                $Qid_role = (integer)filter_input(INPUT_POST, 'id_role');
                $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

                $oUsuario = new Grupo(array('id_usuario' => $Qid_usuario));
                $oUsuario->setUsuario($Qusuario);
                $oUsuario->setid_role($Qid_role);
                break;
        }
        if ($oUsuario->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oUsuario->getErrorTxt();
        }
        break;
    case "nuevo":
        $Qquien = (string)filter_input(INPUT_POST, 'quien');
        switch ($Qquien) {
            case 'usuario':
                $Qperm_activ = (array)filter_input(INPUT_POST, 'perm_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                $Qusuario = (string)filter_input(INPUT_POST, 'usuario');
                $Qemail = (string)filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $Qid_role = (integer)filter_input(INPUT_POST, 'id_role');
                $Qnom_usuario = (string)filter_input(INPUT_POST, 'nom_usuario');
                $Qpassword = (string)filter_input(INPUT_POST, 'password');

                if ($Qusuario && $Qpassword) {
                    $oUsuario = new Usuario();
                    $oUsuario->setUsuario($Qusuario);
                    if (!empty($Qpassword)) {
                        $oCrypt = new MyCrypt();
                        $my_passwd = $oCrypt->encode($Qpassword);
                        $oUsuario->setPassword($my_passwd);
                    }
                    $oUsuario->setEmail($Qemail);
                    $oUsuario->setId_role($Qid_role);
                    $oUsuario->setNom_usuario($Qnom_usuario);
                    //cuando el campo es perm_activ, se pasa un array que hay que convertirlo en número.
                    if (!empty($Qperm_activ)) {
                        $byte = 0;
                        foreach ($Qperm_activ as $bit) {
                            $byte = $byte + $bit;
                        }
                        $oUsuario->setPerm_activ($byte);
                    }
                    if ($oUsuario->DBGuardar() === false) {
                        echo _("hay un error, no se ha guardado");
                        echo "\n" . $oUsuario->getErrorTxt();
                    }
                } else {
                    echo _("debe poner un nombre y el password");
                }
                break;
            case "grupo":
                $Qusuario = (string)filter_input(INPUT_POST, 'usuario');
                $Qid_role = (integer)filter_input(INPUT_POST, 'id_role');

                if ($Qusuario) {
                    $oUsuario = new Grupo();
                    $oUsuario->setUsuario($Qusuario);
                    $oUsuario->setid_role($Qid_role);
                    if ($oUsuario->DBGuardar() === false) {
                        echo _("hay un error, no se ha guardado");
                        echo "\n" . $oUsuario->getErrorTxt();
                    }
                } else {
                    exit("debe poner un nombre");
                }
                break;
        }
        break;
}