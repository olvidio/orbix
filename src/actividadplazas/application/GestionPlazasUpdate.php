<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use function src\shared\domain\helpers\input_string;

/**
 * Mutacion de celda de la tabla editable de `gestion_plazas` (y
 * tambien de `plazas_balance_dl`).
 *
 * Devuelve string vacio si ha ido bien, o mensaje de error. El
 * controlador HTTP lo envuelve con `src\shared\web\ContestarJson::enviar(...)`
 * en el contrato JSON estandar `{success, mensaje, data}`. El widget
 * `frontend\shared\web\TablaEditable` consume esa respuesta via `dataType: 'json'`.
 *
 * Sucesor de la rama `update` del dispatcher legacy
 * `apps/actividadplazas/controller/gestion_plazas_ajax.php`.
 */
final class GestionPlazasUpdate
{
    public function __construct(
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private DelegacionRepositoryInterface $delegacionRepository,
        private ActividadPlazasDlRepositoryInterface $actividadPlazasDlRepository,
        private PlazasDlEdicion $plazasDlEdicion,
    ) {
    }

    /**
     * Campos POST enviados por el form interno de `TablaEditable`:
     *  - `data`    (JSON) fila editada, con claves `id`, `dlorg`,
     *              `tot`, `<dl>-c`, `<dl>-p`, …
     *  - `colName` (JSON) nombre de la columna modificada (`tot`,
     *              `<dl>-c`, `<dl>-p`, `<dl>-l`).
     *
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $dataRaw = input_string($input, 'data');
        $colNameRaw = input_string($input, 'colName');
        if ($dataRaw === '' || $colNameRaw === '') {
            return '';
        }
        $obj = json_decode($dataRaw);
        $dl = json_decode($colNameRaw);
        if (!is_object($obj) || !is_string($dl)) {
            return '';
        }

        $id_activ = (int)($obj->id ?? 0);
        $dl_org = (string)($obj->dlorg ?? '');
        $plazas = (int)($obj->$dl ?? 0);
        if ($id_activ === 0) {
            return '';
        }

        $mi_dele = ConfigGlobal::mi_delef();

        // Plazas totales de la actividad (editable solo si la actividad es
        // de mi dl).
        if ($dl === 'tot' && $mi_dele === $dl_org) {
            $oActividadDl = $this->actividadDlRepository->findById($id_activ);
            if ($oActividadDl === null) {
                return (string)_("no se encuentra la actividad");
            }
            $oActividadDl->setPlazas($plazas);
            if ($this->actividadDlRepository->Guardar($oActividadDl) === false) {
                $err = (string)_("hay un error, no se ha guardado");
                return $err . "\n" . $this->actividadDlRepository->getErrorTxt();
            }
            return '';
        }

        // Resto de columnas: `<dl>-c` o `<dl>-p` (concedidas / pedidas).
        $dl_sigla = substr($dl, 0, -2);
        if (ConfigGlobal::mi_sfsv() === 2) {
            // para sf quitar la `f` final
            $dl_sigla = substr($dl_sigla, 0, -1);
        }
        $id_dl = 0;
        $cDelegaciones = $this->delegacionRepository->getDelegaciones(['dl' => $dl_sigla]);
        if ($cDelegaciones !== []) {
            $id_dl = (int) $cDelegaciones[0]->getIdDlVo()->value();
        }
        // Lectura del cuadro en {@see GestionPlazasData}: da_plazas (calendario común).
        // Lo que edita esta dl se persiste en da_plazas_dl.
        $oActividadPlazasDl = $this->plazasDlEdicion->obtenerOCrearDesdeCalendario($id_activ, $id_dl, $mi_dele);
        if ($oActividadPlazasDl === null) {
            return PlazasCalendarioMensaje::faltaRegistro();
        }
        $oActividadPlazasDl->setPlazas($plazas);
        if ($this->actividadPlazasDlRepository->Guardar($oActividadPlazasDl) === false) {
            $err = (string)_("hay un error, no se ha guardado");
            return $err . "\n" . $this->actividadPlazasDlRepository->getErrorTxt();
        }
        return '';
    }
}
