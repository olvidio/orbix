<?php

namespace frontend\shared\model;

use frontend\shared\config\OrbixRuntime;

/**
 * Renderiza plantillas `.phtml` bajo `frontend/.../view/` a partir del namespace del controlador.
 */
class ViewNewPhtml
{
    private string $snamespace;

    public function __construct(string $namespace)
    {
        $this->snamespace = $namespace;
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function renderizar(string $file, array $variables = [], bool $echo = true): string
    {
        extract($variables);

        ob_start();
        $dirApps = OrbixRuntime::webPath();
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $baseDir = is_string($docRoot) ? $docRoot . $dirApps : $dirApps;

        $patterns = ['/controller/', '/model/'];
        $replacements = ['view', 'view'];
        $newDir = preg_replace($patterns, $replacements, $this->snamespace);
        $newDir = is_string($newDir) ? $newDir : $this->snamespace;
        $newDir = str_replace('\\', DIRECTORY_SEPARATOR, $newDir);

        $fileName = $baseDir . DIRECTORY_SEPARATOR . $newDir . DIRECTORY_SEPARATOR . $file;

        require $fileName;

        $out2 = (string) ob_get_clean();

        if ($echo) {
            echo $out2;
        }

        return $out2;
    }
}
