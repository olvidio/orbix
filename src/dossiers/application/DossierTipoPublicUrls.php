<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use web\Hash;

/**
 * Rutas apps/... para form/update según id_tipo_dossier y resolución codigo|legacy.
 */
final class DossierTipoPublicUrls
{
    public static function relativeFormController(int $idTipoDossier): string
    {
        $tipo = self::requireTipo($idTipoDossier);
        $resolver = DossierTipoFileSuffixResolver::fromDefaultProjectRoot();
        $suffix = $resolver->resolveSuffix($tipo, DossierTipoFileSuffixResolver::KIND_FORM_CONTROLLER);
        return 'apps/' . $tipo->getApp() . '/controller/form_' . $suffix . '.php';
    }

    public static function relativeUpdate(int $idTipoDossier): string
    {
        $tipo = self::requireTipo($idTipoDossier);
        $resolver = DossierTipoFileSuffixResolver::fromDefaultProjectRoot();
        $suffix = $resolver->resolveSuffix($tipo, DossierTipoFileSuffixResolver::KIND_UPDATE);
        return 'apps/' . $tipo->getApp() . '/controller/update_' . $suffix . '.php';
    }

    /**
     * Igual que en Select*_ setLinksInsert (Hash + query).
     */
    public static function hashedFormControllerQuery(int $idTipoDossier, array $aQuery): string
    {
        $base = self::relativeFormController($idTipoDossier);
        if (is_array($aQuery)) {
            array_walk($aQuery, 'core\poner_empty_on_null');
        }
        return Hash::link($base . '?' . http_build_query($aQuery));
    }

    private static function requireTipo(int $idTipoDossier): \src\dossiers\domain\entity\TipoDossier
    {
        $repo = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        $tipo = $repo->findById($idTipoDossier);
        if ($tipo === null) {
            throw new \RuntimeException('d_tipos_dossiers: id no encontrado ' . $idTipoDossier);
        }
        return $tipo;
    }
}
