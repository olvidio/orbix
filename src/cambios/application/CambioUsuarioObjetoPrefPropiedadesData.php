<?php

namespace src\cambios\application;

use src\cambios\domain\AvisoObjetoCatalog;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use src\shared\domain\helpers\FuncTablasSupport;

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
    public function __construct(
        private CambioUsuarioPropiedadPrefRepositoryInterface $cambioUsuarioPropiedadPrefRepository,
    ) {
    }

    /**
     * @param array{
     *   objeto?: string,
     *   id_item_usuario_objeto?: int|string,
     * } $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
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
            $cListaCampos = $this->cambioUsuarioPropiedadPrefRepository->getCambioUsuarioPropiedadPrefs(
                ['id_item_usuario_objeto' => $id_item_usuario_objeto]
            );
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

        $oMiUsuario = ConfigGlobal::MiUsuario();
        if ($oMiUsuario === null) {
            $result['error'] = (string)_('Usuario no encontrado');
            return $result;
        }
        $idRole = $oMiUsuario->getId_role();
        if ($idRole === null) {
            $result['error'] = (string)_('Usuario sin rol asignado');
            return $result;
        }
        $oRole = new Role();
        $oRole->setId_role($idRole);

        if (!array_key_exists($objeto, AvisoObjetoCatalog::getArrayObjetosPosibles())) {
            $result['error'] = sprintf((string)_("objeto %s no encontrado"), $objeto);
            return $result;
        }

        $cDatosCampos = CambioObjetoDatosCampos::forObjeto($objeto);

        $propiedades = [];
        foreach ($cDatosCampos as $oDatosCampo) {
            $nom_prop = $oDatosCampo->getNom_camp();
            if ($nom_prop === 'id_schema') {
                continue;
            }
            $condicion_aviso = $oDatosCampo->getAviso();
            if (!FuncTablasSupport::isTrue($condicion_aviso)) {
                continue;
            }

            $etiqueta = $oDatosCampo->getEtiqueta();

            $chk_prop = '';
            $id_item = '';
            $cambio_prop = '';
            $condicion = (string)_("cualquier cambio");

            $key = array_search($nom_prop, $a_propiedades_sel, true);
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
