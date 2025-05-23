<?php

use core\ConfigGlobal;
use procesos\model\CuadrosFases;
use procesos\model\PermAccion;
use procesos\model\PermAfectados;
use src\shared\ViewSrcPhtml;
use src\usuarios\application\repositories\GrupoRepository;
use src\usuarios\application\repositories\UsuarioGrupoRepository;
use src\usuarios\domain\entity\Role;

$oCuadrosAfecta = new PermAfectados();
$oPermAccion = new PermAccion();
$oCuadrosFases = new CuadrosFases();

//////////// Permisos de grupos ////////////
if (!empty($Qid_usuario)) { // si no hay usuario, no puedo poner permisos.
    //grupo
    $GrupoRepository = new GrupoRepository();
    $UsuarioGrupoRepository = new UsuarioGrupoRepository();
    $cGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(array('id_usuario' => $Qid_usuario));
    $i = 0;
    $txt = '';
    foreach ($cGrupos as $oUsuarioGrupo) {
        $i++;
        $id_grupo = $oUsuarioGrupo->getId_grupo();
        $oGrupo = $GrupoRepository->findById($id_grupo);
        if ($i > 1) $txt .= ", ";
        $txt .= $oGrupo->getUsuario();
    }
    ?>
    <br>
    <b><?= _("grupos") ?>: </b>
    <?php
    //////////// Aclaración permisos ////////////
    if (ConfigGlobal::is_app_installed('procesos')) {
        ?>
        <p class="comentario"><?= _("OJO: los permisos en los grupos no tienen una preferencia definida.") ?></p>
        <p class="comentario"><?= _("Si hay más de uno, deberían ser independientes, sino no se sabe cual sobreescribirá a cual.") ?></p>
        <?php
    } ?>
    <br>
    <p><?= $txt ?></p>
    <br>
    <input type=button onclick="fnjs_add_grup();" value="<?= _("añadir un grupo de permisos") ?>">
    <input type=button onclick="fnjs_del_grup();" value="<?= _("quitar de un grupo de permisos") ?>">
    <div id=lst_grupos></div>
    <br>
    <br>
    <?php
    //////////// Permisos en centros ////////////
    if (ConfigGlobal::is_app_installed('ubis')) {
        if ($pau === Role::PAU_NOM || $pau === Role::PAU_SACD) { //sacd //personas dl
            $a_campos = [
                'quien' => $Qquien,
                'id_usuario' => $Qid_usuario,
                'usuario' => $usuario,
                'cUsuarioPermCtr' => $cUsuarioPermCtr,
                'oCuadrosAfecta' => $oCuadrosAfecta,
                'oPermAccion' => $oPermAccion,
            ];

            $oView = new ViewSrcPhtml('src\usuarios\controller');
            $oView->renderizar('perm_ctr_form.phtml', $a_campos);
        }
    }
    //////////// Permisos en actividades ////////////
    if (ConfigGlobal::is_app_installed('procesos')) {

        $a_campos = [
            'quien' => $Qquien,
            'id_usuario' => $Qid_usuario,
            'usuario' => $usuario,
            'cUsuarioPerm' => $cUsuarioPerm,
            'oCuadrosAfecta' => $oCuadrosAfecta,
            'oCuadrosFases' => $oCuadrosFases,
            'oPermAccion' => $oPermAccion,
        ];

        $oView = new ViewSrcPhtml('src\usuarios\controller');
        $oView->renderizar('perm_activ_form.phtml', $a_campos);
    }

}