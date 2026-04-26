<?php

namespace src\configuracion\application;

use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\entity\Modulo;
use src\configuracion\domain\value_objects\AppsReq;
use src\configuracion\domain\value_objects\ModsReq;
use src\configuracion\domain\value_objects\ModuloDescription;
use src\configuracion\domain\value_objects\ModuloId;
use src\configuracion\domain\value_objects\ModuloName;

/**
 * Alta / baja / modificación de módulos (respuesta texto plano para AJAX legacy).
 */
final class ModulosUpdateAction
{
    /**
     * @param array<string, mixed> $post
     * @return string Texto de error; cadena vacía si OK
     */
    public static function run(array $post): string
    {
        $a_sel = isset($post['sel']) ? (array)$post['sel'] : [];

        $Qmod = (string)($post['mod'] ?? '');
        $Qid_mod = (int)($post['id_mod'] ?? 0);

        if ($a_sel !== []) {
            $Qid_mod = (int)urldecode(strtok((string)($a_sel[0] ?? ''), '#'));
        }

        $Qnom = (string)($post['nom'] ?? '');
        $Qdescripcion = (string)($post['descripcion'] ?? '');
        $Qsel_mods = isset($post['sel_mods']) && is_array($post['sel_mods'])
            ? filter_var($post['sel_mods'], FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY)
            : null;
        if ($Qsel_mods === false) {
            $Qsel_mods = null;
        }
        $Qsel_apps = isset($post['sel_apps']) && is_array($post['sel_apps'])
            ? filter_var($post['sel_apps'], FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY)
            : null;
        if ($Qsel_apps === false) {
            $Qsel_apps = null;
        }

        $ModuloRepository = $GLOBALS['container']->get(ModuloRepositoryInterface::class);

        switch ($Qmod) {
            case 'nuevo':
                if ($Qnom !== '') {
                    $newId = $ModuloRepository->getNewId();
                    $oModulo = new Modulo();
                    $oModulo->setIdModVo(new ModuloId($newId));
                    $oModulo->setNomVo(ModuloName::fromString($Qnom));
                    $oModulo->setDescripcionVo(ModuloDescription::fromNullableString($Qdescripcion));
                    $oModulo->setModsReqVo(ModsReq::fromNullableArray($Qsel_mods));
                    $oModulo->setAppsReqVo(AppsReq::fromNullableArray($Qsel_apps));
                    $ModuloRepository->Guardar($oModulo);
                }
                return '';

            case 'eliminar':
                $oModulo = $ModuloRepository->findById($Qid_mod);
                if ($ModuloRepository->Eliminar($oModulo) === false) {
                    return _("hay un error, no se ha eliminado") . "\n" . $ModuloRepository->getErrorTxt();
                }
                return '';

            default:
                $oModulo = $ModuloRepository->findById($Qid_mod);
                if (!empty($oModulo)) {
                    $oModulo->setNomVo(ModuloName::fromString($Qnom));
                    $oModulo->setDescripcionVo(ModuloDescription::fromNullableString($Qdescripcion));
                    $oModulo->setModsReqVo(ModsReq::fromNullableArray($Qsel_mods));
                    $oModulo->setAppsReqVo(AppsReq::fromNullableArray($Qsel_apps));
                    $ModuloRepository->Guardar($oModulo);
                }
                return '';
        }
    }
}
