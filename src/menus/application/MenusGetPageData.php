<?php

namespace src\menus\application;

use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\menus\domain\PermisoMenuBits;
use src\usuarios\domain\contracts\RoleRepositoryInterface;

/**
 * Datos para `frontend/menus/controller/menus_get.php` (formulario o listado).
 */
final class MenusGetPageData
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private MenuDbRepositoryInterface $menuDbRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    public function execute(array $post): array
    {
        $Qfiltro_grupo = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'filtro_grupo');
        $Qnuevo = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'nuevo');
        $Qid_menu = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'id_menu');

        $aRoles = $this->roleRepository->getArrayRoles();

        $iPermMenus = $_SESSION['iPermMenus'] ?? 1;
        $usuario = [
            'i_perm_menus' => is_numeric($iPermMenus) ? (int) $iPermMenus : 1,
        ];

        $perm_menu_bit_map = PermisoMenuBits::map();

        if (!empty($Qid_menu) || !empty($Qnuevo)) {
            $edit = $this->buildEditPayload($Qid_menu, $Qnuevo, $Qfiltro_grupo);

            return array_merge([
                'mode' => 'edit',
                'aRoles' => $aRoles,
                'usuario' => $usuario,
                'filtro_grupo' => $Qfiltro_grupo,
                'perm_menu_bit_map' => $perm_menu_bit_map,
            ], $edit);
        }

        $menu_rows = [];
        if ($Qfiltro_grupo !== '') {
            $aWhere = ['id_grupmenu' => $Qfiltro_grupo, '_ordre' => 'orden'];
            $oMenuDbs = $this->menuDbRepository->getMenuDbs($aWhere);
            foreach ($oMenuDbs as $oMenuDb) {
                $menu_rows[] = $this->serializeMenuDb($oMenuDb);
            }
        }

        return [
            'mode' => 'list',
            'aRoles' => $aRoles,
            'usuario' => $usuario,
            'filtro_grupo' => $Qfiltro_grupo,
            'menu_rows' => $menu_rows,
            'perm_menu_bit_map' => $perm_menu_bit_map,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEditPayload(
        string $Qid_menu,
        string $Qnuevo,
        string $Qfiltro_grupo
    ): array {
        if (!empty($Qid_menu)) {
            $oMenuDb = $this->menuDbRepository->findById((int) $Qid_menu);
            if ($oMenuDb === null) {
                throw new \RuntimeException(_('No encuentro el menu'));
            }
            $oMenuDb->setId_menu((int)$Qid_menu);

            $orden = $oMenuDb->getOrden();
            $orden_txt = implode(',', $orden ?? []);
            $menu = $oMenuDb->getMenu();
            $parametros = $oMenuDb->getParametros();
            $id_metamenu = $oMenuDb->getId_metamenu();
            $menu_perm = $oMenuDb->getMenu_perm();
            $id_grupmenu = $oMenuDb->getId_grupmenu();
            $ok = $oMenuDb->isOk();

            $chk = ($ok) ? 'checked' : '';
        } else {
            $Qid_menu = '';
            $orden_txt = '';
            $menu = '';
            $parametros = '';
            $menu_perm = 0;
            $id_metamenu = null;
            $id_grupmenu = null;
            $chk = '';
        }
        $txt_ok = "  es ok?<input type='checkbox' name='ok' $chk >";
        $campos_chk = 'ok';

        return [
            'id_menu' => $Qid_menu,
            'nuevo' => $Qnuevo,
            'orden_txt' => $orden_txt,
            'menu' => $menu ?? '',
            'parametros' => $parametros ?? '',
            'id_metamenu' => $id_metamenu,
            'menu_perm' => $menu_perm,
            'id_grupmenu' => $id_grupmenu,
            'txt_ok' => $txt_ok,
            'campos_chk' => $campos_chk,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeMenuDb(MenuDb $oMenuDb): array
    {
        return [
            'id_menu' => $oMenuDb->getId_menu(),
            'orden' => $oMenuDb->getOrden(),
            'menu' => $oMenuDb->getMenu(),
            'parametros' => $oMenuDb->getParametros(),
            'id_metamenu' => $oMenuDb->getId_metamenu(),
            'menu_perm' => $oMenuDb->getMenu_perm(),
            'id_grupmenu' => $oMenuDb->getId_grupmenu(),
            'ok' => $oMenuDb->isOk(),
        ];
    }
}
