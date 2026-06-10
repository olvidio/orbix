<?php

namespace src\dossiers\application;

use src\dossiers\domain\entity\TipoDossier;

/**
 * Resuelve el sufijo de nombre de fichero para dossiers: slug codigo si el fichero existe, si no id numérico (legacy).
 *
 * Legacy Select: Select1302.php (sin guión bajo). Con codigo: Select_cargos_de_actividad.php
 */
final class DossierTipoFileSuffixResolver
{
    public const KIND_SELECT = 'select';

    public const KIND_FORM_CONTROLLER = 'form_controller';

    public const KIND_FORM_VIEW = 'form_view';

    public const KIND_UPDATE = 'update';

    public function __construct(
        private readonly string $projectRoot,
    ) {
    }

    public static function fromDefaultProjectRoot(): self
    {
        return new self(dirname(__DIR__, 3));
    }

    public function resolveSuffix(TipoDossier $tipo, string $kind): string
    {
        $id = $tipo->getId_tipo_dossier();
        $codigo = trim((string) ($tipo->getCodigoVo()?->value() ?? ''));
        if ($codigo === '') {
            return (string) $id;
        }
        if ($this->kindFileExists($tipo, $kind, $codigo)) {
            return $codigo;
        }
        return (string) $id;
    }

    /**
     * FQCN para instanciar la clase Select (apps o src), o null si no hay fichero.
     */
    public function resolveSelectClassFqcn(TipoDossier $tipo): ?string
    {
        $suffix = $this->resolveSuffix($tipo, self::KIND_SELECT);
        $app = $tipo->getApp();
        if ($app === null || $app === '') {
            return null;
        }
        $dbVal = $tipo->getDbVo()?->value();
        $db = $dbVal;
        $baseName = $this->selectBaseClassName($suffix);

        if ($db === 5) {
            $file = $this->projectRoot . '/src/' . $app . '/domain/' . $baseName . '.php';
            if (is_file($file)) {
                return 'src\\' . $app . '\\domain\\' . $baseName;
            }
            return null;
        }
        $file1 = $this->projectRoot . '/apps/' . $app . '/model/' . $baseName . '.php';
        if (is_file($file1)) {
            return $app . '\\model\\' . $baseName;
        }
        $file2 = $this->projectRoot . '/apps/' . $app . '/domain/' . $baseName . '.php';
        if (is_file($file2)) {
            return $app . '\\domain\\' . $baseName;
        }
        $file3 = $this->projectRoot . '/src/' . $app . '/application/' . $baseName . '.php';
        if (is_file($file3)) {
            return 'src\\' . $app . '\\application\\' . $baseName;
        }
        $file4 = $this->projectRoot . '/src/' . $app . '/domain/' . $baseName . '.php';
        if (is_file($file4)) {
            return 'src\\' . $app . '\\domain\\' . $baseName;
        }
        return null;
    }

    /**
     * Nombre de clase/fichero sin .php: Select1302 o Select_cargos_de_actividad
     */
    public function selectBaseClassName(string $suffix): string
    {
        if (preg_match('/^\d+$/', $suffix) === 1) {
            return 'Select' . $suffix;
        }
        return 'Select_' . $suffix;
    }

    private function kindFileExists(TipoDossier $tipo, string $kind, string $suffix): bool
    {
        $path = $this->absolutePathForKind($tipo, $kind, $suffix);
        return $path !== null && is_file($path);
    }

    public function absolutePathForKind(TipoDossier $tipo, string $kind, string $suffix): ?string
    {
        $app = $tipo->getApp();
        if ($app === null || $app === '') {
            return null;
        }
        $base = $this->projectRoot . '/apps/' . $app;

        return match ($kind) {
            self::KIND_SELECT => $this->absolutePathSelect($tipo, $suffix),
            self::KIND_FORM_CONTROLLER => $base . '/controller/form_' . $suffix . '.php',
            self::KIND_FORM_VIEW => $base . '/view/form_' . $suffix . '.phtml',
            self::KIND_UPDATE => $base . '/controller/update_' . $suffix . '.php',
            default => null,
        };
    }

    private function absolutePathSelect(TipoDossier $tipo, string $suffix): ?string
    {
        $app = $tipo->getApp();
        $dbVal = $tipo->getDbVo()?->value();
        $db = $dbVal;
        $baseName = $this->selectBaseClassName($suffix);

        if ($db === 5) {
            $p = $this->projectRoot . '/src/' . $app . '/domain/' . $baseName . '.php';
            return is_file($p) ? $p : null;
        }
        $p1 = $this->projectRoot . '/apps/' . $app . '/model/' . $baseName . '.php';
        if (is_file($p1)) {
            return $p1;
        }
        $p2 = $this->projectRoot . '/apps/' . $app . '/domain/' . $baseName . '.php';
        if (is_file($p2)) {
            return $p2;
        }
        $p3 = $this->projectRoot . '/src/' . $app . '/application/' . $baseName . '.php';
        if (is_file($p3)) {
            return $p3;
        }
        $p4 = $this->projectRoot . '/src/' . $app . '/domain/' . $baseName . '.php';
        return is_file($p4) ? $p4 : null;
    }

    /**
     * Indica si el tipo puede mostrarse en dossiers_ver (widget Select_* o DatosInfoRepo).
     */
    public function canRenderFichaSegment(TipoDossier $tipo): bool
    {
        $selectFqcn = $this->resolveSelectClassFqcn($tipo);
        if ($selectFqcn !== null && class_exists($selectFqcn)) {
            return true;
        }

        $infoFqcn = DossierVerDatosTablaInfoClassResolver::tryResolveFullyQualifiedClassName($tipo);

        return $infoFqcn !== null && class_exists($infoFqcn);
    }
}
