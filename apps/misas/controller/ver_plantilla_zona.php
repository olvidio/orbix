<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewTwig;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargoHorario;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use misas\model\EncargosZona;
use personas\model\entity\PersonaSacd;
use src\ubis\application\repositories\CentroDlRepository;
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
$CentroDlRepository = new CentroDlRepository();
foreach ($a_ctr_enc_t as $id_ubi => $a_ctr_enc) {
    $nombre_ubi = $CentroDlRepository->findById($id_ubi)->getNombre_ubi();
    foreach ($a_ctr_enc as $id_tipo_enc => $a_id_enc) {
        $i++;
        $id_enc = key($a_id_enc);
        $oEncargo = new Encargo($id_enc);
        $desc_enc = $oEncargo->getDesc_enc();

        $a_cosas = ['id_zona' => $Qid_zona,
            'id_ubi' => $id_ubi,
            'id_enc' => $id_enc,
            'origen' => 'misas',
        ];
        $pagina = Hash::link(ConfigGlobal::getWeb() . '/apps/encargossacd/controller/encargo_horario_select.php?' . http_build_query($a_cosas));
        $texto_encargo = "<span class=link onclick=\"fnjs_mostrar_modal('$pagina');\">$desc_enc</span>";

        $a_valores[$i]['clase'] = 'tono2';
        $a_valores[$i][0] = "$id_ubi#$id_enc";
        $a_valores[$i][1] = $nombre_ubi;
        $a_valores[$i][2] = $texto_encargo;

        // horarios del encargo_ctr
        $a_datos_encargo_horario = [];
        $gesEncargoHorarioCentro = new GestorEncargoHorario();
        $cEncargoHorarios = $gesEncargoHorarioCentro->getEncargoHorarios(['id_enc' => $id_enc]);
        foreach ($cEncargoHorarios as $oEncargoHorario) {
            $id_item_h = $oEncargoHorario->getId_item_h();
            $f_ini_iso = empty($oEncargoHorario->getF_ini()) ? '' : $oEncargoHorario->getF_ini()->getIso();
            $f_fin_iso = empty($oEncargoHorario->getF_fin()) ? '' : $oEncargoHorario->getF_fin()->getIso();
            $dia_ref = $oEncargoHorario->getDia_ref();
            $dia_num = $oEncargoHorario->getDia_num();
            $mas_menos = $oEncargoHorario->getMas_menos();
            $dia_inc = $oEncargoHorario->getDia_inc();
            $h_ini = $oEncargoHorario->getH_ini();
            $h_fin = $oEncargoHorario->getH_fin();
            $n_sacd = $oEncargoHorario->getN_sacd();
            $mes = $oEncargoHorario->getMes();

            if ($dia_ref === 'A') { //todos
                $datos_i = ['id_item_h' => $id_item_h,
                    'h_ini' => $h_ini,
                    'h_fin' => $h_fin,
                    'n_sacd' => $n_sacd,
                ];
                $a_dias_encargo = [1, 2, 3, 4, 5, 6, 7];
                $a_datos_encargo_horario = [1 => $datos_i,
                    2 => $datos_i,
                    3 => $datos_i,
                    4 => $datos_i,
                    5 => $datos_i,
                    6 => $datos_i,
                    7 => $datos_i
                ];
            } else {
                $datos_i = ['id_item_h' => $id_item_h,
                    'h_ini' => $h_ini,
                    'h_fin' => $h_fin,
                    'n_sacd' => $n_sacd,
                ];
                $a_dias_encargo = [$dia_ref]; // TODO debería ser el dia calculado...
                $a_datos_encargo_horario = [$dia_ref => $datos_i];
            }
        }

        $c = 1;
        foreach ($a_cabeceras as $column) {
            $c++;
            if ($column === 'sel' || $column === _("Centro") || $column === _("encargo")) {
                continue;
            }
            $dia = $c - 4; //numérico de la semana: 1 = lunes

            // si para este dia el encargo NO es activo, salto. (por defecto lo pongo en blanco.
            $a_valores[$i][$c] = "";
            if (!in_array($dia, $a_dias_encargo)) {
                continue;
            }

            $sacd = $a_ctr_enc[$id_tipo_enc][$id_enc][$dia];
            $id_sacd = 0;
            $id_item_horario_sacd = 0;
            // buscar sacd encargado
            $gesEncargoSacdHorario = new GestorEncargoSacdHorario();
            $aWhere = [
                'id_enc' => $id_enc,
                'dia_ref' => $dia,
            ];
            $aOperador = [];

            $cEncargoSacdHorario = $gesEncargoSacdHorario->getEncargoSacdHorarios($aWhere, $aOperador);
            if (!empty($cEncargoSacdHorario)) {
                $oEncargoHorarioSacd = $cEncargoSacdHorario[0];
                $id_sacd = $oEncargoHorarioSacd->getId_nom();
                $oPersonaSacd = new PersonaSacd($id_sacd);
                $sacd = $oPersonaSacd->getApellidosNombre();
                $id_item_horario_sacd = $oEncargoHorarioSacd->getId_item();
            }

            $id_item_h = empty($a_datos_encargo_horario[$dia]['id_item_h']) ? '' : $a_datos_encargo_horario[$dia]['id_item_h'];
            $t_start = empty($a_datos_encargo_horario[$dia]['h_ini']) ? '??' : $a_datos_encargo_horario[$dia]['h_ini'];
            $t_end = empty($a_datos_encargo_horario[$dia]['h_fin']) ? '??' : $a_datos_encargo_horario[$dia]['h_fin'];

            $a_cosas = ['id_zona' => $Qid_zona,
                'id_ubi' => $id_ubi,
                'id_enc' => $id_enc,
                'dia' => $dia,
                'id_item_h' => $id_item_h,
                'id_sacd' => $id_sacd,
                'id_item_horario_sacd' => $id_item_horario_sacd,
            ];
            $pagina = Hash::link(ConfigGlobal::getWeb() . '/apps/misas/controller/sacd_para_encargo.php?' . http_build_query($a_cosas));
            $texto_nombre = "<span class=link onclick=\"fnjs_mostrar_modal('$pagina');\">$sacd</span>";

            $reloj = ConfigGlobal::getWeb_icons() . '/reloj.png';
            $pagina_horario = Hash::link(ConfigGlobal::getWeb() . '/apps/misas/controller/horario_tarea.php?' . http_build_query($a_cosas));
            $icono_horario = "<span class=link onclick=\"fnjs_mostrar_modal('$pagina_horario');\"><img src=\"$reloj\" width=\"12\" height=\"12\" style=\"float: right; margin: 0 0 15px 15px;\" alt=\"" . _("horario") . "\"></span>";
            if (($t_start !== '??') && ($t_start !== null) && ($t_end !== '??') && ($t_end !== null)) {
                $icono_horario = "<br><span class=link onclick=\"fnjs_mostrar_modal('$pagina_horario');\">$t_start - $t_end</span>";
            }
            if (($t_start !== '??') && ($t_start !== null) && (($t_end === '??') || ($t_end === null))) {
                $icono_horario = "<br><span class=link onclick=\"fnjs_mostrar_modal('$pagina_horario');\">$t_start</span>";
            }

            $a_valores[$i][$c] = "$texto_nombre  $icono_horario";
        }
    }
}

$oTabla = new Lista();
$oTabla->setId_tabla('ver_plantilla_zona');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

// para crear un nuevo encargo
$a_cosas = [
    'que' => 'nuevo',
    'grupo' => 8,
    'id_zona' => $Qid_zona,
];
$pagina_crear_encargo = Hash::link(ConfigGlobal::getWeb() . '/apps/encargossacd/controller/encargo_ver.php?' . http_build_query($a_cosas));

$url = 'apps/misas/controller/ver_plantilla_zona.php';
$aQuery = ['id_zona' => $Qid_zona];
// el http_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$url_ver_plantilla_zona = Hash::link($url . '?' . http_build_query($aQuery));

$url = '/apps/misas/controller/lista_ctr_zona.php';
// el http_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$pagina_lista_ctr_zona = Hash::link($url . '?' . http_build_query($aQuery));

$a_campos = ['oPosicion' => $oPosicion,
    'oTabla' => $oTabla,
    'url_ver_plantilla_zona' => $url_ver_plantilla_zona,
    'pagina_lista_ctr_zona' => $pagina_lista_ctr_zona,
    'pagina_crear_encargo' => $pagina_crear_encargo,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('ver_plantilla_zona.html.twig', $a_campos);