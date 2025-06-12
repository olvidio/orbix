<?php

namespace src\layouts;

/**
 * Factory class for creating layout instances
 * 
 * This class creates the appropriate layout instance based on the layout type.
 */
class LayoutFactory
{
    /**
     * Create a layout instance based on the layout type
     * 
     * @param string $layoutType The type of layout to create ('legacy' or 'new')
     * @return LayoutInterface The layout instance
     * @throws \InvalidArgumentException If the layout type is not supported
     */
    public static function create(string $layoutType): LayoutInterface
    {
        switch ($layoutType) {
            case 'legacy':
                return new LegacyLayout();
            case 'burger':
                return new BurgerLayout();
            default:
                throw new \InvalidArgumentException("Unsupported layout type: $layoutType");
        }
    }
}