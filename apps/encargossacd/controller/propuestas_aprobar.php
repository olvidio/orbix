<?php

// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\GestorPropuestas;
use encargossacd\model\entity\GestorPropuestaEncargosSacd;
use web\DateTimeLocal;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
//

$Qfiltro_ctr = (string) \filter_input(INPUT_POST, 'filtro_ctr');

$gesPropuestas = new GestorPropuestas();
$oHoy = new DateTimeLocal();
$f_iso = $oHoy->getIso();

$gesPropuestaEncargosSacd = new GestorPropuestaEncargosSacd();
$cPropuestas = $gesPropuestaEncargosSacd->getEncargosSacd();
foreach($cPropuestas as $oPropuestaEncargoSacd) {
 $id_nom = $oPropuestaEncargoSacd->getId_nom();
    $id_nom_new = $oPropuestaEncargoSacd->getId_nom_new();
    $id_item = $oPropuestaEncargoSacd->getId_item();
    
    // borrar (poner f_fin) los que desaparecen
    if (empty($id_nom_new) && !empty($id_nom)) {
        $gesPropuestas->finEncargo($id_item,$f_iso);
        continue;
    }
    // insertar los nuevos
    if (empty($id_nom) && !empty($id_nom_new)) {
        $gesPropuestas->newEncargo($oPropuestaEncargoSacd, $f_iso);
        continue;
    }
    
    // actualizar:
    if (!empty($id_nom) && !empty($id_nom_new)) {
        if ($id_nom == $id_nom_new) {
            // comprobar horario
            $gesPropuestas->comprobarHorario($id_item, $f_iso); 
        } else { // cambio sacd
           // poner finalizado en el old
            $gesPropuestas->finEncargo($id_item,$f_iso);
           // generar el nuevo
            $gesPropuestas->newEncargo($oPropuestaEncargoSacd, $f_iso);
        }
    }
    
}
// Hay que borrar las tablas de propuestas para que no haya posibilidad de errores. 
// Al hacer un cambio, borra el aniguo, pero si la tabla no de ha actualizado, no 
// borrará el actual, sino el inicial.
$gesPropuestas->BorrarTablasPropuestas();


echo _("Hecho!");