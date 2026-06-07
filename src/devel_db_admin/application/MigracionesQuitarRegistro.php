<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;

final class MigracionesQuitarRegistro
{
    public function __construct(
        private readonly MigracionAplicadaRepositoryInterface $repository,
        private readonly ?string $migrationsDir = null,
    ) {
    }

    /**
     * @param list<string> $seleccionados
     * @return array{lines: list<string>, error: string|null}
     */
    public function quitar(array $seleccionados): array
    {
        if ($seleccionados === []) {
            return [
                'lines' => [],
                'error' => 'No hay migraciones seleccionadas.',
            ];
        }

        $this->repository->ensureTabla();

        $scan = (new MigracionesEscanear($this->repository, $this->migrationsDir))->escanear();
        $migraciones = $this->migracionesPorId($scan['migraciones']);

        $lines = [];
        $eliminados = 0;
        $sinRegistro = 0;

        foreach ($seleccionados as $id) {
            if (!isset($migraciones[$id])) {
                $lines[] = sprintf('Migracion %s: no encontrada en db/migrations.', $id);
                continue;
            }

            $migracion = $migraciones[$id];
            $lines[] = sprintf(
                'Migracion %s_%s',
                $this->toScalarString($migracion['prefijo'] ?? null),
                $this->toScalarString($migracion['descripcion'] ?? null),
            );
            $eliminadosGrupo = 0;

            foreach ($this->normalizeRows($migracion['aplicaciones'] ?? []) as $aplicacion) {
                $aplicada = $aplicacion['aplicada'] ?? null;
                if (!$aplicada instanceof MigracionAplicada) {
                    continue;
                }

                if ($this->repository->Eliminar($aplicada)) {
                    $eliminados++;
                    $eliminadosGrupo++;
                    $lines[] = sprintf(
                        '  - registro eliminado en %s (%s)',
                        is_scalar($aplicacion['database'] ?? null) ? (string) $aplicacion['database'] : '',
                        is_scalar($aplicacion['file'] ?? null) ? (string) $aplicacion['file'] : '',
                    );
                } else {
                    $lines[] = sprintf(
                        '  - no se pudo eliminar el registro en %s (%s)',
                        is_scalar($aplicacion['database'] ?? null) ? (string) $aplicacion['database'] : '',
                        is_scalar($aplicacion['file'] ?? null) ? (string) $aplicacion['file'] : '',
                    );
                }
            }

            if ($eliminadosGrupo === 0) {
                $sinRegistro++;
                $lines[] = '  - sin registro en migracion_aplicada.';
            }
        }

        if ($eliminados === 0) {
            return [
                'lines' => $lines,
                'error' => $sinRegistro > 0
                    ? 'Ninguna migracion seleccionada tenia registro en migracion_aplicada.'
                    : 'No se elimino ningun registro.',
            ];
        }

        $lines[] = sprintf('Registros eliminados: %d.', $eliminados);

        return [
            'lines' => $lines,
            'error' => null,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $migraciones
     * @return array<string, array<string, mixed>>
     */
    private function migracionesPorId(array $migraciones): array
    {
        $index = [];
        foreach ($migraciones as $migracion) {
            $idVal = $migracion['id'] ?? '';
            $id = is_scalar($idVal) ? (string) $idVal : '';
            if ($id === '') {
                continue;
            }
            $index[$id] = $migracion;
        }

        return $index;
    }

    /**
     * @param mixed $value
     */
    private function toScalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @param mixed $rows
     * @return list<array<string, mixed>>
     */
    private function normalizeRows(mixed $rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $normalized = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                /** @var array<string, mixed> $row */
                $normalized[] = $row;
            }
        }

        return $normalized;
    }
}
