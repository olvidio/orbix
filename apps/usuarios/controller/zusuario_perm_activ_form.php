<?php

//////////// Permisos en actividades ////////////
use core\ConfigGlobal;
use core\ViewPhtml;

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

    $oView = new ViewPhtml('usuarios/controller');
    $oView->renderizar('perm_activ_form.phtml', $a_campos);
}