<?php

namespace src\personas\application;

use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\personas\domain\entity\PersonaPub;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Caso de uso detras del endpoint `/src/personas/traslado_form_data`.
 *
 * Devuelve los datos neutros que usa la vista `traslado_form.phtml`:
 *  - `titulo`     => nombre/apellidos de la persona,
 *  - `id_ctr`     => centro actual (int|string|null),
 *  - `nombre_ctr` => etiqueta del centro actual,
 *  - `dl`         => delegacion actual,
 *  - `hoy`        => fecha de hoy en formato local,
 *  - `opciones_centros`   => mapa `id_ubi => nombre_ubi` para `<select new_ctr>`,
 *  - `opciones_dl`        => mapa region-dl para `<select new_dl>`,
 *  - `opciones_situacion` => mapa situacion => etiqueta para `<select situacion>`.
 *
 * Los componentes `web\Desplegable` y `web\Hash` se montan en el frontend con
 * estas opciones. Aqui no hay HTML.
 */
final class TrasladoFormData
{
    /**
     * @param array<string,mixed> $input habitualmente `$_POST`
     * @return array{
     *     error?: string,
     *     titulo?: string,
     *     id_ctr?: int|string|null,
     *     nombre_ctr?: string,
     *     dl?: string,
     *     hoy?: string,
     *     opciones_centros?: array<int|string,string>,
     *     opciones_dl?: array<string,string>,
     *     opciones_situacion?: array<string,string>
     * }
     */
    public static function build(array $input): array
    {
        $a_sel = self::normalizeSel($input['sel'] ?? null);
        if (!empty($a_sel)) {
            $id_pau = (int)strtok((string)$a_sel[0], '#');
        } else {
            $id_pau = (int)($input['id_pau'] ?? 0);
        }

        $oPersona = Persona::findPersonaEnGlobal($id_pau);
        if (!is_object($oPersona)) {
            return ['error' => sprintf(_("No encuentro a nadie con id_nom: %d"), $id_pau)];
        }
        if (get_class($oPersona) === PersonaPub::class) {
            return ['error' => _("con las personas de paso no tiene sentido.")];
        }

        $gesCentroDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $opciones_centros = $gesCentroDl->getArrayCentros("WHERE tipo_ctr !~ '^[(cgi)|(igl)]'");
        $opciones_dl = DelegacionDropdown::listaRegDele(false);

        $SituacionRepository = $GLOBALS['container']->get(SituacionRepositoryInterface::class);
        $opciones_situacion = $SituacionRepository->getArraySituaciones(traslado: true);

        $id_ctr = $oPersona->getId_ctr();
        $oCentroDl = $gesCentroDl->findById($id_ctr);
        $nombre_ctr = (string)($oCentroDl?->getNombre_ubi() ?? '');
        $dl = (string)($oPersona->getDl() ?? '');
        $hoy = (new DateTimeLocal())->getFromLocal();

        return [
            'titulo' => (string)$oPersona->getNombreApellidos(),
            'id_ctr' => $id_ctr,
            'nombre_ctr' => $nombre_ctr,
            'dl' => $dl,
            'hoy' => (string)$hoy,
            'id_pau' => $id_pau,
            'opciones_centros' => $opciones_centros,
            'opciones_dl' => $opciones_dl,
            'opciones_situacion' => $opciones_situacion,
        ];
    }

    /**
     * @param mixed $sel
     * @return array<int,string>
     */
    private static function normalizeSel(mixed $sel): array
    {
        if (is_array($sel)) {
            return array_values(array_filter(array_map('strval', $sel), static fn(string $v): bool => $v !== ''));
        }
        if (is_string($sel) && $sel !== '') {
            return [$sel];
        }
        return [];
    }
}
