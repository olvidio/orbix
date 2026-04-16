<?php
/**
 * Lista con los datos básicos de los cp.
 *
 * @package    delegacion
 * @subpackage    sg
 * @author    Daniel Serrabou
 * @since        6/10/08.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use frontend\shared\PostRequest;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/ubis/lista_ctrs_data', []);
if (isset($data['error']) && $data['error'] !== '') {
    exit((string)$data['error']);
}

$oTabla = new Lista();
$oTabla->setId_tabla('lista_ctrs');
$oTabla->setCabeceras($data['a_cabeceras'] ?? []);
$oTabla->setDatos($data['a_valores'] ?? []);

$num_total_s = (int)($data['num_total_s'] ?? 0);

echo "<h3>" . ucfirst(sprintf(_("número total de s: %s"), $num_total_s)) . "</h3>";
echo $oTabla->mostrar_tabla();
