<?php

namespace src\personas\application;

use function src\shared\domain\helpers\input_int;

use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\PersonaPub;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Caso de uso detras del endpoint `/src/personas/traslado_form_data`.
 */
final class TrasladoFormData
{
    public function __construct(
        private PersonaFinderService $personaFinderService,
        private CentroDlRepositoryInterface $centroDlRepository,
        private SituacionRepositoryInterface $situacionRepository,
        private DelegacionDropdown $delegacionDropdown,
    ) {
    }

    /**
     * @param array<string,mixed> $input habitualmente `$_POST`
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $a_sel = self::normalizeSel($input['sel'] ?? null);
        if (!empty($a_sel)) {
            $id_pau = (int)strtok((string)$a_sel[0], '#');
        } else {
            $id_pau = input_int($input, 'id_pau');
        }

        $oPersona = $this->personaFinderService->findPersonaEnGlobal($id_pau);
        if (!is_object($oPersona)) {
            return ['error' => sprintf(_("No encuentro a nadie con id_nom: %d"), $id_pau)];
        }
        if (get_class($oPersona) === PersonaPub::class) {
            return ['error' => _("con las personas de paso no tiene sentido.")];
        }

        $opciones_centros = $this->centroDlRepository->getArrayCentros("WHERE tipo_ctr !~ '^[(cgi)|(igl)]'");
        $opciones_dl = $this->delegacionDropdown->listaRegDele(false);
        $opciones_situacion = $this->situacionRepository->getArraySituaciones(traslado: true);

        $id_ctr = $oPersona->getId_ctr() ?? 0;
        $oCentroDl = $id_ctr > 0 ? $this->centroDlRepository->findById($id_ctr) : null;
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
            return array_values(array_filter(array_map(static fn(mixed $v): string => is_scalar($v) ? (string)$v : '', $sel), static fn(string $v): bool => $v !== ''));
        }
        if (is_string($sel) && $sel !== '') {
            return [$sel];
        }
        return [];
    }
}
