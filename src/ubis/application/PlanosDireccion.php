<?php

declare(strict_types=1);

namespace src\ubis\application;

use InvalidArgumentException;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\PlanoRepositoryInterface;

/**
 * Plano de una dirección (centro o casa, propia o de otra dl): saber si hay, leerlo, subirlo y quitarlo.
 * `obj_dir` es el tipo de dirección que usa la ficha (`DireccionCentro`, `DireccionCdcDl`, …).
 */
final class PlanosDireccion
{
    public function __construct(
        private DireccionCentroRepositoryInterface $centro,
        private DireccionCentroDlRepositoryInterface $centroDl,
        private DireccionCentroExRepositoryInterface $centroEx,
        private DireccionCasaRepositoryInterface $casa,
        private DireccionCasaDlRepositoryInterface $casaDl,
        private DireccionCasaExRepositoryInterface $casaEx,
    ) {
    }

    public function existe(string $obj_dir, int $id_direccion): bool
    {
        return $this->leer($obj_dir, $id_direccion) !== null;
    }

    /**
     * @return array{nom: string, extension: string, doc: string}|null null si la dirección no tiene plano
     */
    public function leer(string $obj_dir, int $id_direccion): ?array
    {
        $raw = $this->repo($obj_dir)->planoDownload($id_direccion);
        $doc = $raw['plano_doc'] ?? null;
        if (is_resource($doc)) {
            $doc = stream_get_contents($doc);
        }
        if (!is_string($doc) || $doc === '') {
            return null;
        }

        return [
            'nom' => is_string($raw['plano_nom'] ?? null) ? $raw['plano_nom'] : '',
            'extension' => is_string($raw['plano_extension'] ?? null) ? $raw['plano_extension'] : '',
            'doc' => $doc,
        ];
    }

    public function subir(string $obj_dir, int $id_direccion, string $nom, string $extension, string $fichero): void
    {
        $this->repo($obj_dir)->planoUpload($id_direccion, $nom, $extension, $fichero);
    }

    public function borrar(string $obj_dir, int $id_direccion): void
    {
        $this->repo($obj_dir)->planoBorrar($id_direccion);
    }

    private function repo(string $obj_dir): PlanoRepositoryInterface
    {
        $repo = match ($obj_dir) {
            'DireccionCentro' => $this->centro,
            'DireccionCentroDl' => $this->centroDl,
            'DireccionCentroEx' => $this->centroEx,
            'DireccionCdc' => $this->casa,
            'DireccionCdcDl' => $this->casaDl,
            'DireccionCdcEx' => $this->casaEx,
            default => throw new InvalidArgumentException("obj_dir desconocido: $obj_dir"),
        };
        if (!$repo instanceof PlanoRepositoryInterface) {
            throw new InvalidArgumentException("el repositorio de $obj_dir no guarda planos");
        }

        return $repo;
    }
}
