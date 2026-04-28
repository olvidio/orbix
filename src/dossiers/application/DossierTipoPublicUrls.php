<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;

/**
 * Rutas publicas (frontend/ o apps/) para form/update segun id_tipo_dossier.
 *
 * Prefiere `frontend/<app>/controller/form_<codigo>.php` si existe (modulo ya migrado
 * siguiendo `refactor.md`); cae a `apps/<app>/controller/form_<suffix>.php` cuando
 * el modulo todavia vive en legacy.
 */
final class DossierTipoPublicUrls
{
    public static function relativeFormController(int $idTipoDossier): string
    {
        return self::resolveRelativePath($idTipoDossier, 'form');
    }

    public static function relativeUpdate(int $idTipoDossier): string
    {
        return self::resolveRelativePath($idTipoDossier, 'update');
    }

    /**
     * Resuelve la ruta relativa al prefijo `form_` / `update_`.
     *
     * Orden de preferencia:
     * 1. `frontend/<app>/controller/<prefijo>_<codigo>.php` (si el codigo esta definido
     *    en `d_tipos_dossiers` y el fichero existe).
     * 2. `apps/<app>/controller/<prefijo>_<suffix>.php` donde `suffix` es el
     *    codigo si el fichero existe en `apps/...`, si no el `id_tipo_dossier`.
     */
    private static function resolveRelativePath(int $idTipoDossier, string $prefijo): string
    {
        $tipo = self::requireTipo($idTipoDossier);
        $app = $tipo->getApp();
        $codigo = trim((string) ($tipo->getCodigoVo()?->value() ?? ''));
        if ($codigo !== '') {
            $projectRoot = dirname(__DIR__, 3);
            $frontPath = $projectRoot . '/frontend/' . $app . '/controller/' . $prefijo . '_' . $codigo . '.php';
            if (is_file($frontPath)) {
                return 'frontend/' . $app . '/controller/' . $prefijo . '_' . $codigo . '.php';
            }
        }

        $resolver = DossierTipoFileSuffixResolver::fromDefaultProjectRoot();
        $kind = $prefijo === 'form'
            ? DossierTipoFileSuffixResolver::KIND_FORM_CONTROLLER
            : DossierTipoFileSuffixResolver::KIND_UPDATE;
        $suffix = $resolver->resolveSuffix($tipo, $kind);
        return 'apps/' . $app . '/controller/' . $prefijo . '_' . $suffix . '.php';
    }

    /**
     * Spec de URL (path relativo + query) para formulario; la firma va en el borde
     * (p. ej. `frontend/dossiers/helpers/DossierTipoFormLinkSpecsSigning` en widgets Select_*).
     *
     * @return array{path: string, query: array<string, mixed>}
     */
    public static function formControllerLinkSpec(int $idTipoDossier, array $aQuery): array
    {
        $path = self::relativeFormController($idTipoDossier);
        if (is_array($aQuery)) {
            array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
        }

        return [
            'path' => $path,
            'query' => $aQuery,
        ];
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
