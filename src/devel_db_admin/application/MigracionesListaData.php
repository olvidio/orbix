<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;

final class MigracionesListaData
{
    public function __construct(
        private readonly MigracionAplicadaRepositoryInterface $repository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $scan = (new MigracionesEscanear($this->repository))->escanear();
        $rows = [];

        foreach ($scan['migraciones'] as $migracion) {
            $idValue = $migracion['id'] ?? null;
            $id = is_scalar($idValue) ? (string) $idValue : '';
            $aplicaciones = self::normalizeRows($migracion['aplicaciones'] ?? []);
            $files = self::normalizeRows($migracion['files'] ?? []);
            $rows[] = [
                // Sin prefijo '#': Lista/SlickGrid añaden el separador; si el id ya lleva '#',
                // el value del checkbox queda '#id' y el backend no coincide con migraciones[id].
                'sel' => $id,
                'fichero' => $this->resumenArchivos($files),
                'prefijo' => $this->toScalarString($migracion['prefijo'] ?? null),
                'descripcion' => $this->toScalarString($migracion['descripcion'] ?? null),
                'bds' => $this->resumenDatabases($aplicaciones),
                'tipo' => $this->resumenTipos($aplicaciones),
                'estado' => $this->toScalarString($migracion['estado'] ?? null),
                'fecha' => $this->fechaUltima($aplicaciones),
            ];
        }

        return [
            'a_cabeceras' => [
                ['id' => 'fichero', 'name' => _('fichero'), 'field' => 'fichero', 'width' => 320],
                ['id' => 'prefijo', 'name' => _('prefijo'), 'field' => 'prefijo', 'width' => 120],
                ['id' => 'descripcion', 'name' => _('descripcion'), 'field' => 'descripcion', 'width' => 260],
                ['id' => 'bds', 'name' => _('bases de datos'), 'field' => 'bds', 'width' => 220],
                ['id' => 'tipo', 'name' => _('tipo'), 'field' => 'tipo', 'width' => 100],
                ['id' => 'estado', 'name' => _('estado'), 'field' => 'estado', 'width' => 110],
                ['id' => 'fecha', 'name' => _('fecha'), 'field' => 'fecha', 'width' => 170],
            ],
            'a_valores' => $rows,
            'warnings' => $scan['warnings'],
            'serie' => $scan['serie'],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $files
     */
    private function resumenArchivos(array $files): string
    {
        $nombres = [];
        foreach ($files as $meta) {
            $nombre = $this->toScalarString($meta['file'] ?? null);
            if ($nombre !== '') {
                $nombres[$nombre] = $nombre;
            }
        }
        ksort($nombres, SORT_STRING);

        return implode(', ', array_values($nombres));
    }

    /**
     * @param array<int, array<string, mixed>> $aplicaciones
     */
    private function resumenDatabases(array $aplicaciones): string
    {
        $databases = [];
        foreach ($aplicaciones as $aplicacion) {
            $database = $this->toScalarString($aplicacion['database'] ?? null);
            if ($database !== '') {
                $databases[$database] = $database;
            }
        }

        uasort($databases, static fn (string $a, string $b): int => MigracionesEscanear::ordenDatabase($a) <=> MigracionesEscanear::ordenDatabase($b));

        return implode(', ', array_values($databases));
    }

    /**
     * @param array<int, array<string, mixed>> $aplicaciones
     */
    private function resumenTipos(array $aplicaciones): string
    {
        $tipos = [];
        foreach ($aplicaciones as $aplicacion) {
            $tipo = $this->toScalarString($aplicacion['tipo'] ?? null);
            if ($tipo !== '') {
                $tipos[$tipo] = $tipo;
            }
        }

        return implode(', ', array_values($tipos));
    }

    /**
     * @param array<int, array<string, mixed>> $aplicaciones
     */
    private function fechaUltima(array $aplicaciones): string
    {
        $fechas = [];
        foreach ($aplicaciones as $aplicacion) {
            $aplicada = $aplicacion['aplicada'] ?? null;
            if ($aplicada instanceof MigracionAplicada && $aplicada->getAplicada_en() !== null) {
                $fechas[] = $aplicada->getAplicada_en();
            }
        }
        rsort($fechas, SORT_STRING);

        return $fechas[0] ?? '';
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
     * @return array<int, array<string, mixed>>
     */
    private static function normalizeRows(mixed $rows): array
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
