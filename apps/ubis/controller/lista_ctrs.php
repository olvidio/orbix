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
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Lista;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//Para los centros de la dlb
$CentroRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
$cCentros = $CentroRepository->getCentros(array('tipo_ctr' => '^s[^s]', 'active' => 't', '_ordre' => 'nombre_ubi'), array('tipo_ctr' => '~'));

$PersonaSRepository = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
$num_total_s = 0;
$a_valores = [];
$i = 0;
foreach ($cCentros as $oCentro) {
    $i++;
    $id_ubi = $oCentro->getId_ubi();
    $nombre_ubi = $oCentro->getNombre_ubi();
    $cPersonasCtr = $PersonaSRepository->getPersonas(array('id_ctr' => $id_ubi, 'situacion' => 'A'));
    if ($cPersonasCtr === false) exit(_('error'));
    $num_s = count($cPersonasCtr);
    $num_total_s += $num_s;


    $a_valores[$i][1] = $nombre_ubi;
    $a_valores[$i][2] = $num_s;

}

$a_cabeceras = array(ucfirst(_('centro')),
    _('num s')
);

$oTabla = new Lista();
$oTabla->setId_tabla('lista_ctrs');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

echo "<h3>" . ucfirst(sprintf(_("número total de s: %s"), $num_total_s)) . "</h3>";
echo $oTabla->mostrar_tabla();
