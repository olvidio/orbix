<?php

declare(strict_types=1);

namespace src\personas\application;

use src\personas\application\services\PersonaFinderService;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\PersonaPublicacion;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;

/**
 * Datos del formulario para publicar una persona hacia otras DL (caso B).
 *
 * @return array{
 *     error?: string,
 *     nom?: string,
 *     id_nom?: int,
 *     id_tabla?: string,
 *     id_schema?: int,
 *     opciones_dl?: array<string, string>
 * }
 */
final class PersonaPublicarFormData
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private PersonaFinderService $personaFinderService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $a_sel = self::normalizeSel($input['sel'] ?? null);
        if ($a_sel !== []) {
            $id_nom = (int) strtok((string) $a_sel[0], '#');
            $id_tabla = (string) strtok('#');
        } else {
            $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
            $id_tabla = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tabla');
        }

        if ($id_nom <= 0) {
            return ['error' => _('No se encuentra la persona')];
        }

        $nom = '';
        $id_schema = 0;

        if ($id_tabla !== '') {
            try {
                $repository = $this->personaRepositoryResolver->repositorioPorIdTabla($id_tabla);
                $oPersona = $repository->findById($id_nom);
                if ($oPersona !== null) {
                    $nom = (string) $oPersona->getNombreApellidos();
                    $id_schema = (int) $oPersona->getId_schema();
                }
            } catch (\InvalidArgumentException) {
                // fallback global abajo
            }
        }

        if ($nom === '') {
            $oPersona = $this->personaFinderService->findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                return ['error' => _('No se encuentra la persona')];
            }
            $nom = (string) $oPersona->getNombreApellidos();
            $id_schema = (int) $oPersona->getId_schema();
            if ($id_tabla === '') {
                $id_tabla = (string) ($oPersona->getId_tabla() ?? '');
            }
        }

        if ($id_schema < 1) {
            return ['error' => _('No se puede determinar el esquema de la persona')];
        }

        return [
            'nom' => $nom,
            'id_nom' => $id_nom,
            'id_tabla' => $id_tabla,
            'id_schema' => $id_schema,
            'opciones_dl' => $this->opcionesDlConEsquema(),
        ];
    }

    /**
     * DL con esquema en Orbix, excluyendo la propia (código sin sufijo v/f).
     *
     * @return array<string, string>
     */
    private function opcionesDlConEsquema(): array
    {
        $propia = ConfigGlobal::mi_dele();
        $dbProp = new DBPropiedades();
        $aDl = $dbProp->array_posibles_dl_de_esquemas(false, false);
        $opciones = [];
        foreach ($aDl as $dl => $_label) {
            $dlCode = PersonaPublicacion::normalizarDl((string) $dl);
            if ($dlCode === '' || $dlCode === $propia) {
                continue;
            }
            // Valor normalizado: coincide con publicado_para y el filtro por mi_dele.
            $opciones[$dlCode] = $dlCode;
        }
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
    }

    /**
     * @param mixed $sel
     * @return list<string>
     */
    private static function normalizeSel(mixed $sel): array
    {
        if (is_array($sel)) {
            return array_values(array_filter(
                array_map(static fn(mixed $v): string => is_scalar($v) ? (string) $v : '', $sel),
                static fn(string $v): bool => $v !== '',
            ));
        }
        if (is_string($sel) && $sel !== '') {
            return [$sel];
        }

        return [];
    }
}
