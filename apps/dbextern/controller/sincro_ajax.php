<?php

use core\ConfigGlobal;
use dbextern\model\entity\GestorIdMatchPersona;
use dbextern\model\entity\GestorPersonaBDU;
use dbextern\model\entity\IdMatchPersona;
use dbextern\model\SincroDB;
use Illuminate\Http\JsonResponse;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$sfsv = ConfigGlobal::mi_sfsv();

$que = (string)filter_input(INPUT_POST, 'que');

switch ($que) {
    // 4 casos:
    //	está en listas(midl), esta en orbix(otradl), está unido (si-no)
    //	está en listas(midl), y NO esta en orbix, está unido (si-no)
    case "crear":
        $tabla = 'tmp_bdu';
        $id_nom_listas = (integer)filter_input(INPUT_POST, 'id_nom_listas');
        $id = (integer)filter_input(INPUT_POST, 'id');
        $tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

        $Query = "SELECT * FROM $tabla WHERE identif = $id_nom_listas ";
        //AND camb_fic IS NULL";
        $oSincroDB = new SincroDB();
        $oGesListas = new GestorPersonaBDU();
        $cPersonasListas = $oGesListas->getPersonaBDUQuery($Query);
        if ($cPersonasListas !== FALSE && count($cPersonasListas) == 1) {
            $oPersonaListas = $cPersonasListas[0];
            $id_nom_listas = $oPersonaListas->getIdentif();
            $ape_nom = $oPersonaListas->getApeNom();
            $nombre = $oPersonaListas->getNombre();
            $apellido1 = $oPersonaListas->getApellido1();
            $nx1 = $oPersonaListas->getNx1();
            $apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
            $nx2 = $oPersonaListas->getNx2();
            $apellido2 = $oPersonaListas->getApellido2();
            $apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
            $f_nacimiento = $oPersonaListas->getFecha_Naci();
            $lugar_nacimiento = $oPersonaListas->getLugar_Naci();
            // para las dl dentro de regiones
            $dl_listas = $oPersonaListas->getDl();
            $dl_orbix = $oSincroDB->dlListas2Orbix($dl_listas);

            $id_tipo_persona = substr($id_nom_listas, 0, 1);
            switch ($id_tipo_persona) {
                case '4': // sssc
                    $obj_pau = 'PersonaSSSC';
                    break;
                case '3': // supernumerarios
                    $obj_pau = 'PersonaS';
                    break;
                case '1': // numerarios
                    $obj_pau = 'PersonaN';
                    break;
                case '2': // agregados
                    $obj_pau = 'PersonaAgd';
                    break;
                case "p_nax":
                    $obj_pau = 'PersonaNax';
                    break;
                default:
                    $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                    exit ($err_switch);
            }
            // Buscar si está en orbix (otras dl)
            // a) si ya está unida; b) si está sin unir.
            $oGesMatch = new GestorIdMatchPersona();
            $cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas' => $id_nom_listas));
            if (!empty($cIdMatch[0]) && !empty($cIdMatch)) { // (a) unida
                $id_orbix = $cIdMatch[0]->getId_orbix();
                $oTrasladoDl = new \src\personas\domain\trasladoDl();
                $oTrasladoDl->getEsquemas($id_orbix, $tipo_persona);
            } else { //(b) mala suerte!

            }

            $oHoy = new \src\shared\domain\value_objects\DateTimeLocal();
            $obj = 'personas\\model\\entity\\' . $obj_pau;
            $oPersona = new $obj();

            $oPersona->setSituacion('A');
            $oPersona->setF_situacion($oHoy);
            $oPersona->setNom($nombre);
            $oPersona->setNx1($nx1);
            $oPersona->setApellido1($apellido1_sinprep);
            $oPersona->setNx2($nx2);
            $oPersona->setApellido2($apellido2_sinprep);
            $oPersona->setF_nacimiento($f_nacimiento);
            $oPersona->setLugar_nacimiento($lugar_nacimiento);
            $oPersona->setDl($dl_orbix);

            if ($oPersona->DBGuardar() === false) {
                exit(_("hay un error, no se ha guardado"));
            }
            $id_orbix = $oPersona->getId_nom();
        } else {
            echo "Error";
        }

    // Empalmo con lo de unir:
    case 'unir':
        if ($que !== 'crear') {
            $id_orbix = (integer)filter_input(INPUT_POST, 'id_orbix');
            $id_nom_listas = (integer)filter_input(INPUT_POST, 'id_nom_listas');
            $id = (integer)filter_input(INPUT_POST, 'id');
            $tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
        }
        $oIdMatch = new IdMatchPersona($id_nom_listas);
        $oIdMatch->setId_orbix($id_orbix);
        $oIdMatch->setId_tabla($tipo_persona);

        if ($oIdMatch->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oIdMatch->getErrorTxt();
        }
        //Elimino el valor del array
        session_start();
        unset($_SESSION['DBListas'][$id]);
        array_filter($_SESSION['DBListas']);
        // reindexo el array
        $_SESSION['DBListas'] = array_values($_SESSION['DBListas']);
        session_write_close();

        break;
    case 'desunir':
        $id_nom_listas = (integer)filter_input(INPUT_POST, 'id_nom_listas');
        $tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

        $oIdMatch = new IdMatchPersona($id_nom_listas);
        $oIdMatch->setId_tabla($tipo_persona);

        $error_txt = '';
        if ($oIdMatch->DBEliminar() === false) {
            $error_txt = _("hay un error, no se ha eliminado");
            $error_txt .= "\n" . $oIdMatch->getErrorTxt();
        }
        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
        }
        (new JsonResponse($jsondata))->send();
        break;
    case 'syncro':
        $region = (string)filter_input(INPUT_POST, 'region');
        $dl_listas = (string)filter_input(INPUT_POST, 'dl_listas');
        $tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

        // prepara lista de ctr
        $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        // Hay que asegurarse que ya no se distingue entre ctr de n y agd		
        //$cCentros = $GesCentros->getCentros(array('tipo_ctr'=>'^'.$tipo_persona),array('tipo_ctr'=>'~'));
        //$cCentros = $GesCentros->getCentros(array('tipo_ctr'=>'^[na]'),array('tipo_ctr'=>'~'));
        $cCentros = $GesCentros->getCentros();
        $a_centros = [];
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $ctr = $oCentro->getNombre_ubi();
            $a_centros[$ctr] = $id_ubi;
        }

        $oSincroDB = new dbextern\model\SincroDB();
        $oSincroDB->setTipo_persona($tipo_persona);
        $oSincroDB->setRegion($region);
        $oSincroDB->setDlListas($dl_listas);
        $oSincroDB->setCentros($a_centros);

        // todos los de listas
        $cPersonasListas = $oSincroDB->getPersonasBDU();
        $i = 0;
        $msg = '';
        foreach ($cPersonasListas as $oPersonaListas) {
            $id_nom_listas = $oPersonaListas->getIdentif();

            $oGesMatch = new GestorIdMatchPersona();
            $cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas' => $id_nom_listas));
            if (!empty($cIdMatch[0]) && !empty($cIdMatch)) {
                $i++;
                $id_orbix = $cIdMatch[0]->getId_orbix();
                $rta = $oSincroDB->syncro($oPersonaListas, $id_orbix);
                if (!empty($rta)) {
                    $msg .= !empty($msg) ? "\n" : '';
                    $msg .= $rta;
                }
            }
        }
        if (!empty($msg)) {
            echo $msg;
        } else {
            echo sprintf(_("OK. %s personas sincronizadas"), $i);
        }
        break;
    case 'trasladar':
        $dl = (string)filter_input(INPUT_POST, 'dl');
        $tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
        $id_nom_orbix = (string)filter_input(INPUT_POST, 'id_nom_orbix');

        $oTrasladoDl = new \src\personas\domain\TrasladoDl();
        $oTrasladoDl->setId_nom($id_nom_orbix);

        $aEsquemas = $oTrasladoDl->getEsquemas($id_nom_orbix, $tipo_persona);
        //keys:  schema,id_schema,situacion,f_situacion
        foreach ($aEsquemas as $esquema) {
            if ($esquema['situacion'] === 'A') {
                $esq_org = $esquema['schemaname'];
            }
        }
        $mi_esquema = ConfigGlobal::mi_region_dl();
        $oHoy = new \src\shared\domain\value_objects\DateTimeLocal();

        $oTrasladoDl->setDl_persona($dl);
        $oTrasladoDl->setReg_dl_org($esq_org);
        $oTrasladoDl->setReg_dl_dst($mi_esquema);
        $oTrasladoDl->setF_dl($oHoy);
        $oTrasladoDl->setSituacion('L');

        $jsondata = $oTrasladoDl->trasladar();
        (new JsonResponse($jsondata))->send();

        break;
    case 'trasladarA':
        $dl = (string)filter_input(INPUT_POST, 'dl');
        $tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
        $id_nom_orbix = (string)filter_input(INPUT_POST, 'id_nom_orbix');

        $oTrasladoDl = new \src\personas\domain\TrasladoDl();
        $oTrasladoDl->setId_nom($id_nom_orbix);

        $mi_dele = ConfigGlobal::mi_delef();
        $mi_esquema = ConfigGlobal::mi_region_dl();
        $oHoy = new \src\shared\domain\value_objects\DateTimeLocal();
        $sfsv_txt = (ConfigGlobal::mi_sfsv() == 1) ? 'v' : 'f';
        // si cambia de región, debe hacerse manualmente para introducir
        // correctamente el campo 'situacion'
        if (str_starts_with($dl, 'dl')) {
            $dl_dst = $dl;
        } else {
            $dl_dst = 'cr' . $dl;
        }
        $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $cDl = $repoDelegacion->getDelegaciones(['dl' => $dl_dst, 'active' => 't']);
        $region_dst = $cDl[0]->getRegionVo()->value();

        if ($region_dst !== ConfigGlobal::mi_region()) {
            echo "\n";
            echo _("Este traslado debe hacerse desde el dossier de traslados");
            echo "\n";
            echo _("Para asegurar que se llena correctamente el campo situación");
            break;
        }

        $esq_dst = $region_dst . '-' . $dl_dst . $sfsv_txt;
        $situacion = 'L';

        $oTrasladoDl->setDl_persona($mi_dele);
        $oTrasladoDl->setReg_dl_org($mi_esquema);
        $oTrasladoDl->setReg_dl_dst($esq_dst);
        $oTrasladoDl->setF_dl($oHoy);
        $oTrasladoDl->setSituacion($situacion);

        $jsondata = $oTrasladoDl->trasladar();
        (new JsonResponse($jsondata))->send();

        break;
    case 'baja':
        $dl = (string)filter_input(INPUT_POST, 'dl');
        $tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
        $id_nom_orbix = (string)filter_input(INPUT_POST, 'id_nom_orbix');

        $oTrasladoDl = new \src\personas\domain\TrasladoDl();
        $oTrasladoDl->setId_nom($id_nom_orbix);

        $mi_dele = ConfigGlobal::mi_delef();
        $mi_esquema = ConfigGlobal::mi_region_dl();
        $oHoy = new \src\shared\domain\value_objects\DateTimeLocal();
        $sfsv_txt = (ConfigGlobal::mi_sfsv() == 1) ? 'v' : 'f';
        $esq_dst = "H-" . $dl . $sfsv_txt;

        $oTrasladoDl->setDl_persona($mi_dele);
        $oTrasladoDl->setReg_dl_org($mi_esquema);
        $oTrasladoDl->setReg_dl_dst($esq_dst);
        $oTrasladoDl->setF_dl($oHoy);
        $oTrasladoDl->setSituacion('B');

        $error_txt = '';
        if ($oTrasladoDl->cambiarFichaPersona() === false) {
            $error_txt = _("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.");
        }

        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
        }
        (new JsonResponse($jsondata))->send();
        break;
    case 'crear_todos':
        $region = (string)filter_input(INPUT_POST, 'region');
        $dl = (string)filter_input(INPUT_POST, 'dl');
        $tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

        $oSincroDB = new dbextern\model\SincroDB();
        $oSincroDB->setTipo_persona($tipo_persona);
        $oSincroDB->setRegion($region);
        $oSincroDB->setDlListas($dl);

        // todos los de listas
        $cPersonasListas = $oSincroDB->getPersonasBDU();

        $i = 0;
        foreach ($cPersonasListas as $oPersonaListas) {
            $id_nom_listas = $oPersonaListas->getIdentif();

            $oGesMatch = new dbextern\model\entity\GestorIdMatchPersona();
            $cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas' => $id_nom_listas));
            if (!empty($cIdMatch[0]) && count($cIdMatch) > 0) {
                continue;
            }

            // incremento antes para empezar en 1 y no en 0.
            $i++;

            // Copiado de crear y unir:
            $ape_nom = $oPersonaListas->getApeNom();
            $nombre = $oPersonaListas->getNombre();
            $apellido1 = $oPersonaListas->getApellido1();
            $nx1 = $oPersonaListas->getNx1();
            $apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
            $nx2 = $oPersonaListas->getNx2();
            $apellido2 = $oPersonaListas->getApellido2();
            $apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
            $f_nacimiento = $oPersonaListas->getFecha_Naci();
            $lugar_nacimiento = $oPersonaListas->getLugar_Naci();
            // para las dl dentro de regiones
            $dl_listas = $oPersonaListas->getDl();
            $dl_orbix = $oSincroDB->dlListas2Orbix($dl_listas);

            $id_tipo_persona = substr($id_nom_listas, 0, 1);
            switch ($id_tipo_persona) {
                case '4': // sssc
                    $obj_pau = 'PersonaSSSC';
                    break;
                case '3': // supernumerarios
                    $obj_pau = 'PersonaS';
                    break;
                case '1': // numerarios
                    $obj_pau = 'PersonaN';
                    break;
                case '2': // agregados
                    $obj_pau = 'PersonaAgd';
                    break;
                case "p_nax":
                    $obj_pau = 'PersonaNax';
                    break;
                default:
                    $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                    exit ($err_switch);
            }
            // Buscar si está en orbix (otras dl)
            // a) si ya está unida; b) si está sin unir.
            $oGesMatch = new GestorIdMatchPersona();
            $cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas' => $id_nom_listas));
            if (!empty($cIdMatch[0]) && !empty($cIdMatch)) { // (a) unida
                $id_orbix = $cIdMatch[0]->getId_orbix();
                $oTrasladoDl = new \src\personas\domain\trasladoDl();
                $oTrasladoDl->getEsquemas($id_orbix, $tipo_persona);
            } else { //(b) mala suerte!

            }

            $oHoy = new \src\shared\domain\value_objects\DateTimeLocal();
            $obj = 'personas\\model\\entity\\' . $obj_pau;
            $oPersona = new $obj();

            $oPersona->setSituacion('A');
            $oPersona->setF_situacion($oHoy);
            $oPersona->setNom($nombre);
            $oPersona->setNx1($nx1);
            $oPersona->setApellido1($apellido1_sinprep);
            $oPersona->setNx2($nx2);
            $oPersona->setApellido2($apellido2_sinprep);
            $oPersona->setF_nacimiento($f_nacimiento);
            $oPersona->setLugar_nacimiento($lugar_nacimiento);
            $oPersona->setDl($dl_orbix);

            if ($oPersona->DBGuardar() === false) {
                exit(_("hay un error, no se ha guardado"));
            }
            $id_orbix = $oPersona->getId_nom();
            // Empalmo con lo de unir:
            $oIdMatch = new IdMatchPersona($id_nom_listas);
            $oIdMatch->setId_orbix($id_orbix);
            $oIdMatch->setId_tabla($tipo_persona);

            if ($oIdMatch->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oIdMatch->getErrorTxt();
            }
        }
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}