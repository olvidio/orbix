<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\criteria\GrupMenuRoleField;
use src\menus\domain\entity\GrupMenuRole;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

if (!empty($a_sel)) { //vengo de un checkbox
    $GrupMenuRoleRepository = DependencyResolver::get(GrupMenuRoleRepositoryInterface::class);
    foreach ($a_sel as $sel) {
        if (!is_string($sel) || $sel === '') {
            continue;
        }
        try {
            $ctx = HashB::open($sel, 'role_grupmenu_add');
        } catch (HashBInvalidException $e) {
            continue;
        }
        $id_role = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_role');
        $id_grupmenu = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_grupmenu');
        $cGrupMenuRoles = $GrupMenuRoleRepository->matching(
            Criteria::create()
                ->where(Filter::eq(GrupMenuRoleField::IdRole, $id_role))
                ->andWhere(Filter::eq(GrupMenuRoleField::IdGrupmenu, $id_grupmenu))
        );
        if (empty($cGrupMenuRoles)) {
            $id_item = $GrupMenuRoleRepository->getNewId();
            $oGrupMenuRole = new GrupMenuRole();
            $oGrupMenuRole->setId_item($id_item);
            $oGrupMenuRole->setId_role($id_role);
            $oGrupMenuRole->setId_grupmenu($id_grupmenu);
        } else {
            $oGrupMenuRole = $cGrupMenuRoles[0];
        }

        if ($GrupMenuRoleRepository->Guardar($oGrupMenuRole) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $GrupMenuRoleRepository->getErrorTxt();
        }
    }
} else {
    $error_txt = _("debe seleccionar uno");
}

ContestarJson::enviar($error_txt, 'ok');