<?php

// INICIO Cabecera global de URL de controlador *********************************

use encargossacd\model\entity\Encargo;
use misas\model\EncargosZona;
use personas\model\entity\PersonaSacd;
use ubis\model\entity\CentroDl;
use web\DateTimeLocal;
use web\Hash;
use web\Lista;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$oInicio = DateTimeLocal::createFromLocal('1/1/2000');
$oFin = DateTimeLocal::createFromLocal('8/1/2000');

// encargos de misa (8010) para la zona
$a_tipo_enc = [8010, 8011];
$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin);
$EncargosZona->setATipoEnc($a_tipo_enc);


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

/*
function valorFila($a_horario_por_dias)
{
    foreach ($cEncargoHorarios as $oEncargoHorario) {
        $oPlantilla = $cPlantillas[0];
        $id_item = $oPlantilla->getId_item();
        $t_start = $oPlantilla->getT_start()->format('H:i');
        $t_end = $oPlantilla->getT_end()->format('H:i');
        $id_nom = $oPlantilla->getId_nom();
        $oPersonaSacd = new PersonaSacd($id_nom);
        $sacd = $oPersonaSacd->getNombreApellidos();
        if (empty(trim($sacd))) {
            $sacd = _("se tiene el id, pero NO el sacd");
        }
    }
else {
    $sacd = '??';
    $t_start = '??';
    $t_end = '??';
}

    $a_cosas = ['id_zona' => $Qid_zona,
        'id_ubi' => $id_ubi,
        'dia' => $dia,
        'id_item' => $id_item,
    ];
    $pagina = Hash::link(core\ConfigGlobal::getWeb() . '/apps/misas/controller/definicion_horario.php?' . http_build_query($a_cosas));
    $texto_nombre = "<span class=link onclick=\"fnjs_modificar('$pagina');\">$sacd</span>";

    $reloj = core\ConfigGlobal::getWeb_icons() . '/reloj.png';
    $pagina_horario = Hash::link(core\ConfigGlobal::getWeb() . '/apps/misas/controller/horario_tarea.php?' . http_build_query($a_cosas));
    $icono_horario = "<span class=link onclick=\"fnjs_modificar('$pagina_horario');\"><img src=\"$reloj\" width=\"12\" height=\"12\" style=\"float: right; margin: 0 0 15px 15px;\" alt=\"" . _("horario") . "\"></span>";
    if (($t_start !== '??') && ($t_start !== null) && ($t_end !== '??') && ($t_end !== null)) {
        $icono_horario = "<br><span class=link onclick=\"fnjs_modificar('$pagina_horario');\">$t_start - $t_end</span>";
    }
    if (($t_start !== '??') && ($t_start !== null) && (($t_end === '??') || ($t_end === null))) {
        $icono_horario = "<br><span class=link onclick=\"fnjs_modificar('$pagina_horario');\">$t_start</span>";
    }

    return $texto_nombre . $icono_horario;
}
*/


/* tabla html */
$a_cabeceras = [
    "sel",
    _("Centro"),
    _("encargo"),
    'L',
    'M',
    'X',
    'J',
    'V',
    'S',
    'D',
];


/// $a_ctr_enc[$id_ubi][$id_tipo_enc][$id_enc][$dia_num_semana] = links
$a_ctr_enc_t = $EncargosZona->cuadriculaSemana();
$i = 0;
$a_valores = [];
foreach ($a_ctr_enc_t as $id_ubi => $a_ctr_enc) {
    $oCentroDl = new CentroDl($id_ubi);
    $nombre_ubi = $oCentroDl->getNombre_ubi();
    foreach ($a_ctr_enc as $id_tipo_enc => $a_id_enc) {
        $i++;
        $id_enc = key($a_id_enc);
        $oEncargo = new Encargo($id_enc);
        $desc_enc = $oEncargo->getDesc_enc();

        $a_cosas = ['id_zona' => $Qid_zona,
            'id_ubi' => $id_ubi,
            'id_enc' => $id_enc,
        ];
        $pagina = Hash::link(core\ConfigGlobal::getWeb() . '/apps/encargossacd/controller/encargo_horario_select.php?' . http_build_query($a_cosas));
        $texto_encargo = "<span class=link onclick=\"fnjs_modificar('$pagina');\">$desc_enc</span>";

        $a_valores[$i]['clase'] = 'tono2';
        $a_valores[$i][0] = "$id_ubi#$id_enc";
        $a_valores[$i][1] = $nombre_ubi;
        $a_valores[$i][2] = $texto_encargo;

        $c = 1;
        foreach ($a_cabeceras as $column) {
            $c++;
            if ($column === 'sel' || $column === _("Centro") || $column === _("encargo")) {
                continue;
            }
            $dia = $c - 4; //numérico de la semana: 1 = lunes
            $sacd = $a_ctr_enc[$id_tipo_enc][$id_enc][$dia];

            // buscar sacd encargado
            //$sacd = '??';
            $t_start = '??';
            $t_end = '??';

            $a_cosas = ['id_zona' => $Qid_zona,
                'id_ubi' => $id_ubi,
                'id_enc' => $id_enc,
                'dia' => $dia,
                //'id_item' => $id_item,
            ];
            $pagina = Hash::link(core\ConfigGlobal::getWeb() . '/apps/misas/controller/definicion_horario.php?' . http_build_query($a_cosas));
            $texto_nombre = "<span class=link onclick=\"fnjs_modificar('$pagina');\">$sacd</span>";

            $reloj = core\ConfigGlobal::getWeb_icons() . '/reloj.png';
            $pagina_horario = Hash::link(core\ConfigGlobal::getWeb() . '/apps/misas/controller/horario_tarea.php?' . http_build_query($a_cosas));
            $icono_horario = "<span class=link onclick=\"fnjs_modificar('$pagina_horario');\"><img src=\"$reloj\" width=\"12\" height=\"12\" style=\"float: right; margin: 0 0 15px 15px;\" alt=\"" . _("horario") . "\"></span>";
            if (($t_start !== '??') && ($t_start !== null) && ($t_end !== '??') && ($t_end !== null)) {
                $icono_horario = "<br><span class=link onclick=\"fnjs_modificar('$pagina_horario');\">$t_start - $t_end</span>";
            }
            if (($t_start !== '??') && ($t_start !== null) && (($t_end === '??') || ($t_end === null))) {
                $icono_horario = "<br><span class=link onclick=\"fnjs_modificar('$pagina_horario');\">$t_start</span>";
            }

            $a_valores[$i][$c] = "$texto_nombre  $icono_horario";
        }
    }
}

$oTabla = new Lista();
$oTabla->setId_tabla('crear_plantilla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

// para crear un nuevo encargo
$a_cosas = [
    'que' => 'nuevo',
    'grupo' => 8,
    'id_zona' => $Qid_zona,
];
$pagina_crear_encargo = Hash::link(core\ConfigGlobal::getWeb() . '/apps/encargossacd/controller/encargo_ver.php?' . http_build_query($a_cosas));

$url = 'apps/misas/controller/crear_plantilla.php';
$aQuery = ['id_zona' => $Qid_zona];
// el hppt_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$url_crear_plantilla = web\Hash::link($url . '?' . http_build_query($aQuery));

$url = '/apps/misas/controller/lista_ctr_zona.php';
// el hppt_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$pagina_lista_ctr_zona = web\Hash::link($url . '?' . http_build_query($aQuery));

$a_campos = ['oPosicion' => $oPosicion,
    'oTabla' => $oTabla,
    'url_crear_plantilla' => $url_crear_plantilla,
    'pagina_lista_ctr_zona' => $pagina_lista_ctr_zona,
    'pagina_crear_encargo' => $pagina_crear_encargo,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('crear_plantilla.html.twig', $a_campos);