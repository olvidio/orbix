<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\shared\web\ContestarJson;
use src\shared\security\HashB;

$sfsv = ConfigGlobal::mi_sfsv();

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
// listado de grupos posibles
$GrupoRepository = DependencyResolver::get(GrupoRepositoryInterface::class);
$cGrupos = $GrupoRepository->getGrupos();
// no pongo los que ya tengo. Los pongo en un array
$UsuarioGrupoRepository = DependencyResolver::get(UsuarioGrupoRepositoryInterface::class);
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
    if ($oGrupo === null) {
        continue;
    }
    $usuario = $oGrupo->getUsuarioAsString();
    $seccion = $asfsv[$sfsv];

    $ctx = HashB::sign('usuario_grupo_del', [
        'id_grupo' => $id_grupo,
        'id_usuario' => $Qid_usuario,
    ]);
    $param = http_build_query(['ctx' => $ctx]);
    $script = 'fnjs_del_grup(' . json_encode($param) . ')';

    $a_valores[$i][1] = $usuario;
    $a_valores[$i][2] = $seccion;
    $a_valores[$i][3] = array('script' => $script, 'valor' => _("quitar"));
}

$error_txt = '';
$data['a_cabeceras'] = $a_cabeceras;
$data['a_botones'] = $a_botones;
$data['a_valores'] = $a_valores;

ContestarJson::enviar($error_txt, $data);