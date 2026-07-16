<?php

declare(strict_types=1);

/**
 * Añade contexto (length, value) a excepciones de longitud máxima en value objects.
 *
 * Uso: php tools/fix/fix_vo_max_length_messages.php [--dry-run]
 */

$dryRun = in_array('--dry-run', $argv, true);
$root = dirname(__DIR__, 2);
$useStatement = 'use src\shared\domain\value_objects\ValueObjectMessages;';

$patterns = [
    // sprintf con longitud explícita (PersonaNombreText, EscritoNombramiento, HabitacionObservText)
    '/throw new \\\\InvalidArgumentException\(sprintf\(\s*\'([^\']+)\'[^;]+;/s' => null,
    '/throw new InvalidArgumentException\(sprintf\(\s*\'([^\']+)\'[^;]+;/s' => null,
    // throw simple con mensaje de longitud máxima
    '/throw new \\\\InvalidArgumentException\(\'([^\']*(?:must be at most|debe tener como máximo|no pueden superar)[^\']*)\'\);/' => 1,
    '/throw new InvalidArgumentException\(\'([^\']*(?:must be at most|debe tener como máximo|no pueden superar)[^\']*)\'\);/' => 2,
];

$messageNeedle = '(?:must be at most|debe tener como máximo|no pueden superar)';

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS)
);

$changed = 0;

foreach ($files as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    if (!str_contains($path, '/domain/value_objects/')) {
        continue;
    }
    if (str_ends_with($path, 'ValueObjectMessages.php')) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }

    $original = $content;

    // Ya migrado
    if (str_contains($content, 'ValueObjectMessages::withValueContext')) {
        continue;
    }

    if (!preg_match('/' . $messageNeedle . '/', $content)) {
        continue;
    }

    // sprintf multilínea (PersonaNombreText, EscritoNombramiento)
    $content = preg_replace_callback(
        '/throw new (\\\\)?InvalidArgumentException\(sprintf\(\s*\'([^\']+)\',\s*\$len,\s*(?:self::reprForException|PersonaTextoChars::safeRepr)\(\$value\)\s*\)\);/s',
        static function (array $m): string {
            $backslash = $m[1] ?? '';
            $message = $m[2];

            return 'throw new ' . $backslash . 'InvalidArgumentException(ValueObjectMessages::withValueContext('
                . "\n                '" . $message . "',\n                \$value\n            ));";
        },
        $content
    ) ?? $content;

    // HabitacionObservText y similares: sprintf con mb_strlen en mensaje
    $content = preg_replace_callback(
        '/throw new (\\\\)?InvalidArgumentException\(sprintf\(\'([^\']*(?:no pueden superar)[^\']*)\', mb_strlen\(\$value\)\)\);/',
        static function (array $m): string {
            $backslash = $m[1] ?? '';
            $message = $m[2];

            return 'throw new ' . $backslash . 'InvalidArgumentException(ValueObjectMessages::withValueContext('
                . "'" . $message . "', \$value));";
        },
        $content
    ) ?? $content;

    // throw simple
    $content = preg_replace_callback(
        '/throw new (\\\\)?InvalidArgumentException\(\'([^\']*(?:must be at most|debe tener como máximo|no pueden superar)[^\']*)\'\);/',
        static function (array $m): string {
            $backslash = $m[1] ?? '';
            $message = $m[2];

            return 'throw new ' . $backslash . 'InvalidArgumentException(ValueObjectMessages::withValueContext('
                . "'" . $message . "', \$value));";
        },
        $content
    ) ?? $content;

    // Quitar reprForException privado si quedó huérfano
    $content = preg_replace(
        '/\n    private static function reprForException\(string \$value\): string\n    \{[^}]+\}\n/s',
        "\n",
        $content
    ) ?? $content;

    if ($content === $original) {
        continue;
    }

    if (!str_contains($content, $useStatement)) {
        if (preg_match('/^(<\?php\s*(?:\r?\n)+declare\(strict_types=1\);\s*(?:\r?\n)+namespace [^;]+;\s*(?:\r?\n)+)/', $content, $m)) {
            $content = $m[1] . $useStatement . "\n\n" . substr($content, strlen($m[1]));
        } elseif (preg_match('/^(<\?php\s*(?:\r?\n)+namespace [^;]+;\s*(?:\r?\n)+)/', $content, $m)) {
            $content = $m[1] . $useStatement . "\n\n" . substr($content, strlen($m[1]));
        }
    }

    if ($content !== $original) {
        $relative = substr($path, strlen($root) + 1);
        if ($dryRun) {
            echo "Would update: $relative\n";
        } else {
            file_put_contents($path, $content);
            echo "Updated: $relative\n";
        }
        $changed++;
    }
}

echo $dryRun ? "Would change $changed files.\n" : "Changed $changed files.\n";
