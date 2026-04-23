<?php

namespace src\casas\application;

use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;

/**
 * Data builder: datos del formulario `GrupoCasa` (crear/editar).
 *
 * Sucesor de `apps/casas/controller/grupo_form.php`. Devuelve las
 * opciones para los dos desplegables (casa padre / casa hija) y los
 * valores seleccionados si se está editando un registro existente.
 */
final class GrupoCasaFormData
{
    /**
     * @return array{
     *   es_nuevo: bool,
     *   id_item: string,
     *   id_ubi_padre: int,
     *   id_ubi_hijo: int,
     *   opciones_casas: array<int|string,string>
     * }
     */
    public static function execute(array $input): array
    {
        $id_item_raw = (string)($input['id_item'] ?? '');
        $es_nuevo = ($id_item_raw === '' || $id_item_raw === 'nuevo');

        $id_ubi_padre = 0;
        $id_ubi_hijo = 0;
        if (!$es_nuevo) {
            $repoGrupo = $GLOBALS['container']->get(GrupoCasaRepositoryInterface::class);
            $oGrupoCasa = $repoGrupo->findById((int)$id_item_raw);
            if ($oGrupoCasa !== null) {
                $id_ubi_padre = $oGrupoCasa->getId_ubi_padre();
                $id_ubi_hijo = $oGrupoCasa->getId_ubi_hijo();
            } else {
                $es_nuevo = true;
            }
        }

        $repoCasa = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $opciones_casas = $repoCasa->getArrayCasas("WHERE active = 't'");

        return [
            'es_nuevo' => $es_nuevo,
            'id_item' => $es_nuevo ? 'nuevo' : (string)$id_item_raw,
            'id_ubi_padre' => $id_ubi_padre,
            'id_ubi_hijo' => $id_ubi_hijo,
            'opciones_casas' => is_array($opciones_casas) ? $opciones_casas : [],
        ];
    }
}
