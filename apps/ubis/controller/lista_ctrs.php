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
use personas\model\entity\GestorPersonaS;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Lista;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//Para los centros de la dlb
$CentroReposiory = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
$cCentros = $CentroReposiory->getCentros(array('tipo_ctr' => '^s[^s]', 'status' => 't', '_ordre' => 'nombre_ubi'), array('tipo_ctr' => '~'));

$GesPersonas = new GestorPersonaS();
$num_total_s = 0;
$a_valores = [];
$i = 0;
foreach ($cCentros as $oCentro) {
    $i++;
    $id_ubi = $oCentro->getId_ubi();
    $nombre_ubi = $oCentro->getNombre_ubi();
    $cPersonasCtr = $GesPersonas->getPersonasDl(array('id_ctr' => $id_ubi, 'situacion' => 'A'));
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
