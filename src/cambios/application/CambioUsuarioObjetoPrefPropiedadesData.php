<?php

namespace src\cambios\application;

use src\shared\config\ConfigGlobal;
use src\cambios\domain\AvisoObjetoCatalog;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use function src\shared\domain\helpers\is_true;

/**
 * Data builder: listado de campos configurables del objeto seleccionado,
 * preseleccionados con las preferencias ya guardadas para un
 * `CambioUsuarioObjetoPref`.
 *
 * Sucesor de la rama `propiedades` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */
final class CambioUsuarioObjetoPrefPropiedadesData
{
    /**
     * @param array{
     *   objeto?: string,
     *   id_item_usuario_objeto?: int|string,
     * } $input
     * @return array
     */
    public static function execute(array $input): array
    {
        $objeto = (string)($input['objeto'] ?? '');
        $id_item_usuario_objeto = (int)($input['id_item_usuario_objeto'] ?? 0);

        $result = [
            'error' => '',
            'objeto' => $objeto,
            'id_item_usuario_objeto' => $id_item_usuario_objeto,
            'propiedades' => [],
        ];

        if ($objeto === '') {
            return $result;
        }

        $a_item_sel = [];
        $a_propiedades_sel = [];
        $a_condicion_sel = [];
        $a_cambio_propiedad_sel = [];
        if ($id_item_usuario_objeto > 0) {
            $CambioUsuarioPropiedadPrefRepository = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);
            $cListaCampos = $CambioUsuarioPropiedadPrefRepository->getCambioUsuarioPropiedadPrefs(
                ['id_item_usuario_objeto' => $id_item_usuario_objeto]
            );
            if (is_array($cListaCampos)) {
                $c = 0;
                foreach ($cListaCampos as $oProp) {
                    $c++;
                    $a_item_sel[$c] = $oProp->getId_item();
                    $a_propiedades_sel[$c] = $oProp->getPropiedad();
                    $a_condicion_sel[$c] = $oProp->getTextCambio();
                    $a_cambio_propiedad_sel[$c] = json_encode([
                        'iid_item' => $oProp->getId_item(),
                        'spropiedad' => $oProp->getPropiedad(),
                        'soperador' => (string)($oProp->getOperador() ?? ''),
                        'svalor' => (string)($oProp->getValor() ?? ''),
                        'bvalor_old' => $oProp->isValor_old() ? 't' : 'f',
                        'bvalor_new' => $oProp->isValor_new() ? 't' : 'f',
                    ]);
                }
            }
        }

        $oMiUsuario = ConfigGlobal::MiUsuario();
        $oRole = new Role();
        $oRole->setId_role($oMiUsuario->getId_role());

        $ObjetoFullPath = AvisoObjetoCatalog::getFullPathObj($objeto);
        if (!class_exists($ObjetoFullPath)) {
            $result['error'] = sprintf((string)_("objeto %s no encontrado"), $objeto);
            return $result;
        }
        $oObject = new $ObjetoFullPath();
        $cDatosCampos = $oObject->getDatosCampos();

        $propiedades = [];
        foreach ($cDatosCampos as $oDatosCampo) {
            $nom_prop = $oDatosCampo->getNom_camp();
            if ($nom_prop === 'id_schema') {
                continue;
            }
            $condicion_aviso = $oDatosCampo->getAviso();
            if (!is_true($condicion_aviso)) {
                continue;
            }

            $etiqueta = $oDatosCampo->getEtiqueta();

            $chk_prop = '';
            $id_item = '';
            $cambio_prop = '';
            $condicion = (string)_("cualquier cambio");

            $key = array_search($nom_prop, $a_propiedades_sel);
            if ($key !== false) {
                $chk_prop = 'checked';
                $condicion = empty($a_condicion_sel[$key]) ? (string)_("cualquier cambio") : (string)$a_condicion_sel[$key];
                $id_item = (string)$a_item_sel[$key];
                $cambio_prop = (string)$a_cambio_propiedad_sel[$key];
            } elseif ($nom_prop === 'id_ubi' && $oRole->isRolePau(PauType::PAU_CDC)) {
                $chk_prop = 'checked';
                $condicion = (string)_("ja veurem");
            }

            $propiedades[] = [
                'nom_prop' => $nom_prop,
                'etiqueta' => $etiqueta,
                'chk_prop' => $chk_prop,
                'id_item' => $id_item,
                'cambio_prop' => $cambio_prop,
                'condicion' => $condicion,
            ];
        }

        $result['propiedades'] = $propiedades;
        return $result;
    }
}
