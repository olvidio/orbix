<?php
// si lo ejecuto dese el crontab.
/* Hay que pasarle los argumentos que no tienen si se le llama por command line:
 $username;
 $password;
 $dir_web = orbix | pruebas;
 document_root = /home/dani/orbix_local
 $esquema_web = 'H-dlbv';
 $ubicacion = 'sv';
 $private => pongo el mismo valor que ubicación. Se supone que el cron está en private.
 $DB_SERVER = 1 o 2; para indicar el servidor dede el que se ejecuta. (ver comentario en clase: CambioAnotado)
 */
if (!empty($argv[1])) {
    $_POST['username'] = $argv[1];
    $_POST['password'] = $argv[2];
    $_SERVER['DIRWEB'] = $argv[3];
    $_SERVER['DOCUMENT_ROOT'] = $argv[4];
    putenv("UBICACION=$argv[5]");
    putenv("PRIVATE=$argv[5]");
    putenv("DB_SERVER=$argv[6]");
    putenv("ESQUEMA=$argv[7]");
}
$document_root = $_SERVER['DOCUMENT_ROOT'];
$dir_web = $_SERVER['DIRWEB'];
$path = "$document_root/$dir_web";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

use cambios\model\entity\Cambio;
use cambios\model\entity\CambioDl;
use cambios\model\entity\CambioUsuario;
use cambios\model\entity\GestorCambioUsuario;
use core\ConfigGlobal;
use usuarios\model\entity\Preferencia;
use usuarios\model\entity\Usuario;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

/* se ejecuta desde un cron (de momento) en el servidor exterior, que es el que tiene conexión al servidor de correo.
 Hay que hacerlo para todos los usuarios.
 Comprobar que tengan e-mail
 */

// para asegurar que coje los cambios de otras dl que no tengan instalado el módulo de cambios, 
// hay que ejecutar el generarTabla().
$oCambio = new Cambio();
$oCambio->generarTabla();
sleep(60); // 1 minutos para asegurar que ha terminado el proceso que lanza el generar tabla.


$dele = ConfigGlobal::mi_dele();
$delef = $dele . 'f';
$aSecciones = array(1 => $dele, 2 => $delef);

$aviso_tipo = CambioUsuario::TIPO_MAIL; //e-mail
$mi_sfsv = ConfigGlobal::mi_sfsv();

$aWhere = array();
$aWhere['_ordre'] = 'id_usuario,id_item_cambio';
$aWhere['aviso_tipo'] = $aviso_tipo;
$aWhere['avisado'] = 'false';
$aWhere['sfsv'] = $mi_sfsv;
$GesCambiosUsuario = new GestorCambioUsuario();
$cCambiosUsuario = $GesCambiosUsuario->getCambiosUsuario($aWhere);
$i = 0;
$id_usuario_anterior = '';
$email = '';
$zona_horaria = '';
$a_datos = array();
$a_id = array();
$DateTimeZone = new DateTimeZone('UTC');
foreach ($cCambiosUsuario as $oCambioUsuario) {
    $id_usuario = $oCambioUsuario->getId_usuario();

    if ($id_usuario !== $id_usuario_anterior) {
        // solo en el primer caso no lo hago
        if (!empty($id_usuario_anterior)) {
            enviar_mail($email, $a_datos, $a_id);
            $a_datos = array();
            $a_id = array();
        }
        $oMiUsuario = new Usuario($id_usuario);
        $email = $oMiUsuario->getEmail();
        $id_usuario_anterior = $id_usuario;
        // buscar la zona horaria
        $oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => 'zona_horaria'));
        $zona_horaria = $oPref->getPreferencia();
        if (!empty($zona_horaria)) {
            $a_zonas_horarias = DateTimeZone::listIdentifiers();
            $zona_horaria_txt = $a_zonas_horarias[$zona_horaria];
            try {
                $DateTimeZone = new DateTimeZone($zona_horaria_txt);
            } catch (DateInvalidTimeZoneException $e) {
                $DateTimeZone = new DateTimeZone('UTC');
            }
        }
    }
    if (empty($email)) {
        continue;
    }


    $id_item_cmb = $oCambioUsuario->getId_item_cambio();
    $id_schema_cmb = $oCambioUsuario->getId_schema_cambio();
    if ($id_schema_cmb === 3000) {
        $oCambio = new Cambio($id_item_cmb);
    } else {
        $oCambio = new CambioDl($id_item_cmb);
    }
    $quien_cambia = $oCambio->getQuien_cambia();
    $sfsv_quien_cambia = $oCambio->getSfsv_quien_cambia();
    $oTimestamp_cambio_GMT = $oCambio->getTimestamp_cambio();
    $timestamp_cambio = $oTimestamp_cambio_GMT->setTimezone($DateTimeZone)->getFromLocalHora();

    $aviso_txt = $oCambio->getAvisoTxt();
    if ($aviso_txt === false) {
        continue;
    }
    $i++;
    // Quien cambia
    if ($id_schema_cmb === 3000) {
        $quien = $oCambio->getDl_org();
    } else {
        if ($sfsv_quien_cambia === $mi_sfsv) {
            $oUsuarioCmb = new Usuario($quien_cambia);
            $quien = $oUsuarioCmb->getUsuario();
        } else {
            $quien = $aSecciones[$sfsv_quien_cambia];
        }
    }

    $a_datos[$i][1] = $timestamp_cambio;
    $a_datos[$i][2] = $quien;
    $a_datos[$i][3] = $aviso_txt;
    $a_id[$i] = "$id_item_cmb,$id_usuario,$mi_sfsv,$aviso_tipo";
}
// El último de la lista no se envía.
if (!empty($email)) {
    enviar_mail($email, $a_datos, $a_id);
}

function enviar_mail($email, $a_datos, $a_id)
{
    //Evitar mails vacíos o sin dirección.
    if (empty($a_datos) || empty($email)) {
        eliminar_enviado($a_id);
        return;
    }

    $a_cabeceras = array(ucfirst(_("fecha cambio")),
        ucfirst(_("quien")),
        ucfirst(_("cambio"))
    );
    $oTabla = new Lista();
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setDatos($a_datos);

    $asunto = _("Avisos de cambios en actividades");
    $cuerpo = '
	<html>
	<head>
	<title>Tabla de cambios en actividades</title>
	</head>
	<body>';
    $cuerpo .= $oTabla->lista();
    $cuerpo .= '</body></html>';

    //Envío en formato HTML
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";

    //Dirección del remitente
    $headers .= "From: Aquinate <no-Reply@moneders.net>\r\n";
    //Dirección de respuesta
    $headers .= "Reply-To: no-Reply@moneders.net\r\n";
    //Ruta del mensaje desde origen a destino
    $headers .= "Return-path: no-Reply@moneders.net\r\n";


    //echo "($email<br>$asunto<br>$cuerpo<br>$headers)<br>";
    mail($email, $asunto, $cuerpo, $headers);
    eliminar_enviado($a_id);
}

function eliminar_enviado($a_id)
{
    foreach ($a_id as $id) {
        $ids = explode(',', $id);
        $id_item_cmb = $ids[0];
        $id_usuario = $ids[1];
        $sfsv = $ids[2];
        $aviso_tipo = $ids[3];
        $GesCambioUsuario = new GestorCambioUsuario();
        $aWhere = ['id_item_cambio' => $id_item_cmb,
            'id_usuario' => $id_usuario,
            'sfsv' => $sfsv,
            'aviso_tipo' => $aviso_tipo,
        ];

        $cCambiosUsuario = $GesCambioUsuario->getCambiosUsuario($aWhere);
        foreach ($cCambiosUsuario as $oCambioUsuario) {
            if ($oCambioUsuario->DBEliminar() === false) {
                echo _("Hay un error, no se ha eliminado");
                echo "\n" . $oCambioUsuario->getErrorTxt();
            }
        }
    }
}