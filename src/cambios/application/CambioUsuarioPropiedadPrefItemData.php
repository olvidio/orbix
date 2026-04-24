<?php

namespace src\cambios\application;

use core\ConfigGlobal;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;

/**
 * Data builder: datos de la condicion configurada para una propiedad
 * (si `id_item` > 0) junto con las opciones de casas cuando la propiedad es
 * `id_ubi`. Sucesor de la rama `condicion` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */
final class CambioUsuarioPropiedadPrefItemData
{
    /**
     * @param array{
     *   id_item?: int|string,
     *   objeto?: string,
     *   propiedad?: string,
     * } $input
     * @return array
     */
    public static function execute(array $input): array
    {
        $id_item = (int)($input['id_item'] ?? 0);
        $objeto = (string)($input['objeto'] ?? '');
        $propiedad = (string)($input['propiedad'] ?? '');

        $valor = '';
        $operador = '';
        $chk_old = 'checked';
        $chk_new = 'checked';

        if ($id_item > 0) {
            $CambioUsuarioPropiedadPrefRepository = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);
            $oProp = $CambioUsuarioPropiedadPrefRepository->findById($id_item);
            if ($oProp !== null) {
                $valor = (string)($oProp->getValor() ?? '');
                $operador = (string)($oProp->getOperador() ?? '');
                if ($operador !== '') {
                    $chk_old = $oProp->isValor_old() ? 'checked' : '';
                    $chk_new = $oProp->isValor_new() ? 'checked' : '';
                }
            }
        }

        $aOpcionesCasas = [];
        if ($propiedad === 'id_ubi') {
            $oMiUsuario = ConfigGlobal::MiUsuario();
            $oRole = new Role();
            $oRole->setId_role($oMiUsuario->getId_role());
            $miSfsv = ConfigGlobal::mi_sfsv();
            $donde = '';
            if ($oRole->isRolePau(PauType::PAU_CDC)) {
                $id_pau = $oMiUsuario->getCsv_id_pau();
                $sDonde = str_replace(',', ' OR id_ubi=', (string)$id_pau);
                $donde = "WHERE active='t' AND (id_ubi=$sDonde)";
            } elseif ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
                $donde = "WHERE active='t'";
            } else {
                if ($miSfsv === 1) {
                    $donde = "WHERE active='t' AND sv='t'";
                }
                if ($miSfsv === 2) {
                    $donde = "WHERE active='t' AND sf='t'";
                }
            }
            $CasaDlRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
            $aOpcionesCasas = $CasaDlRepository->getArrayCasas($donde);
        }

        return [
            'error' => '',
            'id_item' => $id_item,
            'objeto' => $objeto,
            'propiedad' => $propiedad,
            'valor' => $valor,
            'operador' => $operador,
            'chk_old' => $chk_old,
            'chk_new' => $chk_new,
            'aOpcionesCasas' => $aOpcionesCasas,
        ];
    }
}
