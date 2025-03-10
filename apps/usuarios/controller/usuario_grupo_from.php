<?php

//////////// Permisos de grupos ////////////
use core\ConfigGlobal;
use core\ViewPhtml;
use usuarios\model\entity\Role;

if (!empty($Qid_usuario)) { // si no hay usuario, no puedo poner permisos.
    //grupo
    $oGesUsuarioGrupo = new usuarios\model\entity\GestorUsuarioGrupo();
    $oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario' => $Qid_usuario));
    $i = 0;
    $txt = '';
    foreach ($oListaGrupos as $oUsuarioGrupo) {
        $i++;
        $oGrupo = new usuarios\model\entity\Grupo($oUsuarioGrupo->getId_grupo());
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
        if ($pau == Role::PAU_NOM || $pau == Role::PAU_SACD) { //sacd //personas dl
            $a_campos = [
                'quien' => $Qquien,
                'id_usuario' => $Qid_usuario,
                'usuario' => $usuario,
                'cUsuarioPermCtr' => $cUsuarioPermCtr,
                'oCuadrosAfecta' => $oCuadrosAfecta,
                'oPermAccion' => $oPermAccion,
            ];

            $oView = new ViewPhtml('usuarios/controller');
            $oView->renderizar('perm_ctr_form.phtml', $a_campos);
        }
    }
}