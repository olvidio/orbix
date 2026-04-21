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
    /**
     * @return array<string, string>
     */
    public static function getOpciones(int $filtro_ctr, int $id_zona): array
    {
        switch ($filtro_ctr) {
            case EncargoGrupo::CENTRO_SV:
                $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $query = "WHERE tipo_ctr ~ '^a|n|s[^s]|of' AND active='t'";
                return $GesCentros->getArrayCentros($query);
            case EncargoGrupo::CENTRO_SF:
                $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                return $GesCentros->getArrayCentros();
            case EncargoGrupo::CENTRO_SSSC:
                $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $query = "WHERE tipo_ctr ~ '^ss' AND active='t'";
                return $GesCentros->getArrayCentros($query);
            case EncargoGrupo::IGL:
                $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $query = "WHERE tipo_ctr ~ '^igl' AND active='t'";
                return $GesCentros->getArrayCentros($query);
            case EncargoGrupo::CGI:
                return self::mergeSvSfCgi();
            case EncargoGrupo::ZONAS_MISAS:
                if ($id_zona === 0) {
                    return [];
                }
                $GesCentrosDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $query = "WHERE id_zona = $id_zona AND active='t' ";
                $opciones_sv = $GesCentrosDl->getArrayCentros($query);
                $GesCentrosSf = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                $opciones_sf = $GesCentrosSf->getArrayCentros($query);
                return array_merge($opciones_sv, $opciones_sf);
            default:
                return [];
        }
    }

    /**
     * @return array<string, string>
     */
    private static function mergeSvSfCgi(): array
    {
        $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $query = "WHERE tipo_ctr ~ 'cgioc|oc|cgi' AND active='t' ";
        $opciones_sv = $GesCentros->getArrayCentros($query);
        $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $opciones_sf = $GesCentros->getArrayCentros($query);
        return array_merge($opciones_sv, $opciones_sf);
    }
}
