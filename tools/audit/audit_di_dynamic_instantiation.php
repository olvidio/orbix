#!/usr/bin/env php
<?php

/**
 * Audita roturas típicas post-migración DI:
 * - `new $obj()` en controladores tablaDB / dossiers
 * - clases Info* (DatosInfoRepo) con constructor obligatorio sin registro obvio en dependencies.php
 *
 * Uso: php tools/audit/audit_di_dynamic_instantiation.php
 */

declare(strict_types=1);

require dirname(__DIR__, 2) . '/libs/vendor/autoload.php';

$root = dirname(__DIR__, 2);

/** @var list<string> */
$dynamicNewSites = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS)
);
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }
    if (preg_match('/new\s+\$\w+\s*\(/', $content)
        && !str_contains($path, 'DatosInfoRepoResolver.php')
        && !str_contains($path, 'DatosInfoRepo.php')
        && !str_contains($path, 'ModuleDbClassInvoker.php')
    ) {
        $dynamicNewSites[] = str_replace($root . '/', '', $path);
    }
}

/** @var list<class-string> */
$infoClasses = [];
$infoIterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS)
);
foreach ($infoIterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    $content = file_get_contents($path);
    if ($content === false || !preg_match('/extends\s+DatosInfoRepo/', $content)) {
        continue;
    }
    $relative = str_replace($root . '/', '', $path);
    $class = preg_replace_callback(
        '#^src/([^/]+)/domain/(.+)\.php$#',
        static fn (array $m): string => 'src\\' . $m[1] . '\\domain\\' . $m[2],
        $relative
    );
    if (!is_string($class) || $class === $relative || !class_exists($class)) {
        continue;
    }
    $infoClasses[] = $class;
}

$dependenciesContent = '';
foreach (glob($root . '/src/*/config/dependencies.php') ?: [] as $depFile) {
    $dependenciesContent .= (string) file_get_contents($depFile);
}

echo "=== Dynamic `new \$var()` call sites ===\n";
foreach ($dynamicNewSites as $site) {
    echo "  - $site\n";
}

echo "\n=== Info* (DatosInfoRepo) with required constructor params ===\n";
foreach ($infoClasses as $class) {
    $ref = new ReflectionClass($class);
    $ctor = $ref->getConstructor();
    if ($ctor === null) {
        continue;
    }
    $required = 0;
    foreach ($ctor->getParameters() as $param) {
        if (!$param->isOptional()) {
            $required++;
        }
    }
    if ($required === 0) {
        continue;
    }
    $shortName = $ref->getShortName();
    $registered = str_contains($dependenciesContent, $shortName . '::class');
    $flag = $registered ? 'DI ok' : 'MISSING dependencies.php?';
    echo sprintf("  - %s (%d required) [%s]\n", $class, $required, $flag);
}

echo "\nTip: tras migrar un módulo, ejecutar también:\n";
echo "  composer phpstan:file -- src/<modulo>/\n";
echo "  rg \"new \\\\$\" src/\n";
echo "  probar endpoints /src/shared/tablaDB_* con clase_info de cada Info*\n";
