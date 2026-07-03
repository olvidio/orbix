<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\shared\web\ContestarJson;
use src\shared\security\HashB;

$sfsv = ConfigGlobal::mi_sfsv();

$Qid_usuario = (integer)FilterPostGet::post('id_usuario');

$UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
$oUsuario = $UsuarioRepository->findById($Qid_usuario);
if ($oUsuario === null) {
    ContestarJson::enviar(_('Usuario no encontrado'), 'none');
    return;
}
$id_role = $oUsuario->getId_role();
$aWhere = [];
$aOperador = [];
// la tabla es de grupos y usuarios. Los grupos empiezan  por 5:
$aWhere['id_usuario'] = '^5';
$aOperador['id_usuario'] = '~';
// Ahora mismo no sé porque hay que filtrar por role. Para añadir se tienen que ver...
//$aWhere['id_role'] = $id_role;
// listado de grupos posibles
$GrupoRepository = DependencyResolver::get(GrupoRepositoryInterface::class);
$cGrupos = $GrupoRepository->getGrupos($aWhere,$aOperador);
// no pongo los que ya tengo. Los pongo en un array
$UsuarioGrupoRepository = DependencyResolver::get(UsuarioGrupoRepositoryInterface::class);
$cListaGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(array('id_usuario' => $Qid_usuario));
$aGruposOn = [];
foreach ($cListaGrupos as $oUsuarioGrupo) {
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
foreach ($cGrupos as $oGrupo) {
    $id_grupo = $oGrupo->getId_usuario();
    if (in_array($id_grupo, $aGruposOn)) {
        continue;
    }
    $i++;
    $nom_grupo = $oGrupo->getUsuarioAsString();
    $seccion = $asfsv[$sfsv];

    $ctx = HashB::sign('usuario_grupo_add', [
        'id_grupo' => $id_grupo,
        'id_usuario' => $Qid_usuario,
    ]);
    $param = http_build_query(['ctx' => $ctx]);
    $script = 'fnjs_add_grup(' . json_encode($param) . ')';

    $a_valores[$i][1] = $nom_grupo;
    $a_valores[$i][2] = $seccion;
    $a_valores[$i][3] = array('script' => $script, 'valor' => _("añadir"));

}

$error_txt = '';
$data['a_cabeceras'] = $a_cabeceras;
$data['a_botones'] = $a_botones;
$data['a_valores'] = $a_valores;

ContestarJson::enviar($error_txt, $data);