<?php

namespace frontend\shared\layouts;

/**
 * Contrato de layouts de aplicación (menú, estáticos, envoltura).
 *
 * Vive en `frontend/` porque solo lo implementan piezas de presentación; `src/` no define UI.
 */
interface LayoutInterface
{
    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function generateMenuHtml(array $params): array;

    /**
     * @param array<string, mixed> $params
     */
    public function includeCss(array $params): string;

    /**
     * @param array<string, mixed> $params
     */
    public function includeJs(array $params): string;

    /**
     * @param array<string, mixed> $htmlComponents
     * @param array<string, mixed> $params
     */
    public function renderHtml(array $htmlComponents, array $params): string;
}
