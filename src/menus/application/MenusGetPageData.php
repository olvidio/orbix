<?php

namespace src\menus\application;

use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\usuarios\domain\contracts\RoleRepositoryInterface;

/**
 * Datos para `frontend/menus/controller/menus_get.php` (formulario o listado).
 */
final class MenusGetPageData
{
    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    public static function execute(array $post): array
    {
        $Qfiltro_grupo = (string)($post['filtro_grupo'] ?? '');
        $Qnuevo = (string)($post['nuevo'] ?? '');
        $Qid_menu = (string)($post['id_menu'] ?? '');

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();
        $MenuDbRepository = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);

        $usuario = [
            'i_perm_menus' => (int)($_SESSION['iPermMenus'] ?? 1),
        ];

        if (!empty($Qid_menu) || !empty($Qnuevo)) {
            $edit = self::buildEditPayload($MenuDbRepository, $Qid_menu, $Qnuevo, $Qfiltro_grupo);

            return array_merge([
                'mode' => 'edit',
                'aRoles' => $aRoles,
                'usuario' => $usuario,
                'filtro_grupo' => $Qfiltro_grupo,
            ], $edit);
        }

        $menu_rows = [];
        if ($Qfiltro_grupo !== '') {
            $aWhere = ['id_grupmenu' => $Qfiltro_grupo, '_ordre' => 'orden'];
            $oMenuDbs = $MenuDbRepository->getMenuDbs($aWhere);
            foreach ($oMenuDbs as $oMenuDb) {
                $menu_rows[] = self::serializeMenuDb($oMenuDb);
            }
        }

        return [
            'mode' => 'list',
            'aRoles' => $aRoles,
            'usuario' => $usuario,
            'filtro_grupo' => $Qfiltro_grupo,
            'menu_rows' => $menu_rows,
        ];
    }

    private static function buildEditPayload(
        MenuDbRepositoryInterface $MenuDbRepository,
        string $Qid_menu,
        string $Qnuevo,
        string $Qfiltro_grupo
    ): array {
        if (!empty($Qid_menu)) {
            $oMenuDb = $MenuDbRepository->findById($Qid_menu);
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
    private static function serializeMenuDb(MenuDb $oMenuDb): array
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
