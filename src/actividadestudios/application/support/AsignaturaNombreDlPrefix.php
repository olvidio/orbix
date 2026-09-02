<?php

declare(strict_types=1);

namespace src\actividadestudios\application\support;

use src\ubis\application\services\DelegacionUtils;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Prefijo «(dl) » en el nombre corto cuando la oferta no es de la dl organizadora
 * (o cuando hay que distinguir dos copias de la misma asignatura).
 */
final class AsignaturaNombreDlPrefix
{
    public static function dlDesdeEsquema(string $schema): string
    {
        return DelegacionUtils::getDlFromSchema($schema);
    }

    public static function normalizarDl(string $dl): string
    {
        $dl = trim($dl);
        if ($dl === '') {
            return '';
        }
        if (str_contains($dl, '-')) {
            return DelegacionUtils::getDlFromSchema($dl);
        }
        // `dl_org` en SF llega como mi_delef() (`dlbvf`); la `v` final de `dlbv` es parte de la sigla.
        if (substr($dl, -1) === 'f') {
            return substr($dl, 0, -1);
        }

        return $dl;
    }

    public static function dlDesdeIdSchema(DbSchemaRepositoryInterface $repo, int $idSchema): string
    {
        if ($idSchema <= 0) {
            return '';
        }
        $encontrados = $repo->getDbSchemas(['id' => $idSchema]);
        foreach ($encontrados as $oSchema) {
            if ($oSchema->getId() === $idSchema) {
                return self::dlDesdeEsquema($oSchema->getSchema());
            }
        }
        if (count($encontrados) === 1) {
            return self::dlDesdeEsquema($encontrados[0]->getSchema());
        }

        return '';
    }

    public static function aplicar(string $nombreCorto, string $dlOferta, string $dlReferencia, bool $forzar = false): string
    {
        $oferta = self::normalizarDl($dlOferta);
        if ($oferta === '') {
            return $nombreCorto;
        }
        $ref = self::normalizarDl($dlReferencia);
        if (!$forzar && $oferta === $ref) {
            return $nombreCorto;
        }

        return '(' . $oferta . ') ' . $nombreCorto;
    }

    /**
     * @param array<int, list<array{id_schema: int, dl: string}>> $ofertasPorAsignatura
     */
    public static function dlParaFilaMatricula(
        int $idAsignatura,
        int $idSchemaFila,
        int $idSchemaSesion,
        array $ofertasPorAsignatura,
    ): string {
        $ofertas = $ofertasPorAsignatura[$idAsignatura] ?? [];
        if ($ofertas === []) {
            return '';
        }
        if ($idSchemaFila > 0) {
            foreach ($ofertas as $oferta) {
                if ($oferta['id_schema'] === $idSchemaFila) {
                    return $oferta['dl'];
                }
            }
        }
        if (count($ofertas) === 1) {
            return $ofertas[0]['dl'];
        }
        foreach ($ofertas as $oferta) {
            if ($oferta['id_schema'] === $idSchemaSesion) {
                return $oferta['dl'];
            }
        }

        return $ofertas[0]['dl'];
    }

    public static function idAsignaturaDeOpcion(string $valor): int
    {
        $valor = trim($valor);
        if ($valor === '') {
            return 0;
        }
        $id = explode('#', $valor, 2)[0];

        return is_numeric($id) ? (int) $id : 0;
    }
}
