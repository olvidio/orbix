<?php

namespace src\tablonanuncios\domain\contracts;

use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\value_objects\AnuncioId;

interface AnuncioRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Anuncio>
     */
    public function getAnuncios(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(Anuncio $Anuncio): bool;

    public function Guardar(Anuncio $Anuncio): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(AnuncioId $vo): array|false;

    public function findById(AnuncioId $vo): ?Anuncio;
}
