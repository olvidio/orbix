<?php

namespace src\layouts;

/**
 * Interface for layout classes
 * 
 * This interface defines the common methods that all layout classes must implement.
 */
interface LayoutInterface
{
    /**
     * Generate the HTML for the menus
     * 
     * @param array $params Additional parameters needed for rendering
     * @return array Associative array with HTML components
     */
    public function generateMenuHtml(array $params): array;

    /**
     * Include CSS files and inline styles
     * 
     * @param array $params Additional parameters needed for CSS inclusion
     * @return string HTML for CSS inclusion
     */
    public function includeCss(array $params): string;

    /**
     * Include JavaScript files and inline scripts
     * 
     * @param array $params Additional parameters needed for JavaScript inclusion
     * @return string HTML for JavaScript inclusion
     */
    public function includeJs(array $params): string;

    /**
     * Render the final HTML structure
     * 
     * @param array $htmlComponents Associative array with HTML components
     * @param array $params Additional parameters needed for rendering
     * @return string Final HTML structure
     */
    public function renderHtml(array $htmlComponents, array $params): string;
}
