<?php

namespace src\menus\application;

use src\shared\config\ConfigGlobal;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\menus\domain\PermisoMenuBits;
use src\usuarios\domain\entity\Role;
use function src\shared\domain\helpers\is_true;

class MenuGuardar
{
    public function __construct(
        private MenuDbRepositoryInterface $menuDbRepository,
    ) {
    }

    /**
     * @param list<int|string> $perm_menu
     */
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


        // si es nuevo
        if (empty($id_menu)) {
            $id_menu_new = $this->menuDbRepository->getNewId();
            $oMenuDb = new MenuDb();
            $oMenuDb->setId_menu($id_menu_new);
        } else {
            $oMenuDb = $this->menuDbRepository->findById($id_menu);
            if ($oMenuDb === null) {
                return _("No encuentro el menu");
            }
        }

        $oMiUsuario = ConfigGlobal::MiUsuario();
        $oRole = new Role();
        $oRole->setId_role($oMiUsuario?->getId_role() ?? 0);

        //if ($oRole->isRole('SuperAdmin')) {
            $ok = is_true($ok);
            $oMenuDb->setOk($ok);
        //}
        $oMenuDb->setId_grupmenu($id_grupmenu);
        $oMenuDb->setMenu($txt_menu);
        $oMenuDb->setParametros($parametros);
        $oMenuDb->setId_metamenu($id_metamenu);
        //cuando el campo es perm_menu, se pasa un array que hay que convertirlo en integer.
        if ($perm_menu !== []) {
            $oMenuDb->setMenu_perm(PermisoMenuBits::combineSelectedBits($perm_menu));
        }
        //cuando el campo es orden, se pasa csv que hay que convertirlo en un array.
        $a_orden = array_map(static fn (string $v): int => (int) $v, explode(',', $orden));
        $oMenuDb->setOrden($a_orden);

        $error_txt = '';
        if ($this->menuDbRepository->Guardar($oMenuDb) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $this->menuDbRepository->getErrorTxt();
        }

        return $error_txt;
    }
}