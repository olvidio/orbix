<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Actualiza el array `cedidas` de `ActividadPlazasDl` para ceder
 * (o quitar) plazas de `mi_dele` a otra dl en una actividad.
 *
 * Sucesor de la rama `ceder` del dispatcher legacy
 * `apps/actividadplazas/controller/resumen_plazas_update.php`.
 */
final class PlazasCeder
{
    public static function execute(array $input): string
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $num_plazas = (int)($input['num_plazas'] ?? 0);
        $reg_dl = (string)($input['region_dl'] ?? '');

        if ($id_activ <= 0 || $reg_dl === '') {
            return (string)_("faltan parametros id_activ / region_dl");
        }

        $dl = substr($reg_dl, strpos($reg_dl, '-') + 1);

        $mi_dele = ConfigGlobal::mi_delef();
        $dl_sigla = ConfigGlobal::mi_sfsv() === 2 ? substr($mi_dele, 0, -1) : $mi_dele;

        $id_dl = 0;
        $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $dl_sigla]);
        if (is_array($cDelegaciones) && count($cDelegaciones) > 0) {
            $id_dl = (int)($cDelegaciones[0]->getIdDlVo()->value() ?? 0);
        }

        $oActividadPlazasDl = PlazasDlEdicion::obtenerOCrearDesdeCalendario($id_activ, $id_dl, $mi_dele);
        if ($oActividadPlazasDl === null) {
            return PlazasCalendarioMensaje::faltaRegistro();
        }

        $aCedidas = $oActividadPlazasDl->getArrayCedidas() ?? [];
        if (!is_array($aCedidas)) {
            $aCedidas = [];
        }

        if ($num_plazas > 0) {
            $msg = self::validarPlazasParaCeder($id_activ, $mi_dele, $aCedidas, $dl, $num_plazas);
            if ($msg !== '') {
                return $msg;
            }
        }

        if ($num_plazas === 0) {
            if (isset($aCedidas[$dl])) {
                unset($aCedidas[$dl]);
            }
        } else {
            $aCedidas[$dl] = $num_plazas;
        }
        $oActividadPlazasDl->setCedidas($aCedidas);

        $ActividadPlazasDlRepository = $GLOBALS['container']->get(ActividadPlazasDlRepositoryInterface::class);
        if ($ActividadPlazasDlRepository->Guardar($oActividadPlazasDl) === false) {
            return (string)_("hay un error, no se ha guardado")
                . "\n" . $ActividadPlazasDlRepository->getErrorTxt();
        }
        return '';
    }

    /**
     * Comprueba que mi_dele dispone de plazas de calendario para ceder.
     *
     * @param array<string, int> $aCedidas
     */
    private static function validarPlazasParaCeder(
        int $id_activ,
        string $mi_dele,
        array $aCedidas,
        string $dl_destino,
        int $num_plazas
    ): string {
        /** @var ResumenPlazasService $gesActividadPlazas */
        $gesActividadPlazas = $GLOBALS['container']->get(ResumenPlazasService::class);
        $gesActividadPlazas->setId_activ($id_activ);

        $calendario = (int)$gesActividadPlazas->getPlazasCalendario($mi_dele);
        $cedidas_totales = array_sum($aCedidas);
        $cedidas_a_destino = (int)($aCedidas[$dl_destino] ?? 0);
        $max_cedible = $calendario - $cedidas_totales + $cedidas_a_destino;

        if ($max_cedible <= 0) {
            return (string)_("No tiene plazas para ceder");
        }
        if ($num_plazas > $max_cedible) {
            return sprintf(
                (string)_("No tiene plazas suficientes para ceder. Puede ceder como máximo %s"),
                $max_cedible
            );
        }

        return '';
    }
}
