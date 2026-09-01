<?php

declare(strict_types=1);

namespace src\actividades\domain\value_objects;

/**
 * Identificador de tipo de actividad con comodines (puntos) para permisos.
 *
 * Forma canónica: exactamente 6 caracteres, dígitos o '.' (p. ej. 1....., 17...., 12....).
 * No se puede guardar como int: (int)'17....' = 17 y se pierde el patrón.
 *
 * Acepta formas truncadas o corrompidas por un cast a int y las normaliza:
 * '17' y '000017' → '17....'.
 */
final class ActividadTipoIdTxt
{
    private string $value;

    public function __construct(string $value)
    {
        $str = self::canonicalize($value);
        $this->validate($str);
        $this->value = $str;
    }

    /**
     * Normaliza un id de tipo (permisos) a 6 caracteres con comodines a la derecha.
     *
     * - Recorta espacios.
     * - '000017' (sprintf('%06d', (int)'17....')) → '17....'.
     * - '17' / '1' (valor int persistido en varchar) → '17....' / '1.....'.
     * - Un id de 6 dígitos sin ceros a la izquierda (p. ej. 171001) se deja igual.
     */
    public static function canonicalize(string $value): string
    {
        $str = trim($value);
        if ($str === '') {
            return $str;
        }

        if (preg_match('/^0+\d+$/', $str) === 1 && strlen($str) === 6) {
            $significant = ltrim($str, '0');
            $str = $significant === '' ? '0' : $significant;
        }

        $len = strlen($str);
        if ($len < 6 && preg_match('/^[\d.]+$/', $str) === 1) {
            $str .= str_repeat('.', 6 - $len);
        }

        return $str;
    }

    /**
     * Claves equivalentes a probar en BD o en la matriz de sesión.
     * '17....' también busca '17' y '000017'.
     *
     * @return list<string>
     */
    public static function lookupKeys(string $value): array
    {
        $canon = self::canonicalize($value);
        if ($canon === '') {
            return [];
        }
        $keys = [$canon];
        $stripped = rtrim($canon, '.');
        if ($stripped !== '' && $stripped !== $canon) {
            $keys[] = $stripped;
            if (ctype_digit($stripped)) {
                $keys[] = sprintf('%06d', (int) $stripped);
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * Compone el id de tipo desde los desplegables del formulario (sfsv, asistentes, actividad, nom_tipo).
     */
    public static function fromFormParts(
        string $sfsv,
        string $asistentes,
        string $actividad,
        string $nomTipo,
        bool $extendida = false,
    ): string {
        $sfsv = $sfsv === '' ? '.' : $sfsv;
        $asistentes = $asistentes === '' ? '.' : $asistentes;
        if ($extendida) {
            $actividad = $actividad === '' ? '..' : $actividad;
            $nomTipo = $nomTipo === '' ? '..' : $nomTipo;
        } else {
            $actividad = $actividad === '' ? '.' : $actividad;
            $nomTipo = $nomTipo === '' ? '...' : $nomTipo;
        }

        return self::canonicalize($sfsv . $asistentes . $actividad . $nomTipo);
    }

    /**
     * Condición de repositorio que encuentra el canónico y alias truncados ('17', '000017').
     *
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperador
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    public static function applyToRepositoryWhere(
        array $aWhere,
        array $aOperador,
        string $value,
        string $field = 'id_tipo_activ_txt',
    ): array {
        $keys = self::lookupKeys($value);
        if ($keys === []) {
            $aWhere[$field] = $value;

            return [$aWhere, $aOperador];
        }
        if (count($keys) === 1) {
            $aWhere[$field] = $keys[0];

            return [$aWhere, $aOperador];
        }
        $aWhere[$field] = $keys;
        $aOperador[$field] = 'IN';

        return [$aWhere, $aOperador];
    }

    private function validate(string $str): void
    {
        if (!preg_match('/^[\d.]{6}$/', $str)) {
            throw new \InvalidArgumentException(
                'ActividadTipoIdTxt debe tener exactamente 6 caracteres (dígitos o punto)'
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ActividadTipoIdTxt $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
