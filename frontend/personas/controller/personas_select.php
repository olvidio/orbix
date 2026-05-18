<?php

namespace frontend\personas\controller;

use frontend\shared\AppInstalled;
use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;

/**
 * Tabla de personas que cumplen la condicion introducida en `personas_que`.
 *
 * Migrado desde `apps/personas/controller/personas_select.php` (slice 4) y
 * refactorizado para cumplir `refactor.md`: toda la obtencion de datos
 * (filtros contra repositorios, busqueda de centros, preferencia de usuario,
 * traduccion de `nivel_stgr`) vive ahora en
 * `src/personas/application/PersonasSelectData.php` tras el endpoint
 * `/src/personas/personas_select_data`. Este controlador frontend no importa
 * clases de `src\...`; construye la UI (`web\Lista`, `web\Hash`, botones JS)
 * con el array que devuelve el backend.
 */
require_once("frontend/shared/global_header_front.inc");

/** @var Posicion $oPosicion */
$oPosicion->recordar();

$tabla = (string)filter_input(INPUT_POST, 'tabla');
$Qna = (string)filter_input(INPUT_POST, 'na');
$tipo = (string)filter_input(INPUT_POST, 'tipo');
$Qes_sacd = (int)filter_input(INPUT_POST, 'es_sacd');

$Qid_sel = (string)filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
$Qque = (string)filter_input(INPUT_POST, 'que');
$Qexacto = (string)filter_input(INPUT_POST, 'exacto');
$Qcmb = (string)filter_input(INPUT_POST, 'cmb');
$Qnombre = (string)filter_input(INPUT_POST, 'nombre');
$Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');
$Qapellido2 = (string)filter_input(INPUT_POST, 'apellido2');
$Qcentro = (string)filter_input(INPUT_POST, 'centro');

// Si vengo por medio de Posicion, borro la ultima.
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            // Recupero campos de busqueda del stack para poder regenerar filtros.
            $Qque = $oPosicion2->getParametro('que') ?? $Qque;
            $Qexacto = $oPosicion2->getParametro('exacto') ?? $Qexacto;
            $Qcmb = $oPosicion2->getParametro('cmb') ?? $Qcmb;
            $Qnombre = $oPosicion2->getParametro('nombre') ?? $Qnombre;
            $Qapellido1 = $oPosicion2->getParametro('apellido1') ?? $Qapellido1;
            $Qapellido2 = $oPosicion2->getParametro('apellido2') ?? $Qapellido2;
            $Qcentro = $oPosicion2->getParametro('centro') ?? $Qcentro;
            $tabla = $oPosicion2->getParametro('tabla') ?? $tabla;
            $Qna = $oPosicion2->getParametro('na') ?? $Qna;
            $tipo = $oPosicion2->getParametro('tipo') ?? $tipo;
            $Qes_sacd = (int)($oPosicion2->getParametro('es_sacd') ?? $Qes_sacd);
            $oPosicion2->olvidar($stack);
        }
    }
}

// Guardo los datos actuales para poder volver.
$oPosicion->setParametros([
    'que' => $Qque,
    'exacto' => $Qexacto,
    'cmb' => $Qcmb,
    'nombre' => $Qnombre,
    'apellido1' => $Qapellido1,
    'apellido2' => $Qapellido2,
    'centro' => $Qcentro,
    'tabla' => $tabla,
    'na' => $Qna,
    'tipo' => $tipo,
    'es_sacd' => $Qes_sacd,
], 1);

$campos = [
    'tabla' => $tabla,
    'na' => $Qna,
    'tipo' => $tipo,
    'es_sacd' => $Qes_sacd,
    'exacto' => $Qexacto,
    'cmb' => $Qcmb,
    'nombre' => $Qnombre,
    'apellido1' => $Qapellido1,
    'apellido2' => $Qapellido2,
    'centro' => $Qcentro,
];

$data = PostRequest::getDataFromUrl('/src/personas/personas_select_data', $campos, false);
$aviso = '';
if (!empty($data['error'])) {
    $errorHtml = PostRequest::stripInternalCallProvenance((string)$data['error']);
    if (
        str_contains($errorHtml, _('persona no válida'))
        || str_contains($errorHtml, 'persona no válida')
        || str_contains($errorHtml, _('Delegaciones no dadas de alta'))
        || str_contains($errorHtml, 'Delegaciones no dadas de alta')
        || str_contains($errorHtml, _('Delegaciones sin región del stgr'))
        || str_contains($errorHtml, 'Delegaciones sin región del stgr')
        || str_contains($errorHtml, _('Personas del listado sin id_schema válido'))
        || str_contains($errorHtml, 'Personas del listado sin id_schema válido')
    ) {
        $aviso = $errorHtml;
        $data = [];
    } else {
        echo $errorHtml;
        return;
    }
}
$payload = is_array($data) ? $data : [];

$tabla = (string)($payload['tabla'] ?? $tabla);
$obj_pau = (string)($payload['obj_pau'] ?? '');
$id_tabla = (string)($payload['id_tabla'] ?? '');
$permiso = (int)($payload['permiso'] ?? 1);
$sPrefs = (string)($payload['sPrefs'] ?? '');
$total = (int)($payload['total'] ?? 0);
$a_filas = (array)($payload['personas'] ?? []);
$aviso = (string)($payload['aviso'] ?? $aviso);

// Botones y scripts: son UI (dependen de `$_SESSION['oPerm']`, apps instaladas
// y ambito) y se construyen en el frontend.
$a_botones = [];
$script = [];

if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
    $a_botones[] = ['txt' => _("cambio de ctr"), 'click' => 'fnjs_modificar_ctr("#seleccionados")'];
}
$script['fnjs_modificar_ctr'] = 1;
$a_botones[] = ['txt' => _("ver dossiers"), 'click' => 'fnjs_dossiers("#seleccionados")'];
$script['fnjs_dossiers'] = 1;
$a_botones[] = ['txt' => _("ficha"), 'click' => 'fnjs_ficha("#seleccionados")'];
$script['fnjs_ficha'] = 1;

if (AppInstalled::is('asistentes')) {
    $a_botones[] = ['txt' => _("ver actividades"), 'click' => 'fnjs_actividades("#seleccionados")'];
    $script['fnjs_actividades'] = 1;
}
if (AppInstalled::is('notas')) {
    if ($tabla === "p_numerarios" || $tabla === "p_agregados" || $tabla === "p_de_paso_ex") {
        $a_botones[] = ['txt' => _("ver tessera"), 'click' => 'fnjs_tessera("#seleccionados")'];
        $script['fnjs_tessera'] = 1;
    }
    if ($_SESSION['oPerm']->have_perm_oficina('est')) {
        $a_botones[] = ['txt' => _("modificar stgr"), 'click' => 'fnjs_modificar("#seleccionados")'];
        $script['fnjs_modificar'] = 1;
        $a_botones[] = ['txt' => _("imprimir tessera"), 'click' => 'fnjs_imp_tessera("#seleccionados")'];
        $script['fnjs_imp_tessera'] = 1;
        $a_botones[] = ['txt' => _("ver notas"), 'click' => 'fnjs_notas("#seleccionados")'];
        $script['fnjs_notas'] = 1;
    }
}
if (
    AppInstalled::is('actividadestudios')
    && ($_SESSION['oPerm']->have_perm_oficina('sm') || $_SESSION['oPerm']->have_perm_oficina('est'))
    && ($tabla === "p_numerarios" || $tabla === "p_agregados" || $tabla === "p_de_paso_ex")
) {
    $a_botones[] = ['txt' => _("posibles ca"), 'click' => 'fnjs_posibles_ca("#seleccionados")'];
    $script['fnjs_posibles_ca'] = 1;
}
if (AppInstalled::is('actividadplazas')) {
    if ($tabla === "p_numerarios" || $tabla === "p_agregados" || $tabla === "p_de_paso_ex") {
        $a_botones[] = ['txt' => _("petición ca"), 'click' => 'fnjs_peticion_activ("#seleccionados","ca")'];
        $a_botones[] = ['txt' => _("petición crt"), 'click' => 'fnjs_peticion_activ("#seleccionados","crt")'];
        $script['fnjs_posibles_activ'] = 1;
    }
}
if ($_SESSION['oPerm']->have_perm_oficina('est')) {
    if (AppInstalled::is('actividadestudios')) {
        $a_botones[] = ['txt' => _("plan estudios"), 'click' => 'fnjs_matriculas("#seleccionados")'];
        $script['fnjs_matriculas'] = 1;
    }
    if (AppInstalled::is('profesores')) {
        $a_botones[] = ['txt' => _("ficha profesor stgr"), 'click' => 'fnjs_ficha_profe("#seleccionados")'];
        $script['fnjs_ficha_profe'] = 1;
    }
    $a_botones[] = ['txt' => _("copiar tessera"), 'click' => 'fnjs_copiar_tessera("#seleccionados")'];
    $script['fnjs_copiar_tessera'] = 1;

    if (OrbixRuntime::miAmbito() === 'r') {
        $a_botones[] = ['txt' => _("imprimir certificado"), 'click' => 'fnjs_imp_certificado("#seleccionados")'];
        $script['fnjs_imp_certificado'] = 1;
        $a_botones[] = ['txt' => _("adjuntar certificado"), 'click' => 'fnjs_upload_certificado("#seleccionados")'];
        $script['fnjs_upload_certificado'] = 1;
    }
}

// Para rstgr, sobreescribe los botones.
if (OrbixRuntime::miAmbito() === 'rstgr') {
    $a_botones = [
        ['txt' => _("ver tessera"), 'click' => 'fnjs_tessera("#seleccionados")'],
        ['txt' => _("imprimir tessera"), 'click' => 'fnjs_imp_tessera("#seleccionados")'],
        ['txt' => _("imprimir certificado"), 'click' => 'fnjs_imp_certificado("#seleccionados")'],
        ['txt' => _("adjuntar certificado"), 'click' => 'fnjs_upload_certificado("#seleccionados")'],
        ['txt' => _("ficha profesor stgr"), 'click' => 'fnjs_ficha_profe("#seleccionados")'],
    ];
    $script = [
        'fnjs_tessera' => 1,
        'fnjs_imp_tessera' => 1,
        'fnjs_imp_certificado' => 1,
        'fnjs_upload_certificado' => 1,
        'fnjs_ficha_profe' => 1,
    ];
}

if (AppInstalled::is('actividadessacd') && $_SESSION['oPerm']->have_perm_oficina('des')) {
    $a_botones[] = ['txt' => _("atención actividades"), 'click' => 'fnjs_lista_activ("#seleccionados")'];
    $script['fnjs_lista_activ'] = 1;
}

$a_cabeceras = [
    ucfirst(_("tabla")),
    ['name' => _("nombre y apellidos"), 'width' => 250, 'formatter' => 'clickFormatter'],
];
if ($tabla === "p_sssc") {
    $a_cabeceras[] = ucfirst(_("socio"));
}
$a_cabeceras[] = ucfirst(_("centro"));
if ($tabla === "p_numerarios" || $tabla === "p_agregados" || $tabla === "p_de_paso_ex") {
    $a_cabeceras[] = ucfirst(_("stgr"));
}
if (!empty($Qcmb)) {
    $a_cabeceras[] = ucfirst(_("situación"));
    $a_cabeceras[] = ['name' => ucfirst(_("fecha cambio situación")), 'class' => 'fecha'];
}

$a_valores = [];
$c = 0;
foreach ($a_filas as $fila) {
    $c++;
    $id_nom = (int)($fila['id_nom'] ?? 0);
    $id_tabla_persona = (string)($fila['id_tabla'] ?? '');
    $nom = (string)($fila['nom'] ?? '');
    $nombre_ubi = (string)($fila['nombre_ubi'] ?? '');

    $a_val = [];
    $a_val['sel'] = "$id_nom#$id_tabla_persona";
    $a_val[1] = $id_tabla_persona;
    if ($sPrefs === 'html') {
        $pagina_persona = HashFront::link(
            AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/home_persona.php?'
            . http_build_query(['id_nom' => $id_nom, 'id_tabla' => $id_tabla_persona, 'obj_pau' => $obj_pau])
        );
        $a_val[2] = ['ira' => $pagina_persona, 'valor' => $nom];
    } else {
        $a_val[2] = ['script' => 'fnjs_home("#seleccionados")', 'valor' => $nom];
    }
    $a_val[4] = $nombre_ubi;
    if (($tabla === 'p_numerarios' || $tabla === 'p_agregados') && $tipo !== 'planning') {
        $a_val[5] = (string)($fila['nivel_stgr'] ?? '');
    }
    if (!empty($Qcmb)) {
        $a_val[6] = (string)($fila['situacion'] ?? '');
        $a_val[7] = (string)($fila['f_situacion'] ?? '');
    }
    $a_valores[$c] = $a_val;
}
if (!empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (!empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oTabla = new Lista();
$oTabla->setId_tabla("personas_select_$tabla");
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$pagina = HashFront::link(
    AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/personas_editar.php?'
    . http_build_query(['obj_pau' => $obj_pau, 'id_tabla' => $id_tabla, 'nuevo' => 1, 'apellido1' => $Qapellido1])
);

$oHash = new HashFront();
$oHash->setCamposForm('sel!que!id_dossier');
$oHash->setcamposNo('que!id_dossier!scroll_id');
$oHash->setArraycamposHidden([
    'pau' => 'p',
    'obj_pau' => $obj_pau,
    'tabla' => $tabla,
    'na' => $Qna,
    'permiso' => $permiso,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'script' => $script,
    'resultado' => sprintf(_("%s personas encontradas"), $total),
    'oTabla' => $oTabla,
    'pagina' => $pagina,
    'permiso' => $permiso,
    'aviso' => $aviso,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('personas_select.phtml', $a_campos);
