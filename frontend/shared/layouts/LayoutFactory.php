<?php

namespace frontend\shared\layouts;

/**
 * Crea la implementación de layout según preferencia de usuario (`legacy`, `burger`, `pills`, `pills2`, …).
 */
final class LayoutFactory
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function create(string $layoutType): LayoutInterface
    {
        switch ($layoutType) {
            case 'legacy':
                return new LegacyLayout();
            case 'burger':
                return new BurgerLayout();
            case 'pills':
                return new PillsLayout();
            case 'pills2':
                return new Pills2Layout();
            default:
                throw new \InvalidArgumentException("Unsupported layout type: $layoutType");
        }
    }
}
