<?php

declare(strict_types=1);

namespace src\notas\application;

use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\TipoActa;
use src\personas\application\services\PersonaFinderService;

/**
 * Listado solo lectura de alumnos y notas de un acta (pantalla `acta_ver`
 * cuando se abre desde el listado de actas, no embebido en actividad).
 *
 * @return array{
 *     filas: list<array{id_nom: int, nombre: string, nota: string, situacion: string}>,
 *     avisos: list<string>
 * }
 */
final class ActaVerNotasListadoData
{
    public function __construct(
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly PersonaFinderService $personaFinderService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{filas: list<array<string, mixed>>, avisos: list<string>}
     */
    public function execute(array $input): array
    {
        $acta = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta');
        if ($acta === '') {
            return ['filas' => [], 'avisos' => []];
        }

        $notas = $this->personaNotaRepository->getPersonaNotas([
            'acta' => $acta,
            'tipo_acta' => TipoActa::FORMATO_ACTA,
        ]);

        $filasPorNombre = [];
        $avisos = [];

        foreach ($notas as $oNota) {
            $id_nom = (int) ($oNota->getId_nom() ?? 0);
            if ($id_nom < 1) {
                continue;
            }

            $oPersona = $this->personaFinderService->findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                $avisos[] = sprintf(
                    _('existe una nota de la que no se tiene acceso al nombre (id_nom = %s)'),
                    $id_nom
                );
                continue;
            }

            $nombre = (string) $oPersona->getApellidosUpperNombre();
            $situacionId = (int) ($oNota->getId_situacion() ?? 0);
            $situaciones = \src\notas\domain\value_objects\NotaSituacion::getArraySituacionTxt();

            $filasPorNombre[$nombre] = [
                'id_nom' => $id_nom,
                'nombre' => $nombre,
                'nota' => $oNota->getNota_txt(),
                'situacion' => $situaciones[$situacionId] ?? '',
            ];
        }

        uksort($filasPorNombre, [\src\shared\domain\helpers\FuncTablasSupport::class, 'strsinacentocmp']);

        return [
            'filas' => array_values($filasPorNombre),
            'avisos' => $avisos,
        ];
    }
}
