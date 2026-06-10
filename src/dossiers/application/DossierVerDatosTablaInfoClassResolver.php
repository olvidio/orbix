<?php

namespace src\dossiers\application;

use src\dossiers\domain\entity\TipoDossier;
use src\profesores\domain\InfoProfesorAmpliacion;
use src\profesores\domain\InfoProfesorCongreso;
use src\profesores\domain\InfoProfesorDirector;
use src\profesores\domain\InfoProfesorDocenciaStgr;
use src\profesores\domain\InfoProfesorJuramento;
use src\profesores\domain\InfoProfesorLatin;
use src\profesores\domain\InfoProfesorPublicacion;
use src\profesores\domain\InfoProfesorStgr;
use src\profesores\domain\InfoProfesorTituloEst;

/**
 * FQCN de {@see \src\shared\domain\DatosInfoRepo} para segmentos `datos_tabla` en dossiers_ver.
 *
 * En `d_tipos_dossiers` varios tipos de profesor comparten `class = Profesor` pero cada uno
 * tiene su clase `InfoProfesor*` (ver {@see \src\profesores\application\FichaProfesorStgr}).
 */
final class DossierVerDatosTablaInfoClassResolver
{
    /** @var array<int, class-string> */
    private const ID_STGR_TO_INFO = [
        1012 => InfoProfesorPublicacion::class,
        1017 => InfoProfesorTituloEst::class,
        1018 => InfoProfesorStgr::class,
        1019 => InfoProfesorAmpliacion::class,
        1020 => InfoProfesorDirector::class,
        1021 => InfoProfesorJuramento::class,
        1022 => InfoProfesorLatin::class,
        1024 => InfoProfesorCongreso::class,
        1025 => InfoProfesorDocenciaStgr::class,
    ];

    public static function resolveFullyQualifiedClassName(TipoDossier $tipo): string
    {
        $fqcn = self::tryResolveFullyQualifiedClassName($tipo);
        if ($fqcn === null) {
            throw new \InvalidArgumentException(
                sprintf('Tipo dossier id=%d sin app/class para DatosInfoRepo', $tipo->getId_tipo_dossier())
            );
        }

        return $fqcn;
    }

    /**
     * @return class-string|null
     */
    public static function tryResolveFullyQualifiedClassName(TipoDossier $tipo): ?string
    {
        $id = $tipo->getId_tipo_dossier();
        if (isset(self::ID_STGR_TO_INFO[$id])) {
            return self::ID_STGR_TO_INFO[$id];
        }

        $app = $tipo->getApp();
        $clase = $tipo->getClass();
        if ($app === null || $app === '' || $clase === null || $clase === '') {
            return null;
        }

        return 'src\\' . $app . '\\domain\\Info' . $clase;
    }
}
