<?php

// INICIO Cabecera global de URL de controlador *********************************

use misas\domain\repositories\PlantillaRepository;
use personas\model\entity\PersonaSacd;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use web\Hash;
use web\Lista;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


//$gestorPersonaSacd = new GestorPersonaSacd();

$Qid_zona = 24; // l'hospitalet (24)

// ctr de la zona

$aWhere['status'] = 't';
$aWhere['id_zona'] = $Qid_zona;
$aWhere['_ordre'] = 'nombre_ubi';
$GesCentrosDl = new GestorCentroDl();
$cCentrosDl = $GesCentrosDl->getCentros($aWhere);
$GesCentrosSf = new GestorCentroEllas();
$cCentrosSf = $GesCentrosSf->getCentros($aWhere);
$cCentros = array_merge($cCentrosDl, $cCentrosSf);

$a_botones = [];
/* tabla editable
$a_cabeceras = [
    ['name' => "id_ubi", 'field' => 'id', 'visible' => 'no'],
    ['name' => _("Centro"), 'field' => 'ctr', 'width' => 80, 'formatter' => 'clickFormatter'],
    ['name' => 'L', 'title' => _("lunes"), 'field' => "L", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
    ['name' => 'M', 'title' => _("lunes"), 'field' => "M", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
    ['name' => 'X', 'title' => _("lunes"), 'field' => "X", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
    ['name' => 'J', 'title' => _("lunes"), 'field' => "J", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
    ['name' => 'V', 'title' => _("lunes"), 'field' => "V", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
    ['name' => 'S', 'title' => _("lunes"), 'field' => "S", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
    ['name' => 'D', 'title' => _("lunes"), 'field' => "D", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
];

$i = 0;
$a_valores = [];
foreach ($cCentros as $oCentro) {
    $i++;
    $id_ubi = "{$oCentro->getId_ubi()}"; // Para que lo coja como un string.
    $nombre_ubi = $oCentro->getNombre_ubi();
    $a_valores[$i]['clase'] = 'tono2';
    $a_valores[$i]['id'] = $id_ubi;
    $a_valores[$i]['ctr'] = $nombre_ubi;
    foreach ($a_cabeceras as $column ) {
        $field = $column['field'];
        if ($field === 'id' || $field === 'ctr') {
            continue;
        }
        $a_valores[$i][$field] = ['editable' => 'true', 'valor' => 'x'];

    }
}

$oTabla = new TablaEditable();
$oTabla->setId_tabla('crear_plantilla');
$UpdateUrl = ConfigGlobal::getWeb() . '/apps/misas/controller/plantilla_ajax.php';
//$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
*/

$reloj = core\ConfigGlobal::getWeb_icons() . '/reloj.png';

/* tabla html */
$a_cabeceras = [
    "id_ubi",
    _("Centro"),
    'L',
    'M',
    'X',
    'J',
    'V',
    'S',
    'D',
];

$PlantillaRepository = new PlantillaRepository();
$i = 0;
$a_valores = [];
foreach ($cCentros as $oCentro) {
    $i++;
    $id_ubi = "{$oCentro->getId_ubi()}"; // Para que lo coja como un string.
    $nombre_ubi = $oCentro->getNombre_ubi();
    $tarea = 1; // 1:Misa, 2: bendición
    $a_valores[$i]['clase'] = 'tono2';
    $a_valores[$i][0] = $id_ubi;
    $a_valores[$i][1] = $nombre_ubi;
    $c = 1;
    foreach ($a_cabeceras as $column) {
        $c++;
        if ($column === 'id_ubi' || $column === _("Centro")) {
            continue;
        }
        switch ($column) {
            case 'L':
                $dia = 'MON';
                break;
            case 'M':
                $dia = 'TUE';
                break;
            case 'X':
                $dia = 'WED';
                break;
            case 'J':
                $dia ='THU';
                break;
            case 'V':
                $dia = 'FRI';
                break;
            case 'S':
                $dia = 'SAT';
                break;
            case 'D':
                $dia = 'SUN';
                break;
        }
        $semana = 0;
        $id_item = '';
        $aWhere = [
                'id_ctr' => $id_ubi,
                'tarea' => $tarea,
                'dia' => $dia,
                'semana' => $semana,
        ];
        $cPlantillas = $PlantillaRepository->getPlantillas($aWhere);
        if (is_array($cPlantillas) && count($cPlantillas) > 0) {
            $oPlantilla = $cPlantillas[0];
            $id_item = $oPlantilla->getId_item();
            $id_nom = $oPlantilla->getId_nom();
            $oPersonaSacd = new PersonaSacd($id_nom);
            $sacd = $oPersonaSacd->getNombreApellidos();
        } else {
            $sacd = '??';
        }
        $a_cosas = ['id_zona' => $Qid_zona,
            'id_ubi' => $id_ubi,
            'tarea' => $tarea,
            'dia' => $dia,
            'semana' => $semana,
            'id_item' => $id_item,
        ];
        $pagina = Hash::link(core\ConfigGlobal::getWeb() . '/apps/misas/controller/lista_sacd_zona.php?' . http_build_query($a_cosas));
        $texto_nombre = "<span class=link onclick=\"fnjs_modificar('$pagina');\">$sacd</span>";
        $pagina_horario = Hash::link(core\ConfigGlobal::getWeb() . '/apps/misas/controller/horario_tarea.php?' . http_build_query($a_cosas));
        $icono_horario = "<span class=link onclick=\"fnjs_modificar('$pagina_horario');\"><img src=\"$reloj\" width=\"12\" height=\"12\" style=\"float: right; margin: 0px 0px 15px 15px;\" alt=\"" . _("horario") . "\"></span>";
        $a_valores[$i][$c] = $texto_nombre . $icono_horario;
    }
}

$oTabla = new Lista();
$oTabla->setId_tabla('crear_plantilla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);


$url = 'apps/misas/controller/crear_plantilla.php';
$aQuery = ['id_zona' => $Qid_zona];
// el hppt_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$url_crear_plantilla = web\Hash::link($url . '?' . http_build_query($aQuery));


$a_campos = ['oPosicion' => $oPosicion,
    'oTabla' => $oTabla,
    'url_crear_plantilla' => $url_crear_plantilla,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('crear_plantilla.html.twig', $a_campos);