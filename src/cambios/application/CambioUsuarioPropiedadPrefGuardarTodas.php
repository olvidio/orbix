<?php

namespace src\cambios\application;

use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use function src\shared\domain\helpers\is_true;

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
    /**
     * @param array $input POST completo (se accede a `id_item_usuario_objeto_prop`, `objeto_prop`, $objeto[] y $id_cond).
     * @return array{error: string}
     */
    public static function execute(array $input): array
    {
        $id_item_usuario_objeto = (int)($input['id_item_usuario_objeto_prop'] ?? 0);
        $objeto = (string)($input['objeto_prop'] ?? '');

        if ($id_item_usuario_objeto <= 0 || $objeto === '') {
            return ['error' => (string)_("faltan parametros")];
        }

        $Repo = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);

        $a_propiedades_sel = [];

        $a_conds = $input[$objeto] ?? [];
        if (!is_array($a_conds)) {
            $a_conds = [];
        }
        foreach ($a_conds as $id_cond) {
            $id_cond = (string)$id_cond;
            $nom_prop_cond = substr(strstr($id_cond, '_'), 1);
            $nom_prop = strstr($nom_prop_cond, '_cond', true);
            if ($nom_prop === false || $nom_prop === '') {
                continue;
            }
            $a_propiedades_sel[] = $nom_prop;

            $payload_raw = (string)($input[$id_cond] ?? '');
            if ($payload_raw !== '') {
                $aCambio = json_decode($payload_raw, true) ?: [];
                $oProp = new CambioUsuarioPropiedadPref();
                $id_item = (int)($aCambio['iid_item'] ?? 0);
                if ($id_item <= 0) {
                    $id_item = $Repo->getNewId();
                }
                $oProp->setId_item($id_item);
                $oProp->setId_item_usuario_objeto($id_item_usuario_objeto);
                $oProp->setPropiedad((string)($aCambio['spropiedad'] ?? $nom_prop));
                $operador = (string)($aCambio['soperador'] ?? '');
                if ($operador !== '') {
                    $oProp->setOperador($operador);
                }
                $valor = (string)($aCambio['svalor'] ?? '');
                if ($valor !== '') {
                    $oProp->setValor($valor);
                }
                $oProp->setValor_old(is_true($aCambio['bvalor_old'] ?? ''));
                $oProp->setValor_new(is_true($aCambio['bvalor_new'] ?? ''));
            } else {
                $nom_item = str_replace('_cond', '_item', $id_cond);
                $existing_id = (int)($input[$nom_item] ?? 0);
                if ($existing_id > 0) {
                    $oProp = $Repo->findById($existing_id);
                    if ($oProp === null) {
                        $oProp = new CambioUsuarioPropiedadPref();
                        $oProp->setId_item($existing_id);
                    }
                } else {
                    $oProp = new CambioUsuarioPropiedadPref();
                    $oProp->setId_item($Repo->getNewId());
                }
                $oProp->setId_item_usuario_objeto($id_item_usuario_objeto);
                $oProp->setPropiedad($nom_prop);
            }

            if ($Repo->Guardar($oProp) === false) {
                return ['error' => (string)_("Hay un error, no se ha guardado")];
            }
        }

        // Borrado de las propiedades que estaban y ya no estan.
        $cLista = $Repo->getCambioUsuarioPropiedadPrefs(['id_item_usuario_objeto' => $id_item_usuario_objeto]);
        if (is_array($cLista)) {
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
                $key = array_search($propiedad_borrar, $a_propiedades_tot);
                if ($key === false) {
                    continue;
                }
                $oPropBorrar = $Repo->findById($a_item_tot[$key]);
                if ($oPropBorrar !== null && $Repo->Eliminar($oPropBorrar) === false) {
                    return ['error' => (string)_("Hay un error, no se ha eliminado")];
                }
            }
        }

        return ['error' => ''];
    }
}
