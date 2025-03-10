<?php

use core\ConfigGlobal;
use usuarios\model\entity\GestorGrupo;
use usuarios\model\entity\GestorUsuarioGrupo;
use usuarios\model\entity\Usuario;
use web\ContestarJson;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$sfsv = ConfigGlobal::mi_sfsv();

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$oUsuario = new Usuario(array('id_usuario' => $Qid_usuario));
$id_role = $oUsuario->getId_role();
$aWhere = array();
// Ahora mismo no sé porque hay que filtrar por role. Para añadir se tienen que ver...
//$aWhere['id_role'] = $id_role;
// listado de grupos posibles
$oGesGrupos = new GestorGrupo();
$oGrupoColeccion = $oGesGrupos->getGrupos($aWhere);
// no pongo los que ya tengo. Los pongo en un array
$oGesUsuarioGrupo = new GestorUsuarioGrupo();
$oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario' => $Qid_usuario));
$aGruposOn = array();
foreach ($oListaGrupos as $oUsuarioGrupo) {
    $aGruposOn[] = $oUsuarioGrupo->getId_grupo();
}
$i = 0;
$a_botones = [];
$a_cabeceras = [_("grupo"),
    _("sección"),
    ['name' => _("acción"),
        'formatter' => 'clickFormatter'
    ],
];
$a_valores = [];
$asfsv = array(1 => 'sv', 2 => 'sf');
foreach ($oGrupoColeccion as $oGrupo) {
    $id_grupo = $oGrupo->getId_usuario();
    if (in_array($id_grupo, $aGruposOn)) {
        continue;
    }
    $i++;
    $nom_grupo = $oGrupo->getUsuario();
    $seccion = $asfsv[$sfsv];

    //$a_parametros = array('id_grupo' => $id_grupo, 'id_usuario' => $Qid_usuario);
    //$pagina = Hash::link(ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_grupo_add.php?' . http_build_query($a_parametros));

    $oHash = new Hash();
    $a_camposHidden = array(
        'id_grupo' => $id_grupo,
        'id_usuario' => $Qid_usuario,
    );
    $oHash->setArraycamposHidden($a_camposHidden);
    $param = $oHash->getParamAjax();

    $script = "fnjs_add_grup(\"$param\")";

    $a_valores[$i][1] = $nom_grupo;
    $a_valores[$i][2] = $seccion;
    $a_valores[$i][3] = array('script' => $script, 'valor' => _("añadir"));

}

$error_txt = '';
$data['a_cabeceras'] = $a_cabeceras;
$data['a_botones'] = $a_botones;
$data['a_valores'] = $a_valores;

ContestarJson::enviar($error_txt, $data);