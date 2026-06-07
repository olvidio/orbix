<?php

namespace src\personas\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\application\support\PersonaRepositoryResolver;

/**
 * Caso de uso detras del endpoint `/src/personas/stgr_cambio_data`.
 *
 * Devuelve los datos que necesita la vista `stgr_cambio.phtml`:
 * - nombre completo de la persona,
 * - nivel_stgr actual (para preseleccionar),
 * - mapa `value => etiqueta` de niveles posibles (para el `<select>`).
 *
 * El frontend construye `web\Desplegable` y `web\Hash`; aqui no hay HTML.
 */
final class StgrCambioData
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
    ) {
    }

    /**
     * @param array<string,mixed> $input habitualmente `$_POST`
     * @return array{
     *     error?: string,
     *     nom?: string,
     *     nivel_stgr?: string,
     *     opciones_nivel_stgr?: array<int,string>
     * }
     */
    public function execute(array $input): array
    {
        $a_sel = self::normalizeSel($input['sel'] ?? null);
        if (!empty($a_sel)) {
            $id_nom = (int)strtok((string)$a_sel[0], '#');
            $id_tabla = (string)strtok('#');
        } else {
            $id_nom = input_int($input, 'id_nom');
            $id_tabla = input_string($input, 'id_tabla');
        }

        if (empty($id_tabla)) {
            return ['error' => _("No existe la clase de la persona")];
        }

        try {
            $repository = $this->personaRepositoryResolver->repositorioPorIdTabla($id_tabla);
        } catch (\InvalidArgumentException) {
            return ['error' => _("No existe la clase de la persona")];
        }

        $oPersona = $repository->findById($id_nom);
        if ($oPersona === null) {
            return ['error' => _("No se encuentra la persona")];
        }

        return [
            'nom' => (string)$oPersona->getNombreApellidos(),
            'nivel_stgr' => (string)($oPersona->getNivel_stgr() ?? ''),
            'id_nom' => $id_nom,
            'id_tabla' => $id_tabla,
            'opciones_nivel_stgr' => NivelStgrId::getArrayNivelStgr(),
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
