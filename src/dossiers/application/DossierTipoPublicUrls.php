<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use src\shared\infrastructure\DependencyResolver;

/**
 * Rutas publicas (frontend/ o apps/) para form/update segun id_tipo_dossier.
 */
final class DossierTipoPublicUrls
{
    public function __construct(
        private TipoDossierRepositoryInterface $tipoDossierRepository,
        private DossierTipoFileSuffixResolver $suffixResolver,
    ) {
    }

    public static function relativeFormController(int $idTipoDossier): string
    {
        return self::resolve()->relativeFormControllerInstance($idTipoDossier);
    }

    public static function relativeUpdate(int $idTipoDossier): string
    {
        return self::resolve()->relativeUpdateInstance($idTipoDossier);
    }

    /**
     * @param array<string, mixed> $aQuery
     * @return array{path: string, query: array<string, mixed>}
     */
    public static function formControllerLinkSpec(int $idTipoDossier, array $aQuery): array
    {
        return self::resolve()->formControllerLinkSpecInstance($idTipoDossier, $aQuery);
    }

    public function relativeFormControllerInstance(int $idTipoDossier): string
    {
        return $this->resolveRelativePath($idTipoDossier, 'form');
    }

    public function relativeUpdateInstance(int $idTipoDossier): string
    {
        return $this->resolveRelativePath($idTipoDossier, 'update');
    }

    /**
     * @param array<string, mixed> $aQuery
     * @return array{path: string, query: array<string, mixed>}
     */
    public function formControllerLinkSpecInstance(int $idTipoDossier, array $aQuery): array
    {
        $path = $this->relativeFormControllerInstance($idTipoDossier);
        array_walk($aQuery, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);

        return [
            'path' => $path,
            'query' => $aQuery,
        ];
    }

    private function resolveRelativePath(int $idTipoDossier, string $prefijo): string
    {
        $tipo = $this->requireTipo($idTipoDossier);
        $app = $tipo->getApp() ?? '';
        $codigo = trim((string) ($tipo->getCodigoVo()?->value() ?? ''));
        if ($codigo !== '') {
            $projectRoot = dirname(__DIR__, 3);
            $frontPath = $projectRoot . '/frontend/' . $app . '/controller/' . $prefijo . '_' . $codigo . '.php';
            if (is_file($frontPath)) {
                return 'frontend/' . $app . '/controller/' . $prefijo . '_' . $codigo . '.php';
            }
        }

        $kind = $prefijo === 'form'
            ? DossierTipoFileSuffixResolver::KIND_FORM_CONTROLLER
            : DossierTipoFileSuffixResolver::KIND_UPDATE;
        $suffix = $this->suffixResolver->resolveSuffix($tipo, $kind);

        return 'apps/' . $app . '/controller/' . $prefijo . '_' . $suffix . '.php';
    }

    private function requireTipo(int $idTipoDossier): TipoDossier
    {
        $tipo = $this->tipoDossierRepository->findById($idTipoDossier);
        if ($tipo === null) {
            throw new \RuntimeException('d_tipos_dossiers: id no encontrado ' . $idTipoDossier);
        }
        return $tipo;
    }

    private static function resolve(): self
    {
        return DependencyResolver::get(self::class);
    }
}
