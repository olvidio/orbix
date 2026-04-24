<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\entity\Asistente;

/**
 * Asigna un sacd nuevo a una actividad.
 *
 * Reglas:
 *  - `id_cargo` es el primer hueco libre dentro del listado de cargos
 *    tipo `sacd` (`CargoRepository::getArrayCargos('sacd')`). Si todos
 *    estan ocupados, se calcula `max(id_cargo) + 1`.
 *  - Si la actividad es de sv (`id_tipo_activ` empieza por `1`), se
 *    crea ademas la fila de `Asistencia` correspondiente.
 *
 * Sucesor de la rama `asignar` del dispatcher legacy
 * `apps/actividadessacd/controller/activ_sacd_ajax.php`.
 */
final class SacdAsignar
{
    public static function execute(array $input): string
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $id_nom = (int)($input['id_nom'] ?? 0);
        if ($id_activ <= 0 || $id_nom <= 0) {
            return _("faltan parametros id_activ / id_nom");
        }

        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');
        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);

        $id_cargo = self::resolverIdCargo($ActividadCargoRepository, $aIdCargos_sacd, $id_activ);
        if ($id_cargo === null) {
            return _("No puede haber tantos cargos de sacd en una actividad");
        }

        $oActividadCargo = new ActividadCargo();
        $oActividadCargo->setId_item($ActividadCargoRepository->getNewId());
        $oActividadCargo->setId_activ($id_activ);
        $oActividadCargo->setId_nom($id_nom);
        $oActividadCargo->setId_cargo($id_cargo);
        if ($ActividadCargoRepository->Guardar($oActividadCargo) === false) {
            return _("hay un error, no se ha guardado el cargo");
        }

        // Crear asistencia si es actividad de sv (id_tipo_activ[0] === '1').
        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $oActividad = $ActividadDlRepository->findById($id_activ);
        $id_tipo_activ = $oActividad !== null ? (string)$oActividad->getId_tipo_activ() : '';
        if ($id_tipo_activ !== '' && $id_tipo_activ[0] === '1') {
            $AsistenteDlRepository = $GLOBALS['container']->get(AsistenteDlRepositoryInterface::class);
            $oAsisActiv = new Asistente();
            $oAsisActiv->setId_activ($id_activ);
            $oAsisActiv->setId_nom($id_nom);
            $oAsisActiv->setPropio(false);
            $oAsisActiv->setFalta(false);
            $oAsisActiv->setDl_responsable(ConfigGlobal::mi_delef());
            if ($AsistenteDlRepository->Guardar($oAsisActiv) === false) {
                return _("hay un error, no se ha guardado la asistencia");
            }
        }

        return '';
    }

    /**
     * Determina el `id_cargo` a usar: primer hueco libre, o `max+1`.
     */
    private static function resolverIdCargo(
        ActividadCargoRepositoryInterface $repo,
        array $aIdCargos_sacd,
        int $id_activ
    ): ?int {
        $txt_where = implode(',', array_keys($aIdCargos_sacd));
        $cCargos = $repo->getActividadCargos(
            ['id_activ' => $id_activ, 'id_cargo' => $txt_where, '_ordre' => 'id_cargo DESC'],
            ['id_cargo' => 'IN']
        );
        if (!is_array($cCargos) || count($cCargos) < 1) {
            return key($aIdCargos_sacd);
        }
        $ocupados = [];
        foreach ($cCargos as $oCargo) {
            $ocupados[] = (int)$oCargo->getId_cargo();
        }
        foreach ($aIdCargos_sacd as $id_cargo_x => $_cargo) {
            if (!in_array((int)$id_cargo_x, $ocupados, true)) {
                return (int)$id_cargo_x;
            }
        }
        return null;
    }
}
