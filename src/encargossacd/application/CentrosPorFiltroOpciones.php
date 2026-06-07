<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Mapa id_ubi => nombre para el desplegable de centros según filtro de encargo (Postgres vía repositorios).
 */
final class CentrosPorFiltroOpciones
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getOpciones(int $filtro_ctr, int $id_zona): array
    {
        switch ($filtro_ctr) {
            case EncargoGrupo::CENTRO_SV:
                $query = "WHERE tipo_ctr ~ '^a|n|s[^s]|of' AND active='t'";
                return self::normalizeStringKeys($this->centroDlRepository->getArrayCentros($query));
            case EncargoGrupo::CENTRO_SF:
                return self::normalizeStringKeys($this->centroDlRepository->getArrayCentros());
            case EncargoGrupo::CENTRO_SSSC:
                $query = "WHERE tipo_ctr ~ '^ss' AND active='t'";
                return self::normalizeStringKeys($this->centroDlRepository->getArrayCentros($query));
            case EncargoGrupo::IGL:
                $query = "WHERE tipo_ctr ~ '^igl' AND active='t'";
                return self::normalizeStringKeys($this->centroDlRepository->getArrayCentros($query));
            case EncargoGrupo::CGI:
                return $this->mergeSvSfCgi();
            case EncargoGrupo::ZONAS_MISAS:
                if ($id_zona === 0) {
                    return [];
                }
                $query = "WHERE id_zona = $id_zona AND active='t' ";
                $opciones_sv = $this->centroDlRepository->getArrayCentros($query);
                $opciones_sf = $this->centroEllasRepository->getArrayCentros($query);
                return self::normalizeStringKeys(array_merge($opciones_sv, $opciones_sf));
            default:
                return [];
        }
    }

    /**
     * @return array<string, string>
     */
    private function mergeSvSfCgi(): array
    {
        $query = "WHERE tipo_ctr ~ 'cgioc|oc|cgi' AND active='t' ";
        $opciones_sv = $this->centroDlRepository->getArrayCentros($query);
        $opciones_sf = $this->centroDlRepository->getArrayCentros($query);
        return self::normalizeStringKeys(array_merge($opciones_sv, $opciones_sf));
    }

    /**
     * @param array<int|string, string> $opciones
     * @return array<string, string>
     */
    private static function normalizeStringKeys(array $opciones): array
    {
        $out = [];
        foreach ($opciones as $k => $v) {
            $out[(string) $k] = (string) $v;
        }

        return $out;
    }
}
