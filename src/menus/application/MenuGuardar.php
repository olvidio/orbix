<?php

namespace src\menus\application;

use core\ConfigGlobal;
use src\menus\application\repositories\MenuDbRepository;
use src\menus\domain\entity\MenuDb;
use src\menus\domain\PermisoMenu;
use src\usuarios\domain\entity\Role;
use function core\is_true;

class MenuGuardar
{
    public function __invoke(int    $id_grupmenu,
                             int    $id_menu,
                             string $ok,
                             string $orden,
                             string $txt_menu,
                             string $parametros,
                             int    $id_metamenu,
                             array  $perm_menu
    ): string
    {

        $MenuDbRepository = new MenuDbRepository();
        $oPermisoMenu = new PermisoMenu;

        // si es nuevo
        if (empty($id_menu)) {
            $id_menu_new = $MenuDbRepository->getNewId();
            $oMenuDb = new MenuDb();
            $oMenuDb->setId_menu($id_menu_new);
        } else {
            $oMenuDb = $MenuDbRepository->findById($id_menu);
        }

        $oMiUsuario = ConfigGlobal::MiUsuario();
        $oRole = new Role();
        $oRole->setId_role($oMiUsuario->getId_role());


        //if ($oRole->isRole('SuperAdmin')) {
            $ok = is_true($ok);
            $oMenuDb->setOk($ok);
        //}
        $oMenuDb->setId_grupmenu($id_grupmenu);
        $oMenuDb->setMenu($txt_menu);
        $oMenuDb->setParametros($parametros);
        $oMenuDb->setId_metamenu($id_metamenu);
        //cuando el campo es perm_menu, se pasa un array que hay que convertirlo en integer.
        if (!empty($perm_menu)) {
            list ($ok0, $sum) = $oPermisoMenu->permsum_bit($perm_menu);
            if ($ok0) {
                $oMenuDb->setMenu_perm($sum);
            }
        }
        //cuando el campo es orden, se pasa csv que hay que convertirlo en un array.
        $a_orden = explode(',', $orden);
        $oMenuDb->setOrden($a_orden);

        $error_txt = '';
        if ($MenuDbRepository->Guardar($oMenuDb) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $MenuDbRepository->getErrorTxt();
        }

        return $error_txt;
    }
}