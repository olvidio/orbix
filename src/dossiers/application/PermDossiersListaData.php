<?php

namespace src\dossiers\application;

use frontend\shared\config\AppUrlConfig;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use web\Hash;

/**
 * Listado de tipos de dossier para pantalla de permisos.
 *
 * @return array{a_filas: list<array{descripcion: string, pagina: string}>}
 */
class PermDossiersListaData
{
    public static function build(string $tipo): array
    {
        $TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        $cTipoDossiers = $TipoDossierRepository->getTiposDossiers(
            ['tabla_from' => $tipo, '_ordre' => 'id_tipo_dossier']
        );
        $base = AppUrlConfig::getPublicAppBaseUrl();
        $a_filas = [];
        foreach ($cTipoDossiers as $oTipoDossier) {
            $id_tipo_dossier = $oTipoDossier->getId_tipo_dossier();
            $depende_modificar = $oTipoDossier->isDepende_modificar();
            $descripcion = $oTipoDossier->getDescripcion();
            $pagina = Hash::link(
                $base . '/frontend/dossiers/controller/perm_dossier_ver.php?' . http_build_query(
                    [
                        'id_tipo_dossier' => $id_tipo_dossier,
                        'depende_modificar' => $depende_modificar,
                        'tipo' => $tipo,
                    ]
                )
            );
            $a_filas[] = [
                'descripcion' => $descripcion,
                'pagina' => $pagina,
            ];
        }
        return ['a_filas' => $a_filas];
    }
}
