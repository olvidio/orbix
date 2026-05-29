<?php

declare(strict_types=1);

namespace frontend\misas\support;

/**
 * Tabla HTML de solo lectura para la cuadrícula de zona (encargos × días).
 */
final class CuadriculaZonaHtmlTable
{
    /**
     * @param array<int, array<string, mixed>> $rows
     * @param string|array<int, array<string, mixed>>|null $columns JSON o definición SlickGrid (solo cabeceras).
     */
    public static function render(array $rows, string|array|null $columns = null): string
    {
        if ($rows === []) {
            return '';
        }

        [$headers, $dayFields] = self::resolveHeaders($rows, $columns);
        if ($dayFields === []) {
            return '';
        }

        $encargoHeader = $headers[0] ?? _('Encargo');
        $html = '<table class="cuadricula_zona">';
        $html .= '<thead><tr><th>' . self::e($encargoHeader) . '</th>';
        $dayCount = count($dayFields);
        for ($i = 1; $i <= $dayCount; $i++) {
            $html .= '<th>' . self::e($headers[$i] ?? $dayFields[$i - 1]) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            if (self::isHeaderRow($row)) {
                continue;
            }
            $html .= '<tr>';
            $html .= self::td(
                (string)($row['encargo'] ?? ''),
                self::encargoCellClass($row),
                ''
            );
            foreach ($dayFields as $field) {
                $meta = is_array($row['meta'] ?? null) ? $row['meta'][$field] ?? null : null;
                $html .= self::td(
                    (string)($row[$field] ?? ''),
                    is_array($meta) ? (string)($meta['color'] ?? '') : '',
                    is_array($meta) ? (string)($meta['texto'] ?? '') : ''
                );
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array{0: list<string>, 1: list<string>}
     */
    private static function resolveHeaders(array $rows, string|array|null $columns): array
    {
        foreach ($rows as $row) {
            if (!self::isHeaderRow($row)) {
                continue;
            }
            $meta = is_array($row['meta'] ?? null) ? $row['meta'] : [];
            $dayFields = [];
            foreach ($meta as $field => $cellMeta) {
                if (!is_string($field) || !self::isDayField($field)) {
                    continue;
                }
                if (!is_array($cellMeta) || ($cellMeta['tipo'] ?? '') !== 'titulo') {
                    continue;
                }
                $dayFields[] = $field;
            }
            usort($dayFields, 'strcmp');
            $sortedHeaders = [_('Encargo')];
            foreach ($dayFields as $field) {
                $sortedHeaders[] = (string)($row[$field] ?? $field);
            }

            return [$sortedHeaders, $dayFields];
        }

        $fromColumns = self::headersFromColumns($columns);
        if ($fromColumns !== null) {
            return $fromColumns;
        }

        foreach ($rows as $row) {
            $dayFields = self::dayFieldsFromRow($row);
            if ($dayFields !== []) {
                $headers = [_('Encargo')];
                foreach ($dayFields as $field) {
                    $headers[] = $field;
                }

                return [$headers, $dayFields];
            }
        }

        return [[], []];
    }

    /**
     * @return array{0: list<string>, 1: list<string>}|null
     */
    private static function headersFromColumns(string|array|null $columns): ?array
    {
        if ($columns === null || $columns === '' || $columns === '[]') {
            return null;
        }

        $decoded = is_array($columns) ? $columns : json_decode($columns, true);
        if (!is_array($decoded)) {
            return null;
        }

        $headers = [];
        $dayFields = [];
        foreach ($decoded as $col) {
            if (!is_array($col)) {
                continue;
            }
            $field = (string)($col['field'] ?? $col['id'] ?? '');
            $name = (string)($col['name'] ?? $field);
            if ($field === 'encargo') {
                $headers[] = $name;
                continue;
            }
            if (!self::isDayField($field)) {
                continue;
            }
            $dayFields[] = $field;
            $headers[] = $name;
        }

        if ($dayFields === []) {
            return null;
        }

        if ($headers === [] || $headers[0] === '') {
            array_unshift($headers, _('Encargo'));
        } elseif (count($headers) === count($dayFields)) {
            array_unshift($headers, _('Encargo'));
        }

        return [$headers, $dayFields];
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function isHeaderRow(array $row): bool
    {
        if (($row['color_encargo'] ?? '') === 'titulo') {
            return true;
        }
        $meta = $row['meta'] ?? null;
        if (!is_array($meta)) {
            return false;
        }
        foreach ($meta as $cellMeta) {
            if (is_array($cellMeta) && ($cellMeta['tipo'] ?? '') === 'titulo') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function encargoCellClass(array $row): string
    {
        $color = (string)($row['color_encargo'] ?? '');
        if ($color !== '' && $color !== 'titulo') {
            return $color;
        }

        return '';
    }

    /**
     * @param array<string, mixed> $row
     * @return list<string>
     */
    private static function dayFieldsFromRow(array $row): array
    {
        $fields = [];
        foreach ($row as $key => $_) {
            if (is_string($key) && self::isDayField($key)) {
                $fields[] = $key;
            }
        }
        sort($fields);

        return $fields;
    }

    private static function isDayField(string $field): bool
    {
        return (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $field);
    }

    private static function td(string $value, string $class, string $title): string
    {
        $attrs = '';
        if ($class !== '') {
            $attrs .= ' class="' . self::e($class) . '"';
        }
        if ($title !== '') {
            $attrs .= ' title="' . self::e($title) . '"';
        }

        return '<td' . $attrs . '>' . self::e($value) . '</td>';
    }

    private static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
