<?php

namespace src\cambios\application;

use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioPropiedadPref;

/**
 * Mutacion: sincroniza las propiedades vigiladas (`CambioUsuarioPropiedadPref`)
 * para un `CambioUsuarioObjetoPref`. Crea, actualiza o elimina segun la
 * seleccion (`objeto[]`) y los metadatos (`id_cond`, `id_item`) presentes
 * en el POST.
 *
 * Sucesor de la rama `guardar_propiedades` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */
final class CambioUsuarioPropiedadPrefGuardarTodas
{
    public function __construct(
        private CambioUsuarioPropiedadPrefRepositoryInterface $cambioUsuarioPropiedadPrefRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input POST completo (se accede a `id_item_usuario_objeto_prop`, `objeto_prop`, $objeto[] y $id_cond).
     * @return array{error: string}
     */
    public function execute(array $input): array
    {
        $id_item_usuario_objeto = isset($input['id_item_usuario_objeto_prop']) && is_numeric($input['id_item_usuario_objeto_prop'])
            ? (int) $input['id_item_usuario_objeto_prop']
            : 0;
        $objeto = isset($input['objeto_prop']) && is_string($input['objeto_prop'])
            ? $input['objeto_prop']
            : '';

        if ($id_item_usuario_objeto <= 0 || $objeto === '') {
            return ['error' => (string)_("faltan parametros")];
        }

        $a_propiedades_sel = [];

        $a_conds = $input[$objeto] ?? [];
        if (!is_array($a_conds)) {
            $a_conds = [];
        }
        foreach ($a_conds as $id_cond) {
            if (!is_scalar($id_cond)) {
                continue;
            }
            $id_cond = (string) $id_cond;
            $nom_prop_cond = substr(strstr($id_cond, '_') ?: '', 1);
            $nom_prop = strstr($nom_prop_cond, '_cond', true);
            if ($nom_prop === false || $nom_prop === '') {
                continue;
            }
            $a_propiedades_sel[] = $nom_prop;

            $payload_raw = isset($input[$id_cond]) && is_string($input[$id_cond]) ? $input[$id_cond] : '';
            if ($payload_raw !== '') {
                $decoded = json_decode($payload_raw, true);
                $aCambio = is_array($decoded) ? $decoded : [];
                $oProp = new CambioUsuarioPropiedadPref();
                $id_item = isset($aCambio['iid_item']) && is_numeric($aCambio['iid_item'])
                    ? (int) $aCambio['iid_item']
                    : 0;
                if ($id_item <= 0) {
                    $id_item = $this->cambioUsuarioPropiedadPrefRepository->getNewId();
                }
                $oProp->setId_item($id_item);
                $oProp->setId_item_usuario_objeto($id_item_usuario_objeto);
                $propiedad = isset($aCambio['spropiedad']) && is_string($aCambio['spropiedad'])
                    ? $aCambio['spropiedad']
                    : $nom_prop;
                $oProp->setPropiedad($propiedad);
                $operador = isset($aCambio['soperador']) && is_string($aCambio['soperador'])
                    ? $aCambio['soperador']
                    : '';
                if ($operador !== '') {
                    $oProp->setOperador($operador);
                }
                $valor = isset($aCambio['svalor']) && is_string($aCambio['svalor'])
                    ? $aCambio['svalor']
                    : '';
                if ($valor !== '') {
                    $oProp->setValor($valor);
                }
                $oProp->setValor_old(\src\shared\domain\helpers\FuncTablasSupport::isTrue($aCambio['bvalor_old'] ?? ''));
                $oProp->setValor_new(\src\shared\domain\helpers\FuncTablasSupport::isTrue($aCambio['bvalor_new'] ?? ''));
            } else {
                $nom_item = str_replace('_cond', '_item', $id_cond);
                $existing_id = isset($input[$nom_item]) && is_numeric($input[$nom_item])
                    ? (int) $input[$nom_item]
                    : 0;
                if ($existing_id > 0) {
                    $oProp = $this->cambioUsuarioPropiedadPrefRepository->findById($existing_id);
                    if ($oProp === null) {
                        $oProp = new CambioUsuarioPropiedadPref();
                        $oProp->setId_item($existing_id);
                    }
                } else {
                    $oProp = new CambioUsuarioPropiedadPref();
                    $oProp->setId_item($this->cambioUsuarioPropiedadPrefRepository->getNewId());
                }
                $oProp->setId_item_usuario_objeto($id_item_usuario_objeto);
                $oProp->setPropiedad($nom_prop);
            }

            if ($this->cambioUsuarioPropiedadPrefRepository->Guardar($oProp) === false) {
                return ['error' => (string)_("Hay un error, no se ha guardado")];
            }
        }

        $cLista = $this->cambioUsuarioPropiedadPrefRepository->getCambioUsuarioPropiedadPrefs(
            ['id_item_usuario_objeto' => $id_item_usuario_objeto]
        );
        $a_item_tot = [];
        $a_propiedades_tot = [];
        $c = 0;
        foreach ($cLista as $oProp) {
            $c++;
            $a_item_tot[$c] = $oProp->getId_item();
            $a_propiedades_tot[$c] = $oProp->getPropiedad();
        }
        $a_propiedades_borrar = array_diff($a_propiedades_tot, $a_propiedades_sel);
        foreach ($a_propiedades_borrar as $propiedad_borrar) {
            $key = array_search($propiedad_borrar, $a_propiedades_tot, true);
            if ($key === false) {
                continue;
            }
            $oPropBorrar = $this->cambioUsuarioPropiedadPrefRepository->findById($a_item_tot[$key]);
            if ($oPropBorrar !== null && $this->cambioUsuarioPropiedadPrefRepository->Eliminar($oPropBorrar) === false) {
                return ['error' => (string)_("Hay un error, no se ha eliminado")];
            }
        }

        return ['error' => ''];
    }
}
