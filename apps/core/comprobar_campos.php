<?php

namespace core;
/**
 * Esta página sirve para comprobar que los valores de los campos de
 * un formulario, se corresponden con el tipo de datos de la base de datos.
 *
 * Se le debe pasar el parámetro 'tabla'
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        17/9/2010.
 *
 */
/**
 * Para asegurar que inicia la sesión, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$cDatosCampos = false;

$Qcc_obj = (string)filter_input(INPUT_POST, 'cc_obj');

// si es nuevo, ya lo hare con value objects:
if (!empty($Qcc_obj) && str_contains($Qcc_obj,'src')) {
    exit();
}

if (!empty($Qcc_obj)) {
    $Object = new $Qcc_obj;
    $oDbl = $Object->getoDbl();
    $tabla = $Object->getNomTabla();
    // selecciono las restricciones anotadas en el objeto: getDatosCampos().
    $cDatosCampos = $Object->getDatosCampos();
}

$errores = [];
$Qcc_pau = (integer)filter_input(INPUT_POST, 'cc_pau');
foreach ($cDatosCampos as $oDatosCampo) {
    $reg_exp = $oDatosCampo->getRegExp();
    $nomcamp = $oDatosCampo->getNom_camp();
    $etiqueta = $oDatosCampo->getEtiqueta();
    $tipo = $oDatosCampo->datos_campo($oDbl, 'tipo');
    $not_null = $oDatosCampo->datos_campo($oDbl, 'nulo');
    $longitud = $oDatosCampo->datos_campo($oDbl, 'longitud');

    if (($nomcamp === 'id_nom' || $nomcamp === 'id_activ' || $nomcamp === 'id_ubi') && $Qcc_pau === 1) {
        $nomcamp = 'id_pau';
    }
    // caso especial.
    if ($nomcamp === 'sfsv') {
        $nomcamp = 'isfsv';
    }

    //print_r($_POST);
    // lo tengo en el POST?
    $nomcamp_post = $nomcamp;
    // No compruebo los que son array (tipo_labor...)
    if (array_key_exists($nomcamp_post, $_POST) && !is_array($_POST[$nomcamp_post])) {
        $valor = trim($_POST[$nomcamp_post]);
        if (!empty($valor)) {
            $vacio = 0;
            if (!empty($reg_exp)) {
                if (preg_match($reg_exp, $valor) === 0 || preg_match($reg_exp, $valor) === FALSE) {
                    $reg_text = $oDatosCampo->getRegExpText();
                    $reg_text = !empty($reg_text) ? $reg_text : _("no tiene el formato requerido");

                    $errores[] = array('txt' => _("el campo \"%1\$s\" \"%3\$s\". Valor actual: \"%2\$s\""),
                        'camp' => $nomcamp,
                        'etiqueta' => $etiqueta,
                        'val' => array($valor),
                        'regexptxt' => $reg_text);
                }
            }

            switch ($tipo) {
                case 'float4':
                case 'double':
                    if (!is_numeric($valor)) {
                        $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser un número real, y es: \"%2\$s\""),
                            'camp' => $nomcamp,
                            'etiqueta' => $etiqueta,
                            'val' => array($valor));
                    }
                    break;
                case 'numeric':
                    if (!is_numeric($valor)) {
                        $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser un número, y es: \"%2\$s\""),
                            'camp' => $nomcamp,
                            'etiqueta' => $etiqueta,
                            'val' => array($valor));
                    }
                    break;
                case 'int8':
                case 'int4':
                case 'int2':
                    //$valor=(int)$valor;
                    if (is_numeric($valor)) {
                        if ((int)$valor != $valor) {  //$valor es un string  (viene del POST)
                            $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser un número entero, y es: \"%2\$s\""),
                                'camp' => $nomcamp,
                                'etiqueta' => $etiqueta,
                                'val' => array($valor));
                        }
                    } else {
                        $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser un número entero, y es: \"%2\$s\""),
                            'camp' => $nomcamp,
                            'etiqueta' => $etiqueta,
                            'val' => array($valor));
                    }
                    break;
                case 'text':
                    if (!is_string($valor)) {
                        $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser un texto, y es: \"%2\$s\""),
                            'camp' => $nomcamp,
                            'etiqueta' => $etiqueta,
                            'val' => array($valor));
                    }
                    break;
                case 'varchar':
                    if (!is_string($valor)) {
                        $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser un texto, y es: \"%2\$s\""),
                            'camp' => $nomcamp,
                            'etiqueta' => $etiqueta,
                            'val' => array($valor));
                    } else {
                        if (strlen($valor) > ($longitud)) {
                            $errores[] = array('txt' => _("el campo \"%1\$s\" sólo puede tener %2\$d cararcteres y tiene %3\$d"),
                                'camp' => $nomcamp,
                                'etiqueta' => $etiqueta,
                                'val' => array(($longitud), strlen($valor))
                            );
                        }
                    }
                    break;
                case 'date':
                    $locale_us = ConfigGlobal::is_locale_us();
                    if (preg_match("/^([0-9]{1,2})[\/-]([0-9]{1,2})[\/-]([0-9]{2}|[0-9]{4})$/", $valor, $parts) == 1) {
                        if ($locale_us) {
                            //check weather the date is valid of not
                            $valid_date = checkdate($parts[1], $parts[2], $parts[3]);
                        } else {
                            $valid_date = checkdate($parts[2], $parts[1], $parts[3]);
                        }

                        if (!$valid_date) {
                            $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser una fecha, y es: \"%2\$s\""),
                                'camp' => $nomcamp,
                                'etiqueta' => $etiqueta,
                                'val' => array($valor));
                        }
                    } else {
                        $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser una fecha, y es: \"%2\$s\""),
                            'camp' => $nomcamp,
                            'etiqueta' => $etiqueta,
                            'val' => array($valor));
                    }
                    break;
                case
                'time':
                    $err = 0;
                    if (!empty($valor)) {
                        $parts = [];
                        if (preg_match("/^([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2}))?$/", $valor, $parts) == 1) {
                            if ($parts[1] > 24) $err = 1;
                            if ($parts[2] > 60) $err = 1;
                            if (!empty($parts[4]) && $parts[4] > 60) $err = 1;
                            if ($err == 1) {
                                $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser una hora. Debe tener el formato hh:mm:ss. [%2\$s]"),
                                    'camp' => $nomcamp,
                                    'etiqueta' => $etiqueta,
                                    'val' => array($valor));
                            }
                        } else {
                            $errores[] = array('txt' => _("el campo \"%1\$s\" debe ser una hora. Debe tener el formato hh:mm:ss. [%2\$s]"),
                                'camp' => $nomcamp,
                                'etiqueta' => $etiqueta,
                                'val' => array($valor));
                        }
                    }
                    break;
            }
        } else {
            $vacio = 1;
        }
    } else {
        $vacio = ($tipo === 'bool') ? 0 : 1; // en el caso de checkbox entiendo que no valor = false.
    }
    if ($vacio === 1) {
        // Es necesario?
        if ($not_null) {
            //tiene un valor por defecto?
            $default = $oDatosCampo->datos_campo($oDbl, 'valor');
            if (empty($default)) {
                $errores[] = array('txt' => _("el campo \"%1\$s\" no puede estar vacío"),
                    'camp' => $nomcamp,
                    'etiqueta' => $etiqueta,
                    'val' => array());
            }
        }
    }
}

/*
*  En caso de error busco la etiqueta del campo (si la hay) para hacer más
*  entendible el mensaje
*/
if (!empty($errores)) {
    $error_txt = "";
    foreach ($errores as $error) {
        $txt = $error['txt'];
        $camp = $error['camp'];
        $etiqueta = $error['etiqueta'];
        $valores = $error['val'];
        $regexptext = empty($error['regexptxt']) ? '' : $error['regexptxt'];
        $nomcampo = empty($etiqueta) ? $camp : $etiqueta;

        array_unshift($valores, $nomcampo);
        if (!empty($regexptext)) {
            $valores[] = $regexptext;
        }
        $error_txt .= vsprintf($txt, $valores) . "\n";
    }
    echo trim($error_txt);
}
