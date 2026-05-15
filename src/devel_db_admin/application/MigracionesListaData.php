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
            $id = (string) $migracion['id'];
            $aplicaciones = (array) $migracion['aplicaciones'];
            $files = (array) ($migracion['files'] ?? []);
            $rows[] = [
                // Sin prefijo '#': Lista/SlickGrid añaden el separador; si el id ya lleva '#',
                // el value del checkbox queda '#id' y el backend no coincide con migraciones[id].
                'sel' => $id,
                'fichero' => $this->resumenArchivos($files),
                'prefijo' => (string) $migracion['prefijo'],
                'descripcion' => (string) $migracion['descripcion'],
                'bds' => $this->resumenDatabases($aplicaciones),
                'tipo' => $this->resumenTipos($aplicaciones),
                'estado' => (string) $migracion['estado'],
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
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $files
     */
    private function resumenArchivos(array $files): string
    {
        $nombres = [];
        foreach ($files as $meta) {
            $nombre = (string) ($meta['file'] ?? '');
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
            $database = (string) ($aplicacion['database'] ?? '');
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
            $tipo = (string) ($aplicacion['tipo'] ?? '');
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
}
