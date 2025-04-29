<?php

use core\ConfigGlobal;
use src\usuarios\application\repositories\GrupoRepository;
use src\usuarios\application\repositories\UsuarioGrupoRepository;
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
// listado de grupos posibles
$GrupoRepository = new GrupoRepository();
$cGrupos = $GrupoRepository->getGrupos();
// no pongo los que ya tengo. Los pongo en un array
$UsuarioGrupoRepository = new UsuarioGrupoRepository();
$cListaGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(array('id_usuario' => $Qid_usuario));
$aGruposOn = [];
foreach ($cListaGrupos as $oUsuarioGrupo) {
    $aGruposOn[] = $oUsuarioGrupo->getId_grupo();
}
$i = 0;
$a_botones = [];
$a_cabeceras = array('usuario', 'seccion', array('name' => 'accion', 'formatter' => 'clickFormatter'));
$a_valores = [];
$asfsv = array(1 => 'sv', 2 => 'sf');
foreach ($cListaGrupos as $oUsuarioGrupo) {
    $i++;
    $id_grupo = $oUsuarioGrupo->getId_grupo();
    $oGrupo = $GrupoRepository->findById($id_grupo);
    $usuario = $oGrupo->getUsuario();
    $seccion = $asfsv[$sfsv];

    /*
    $a_parametros = array('id_grupo' => $id_grupo, 'id_usuario' => $Qid_usuario);
    $pagina = Hash::link(ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_grupo_del.php?' . http_build_query($a_parametros));
    */
    $oHash = new Hash();
    $a_camposHidden = array(
        'id_grupo' => $id_grupo,
        'id_usuario' => $Qid_usuario,
    );
    $oHash->setArraycamposHidden($a_camposHidden);
    $param = $oHash->getParamAjax();

    $script = "fnjs_del_grup(\"$param\")";

    $a_valores[$i][1] = $usuario;
    $a_valores[$i][2] = $seccion;
    $a_valores[$i][3] = array('script' => $script, 'valor' => _("quitar"));
}

$error_txt = '';
$data['a_cabeceras'] = $a_cabeceras;
$data['a_botones'] = $a_botones;
$data['a_valores'] = $a_valores;

ContestarJson::enviar($error_txt, $data);