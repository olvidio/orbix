<?php

namespace src\usuarios\domain;

class Verify2fa
{

    /**
     * Verifica un código de autenticación de doble factor (2FA)
     *
     * @param string $code Código ingresado por el usuario
     * @param string $secret Clave secreta del usuario
     * @return bool True si el código es válido, False en caso contrario
     */
    public static function verify_2fa_code(string $code, string $secret): bool {
        return self::paso_2fa_valido($code, $secret) !== null;
    }

    /**
     * Paso de 30 s al que corresponde un código válido (el actual, el anterior o el siguiente, para
     * compensar la desincronización del reloj), o null si el código no vale. El login guarda el último
     * paso aceptado para que un código no sirva dos veces.
     */
    public static function paso_2fa_valido(string $code, string $secret): ?int {
        // Eliminar espacios y convertir a mayúsculas
        $code = strtoupper(str_replace(' ', '', $code));

        // Obtener el tiempo actual en intervalos de 30 segundos
        $time = intdiv(time(), 30);

        for ($i = -1; $i <= 1; $i++) {
            $timeSlice = $time + $i;
            if (hash_equals(self::calculate_totp($secret, $timeSlice), $code)) {
                return $timeSlice;
            }
        }

        return null;
    }

    /**
     * Calcula un código TOTP basado en una clave secreta y un intervalo de tiempo
     *
     * @param string $secret Clave secreta del usuario
     * @param int $timeSlice Intervalo de tiempo
     * @return string Código TOTP generado
     */
    private static function calculate_totp($secret, $timeSlice) {
        // Convertir el tiempo a bytes (big-endian)
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);

        // Convertir la clave secreta de base32 a binario
        $secretkey = self::base32_decode($secret);

        // Calcular HMAC-SHA1
        $hash = hash_hmac('sha1', $time, $secretkey, true);

        // Extraer 4 bytes basados en el offset
        $offset = ord(substr($hash, -1)) & 0x0F;
        $value = ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF);

        // Generar código de 6 dígitos
        $modulo = pow(10, 6);
        $code = $value % $modulo;
        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decodifica una cadena en base32
     *
     * @param string $secret Cadena en base32
     * @return string Datos binarios decodificados
     */
    private  static function base32_decode($secret) {
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $n = 0;
        $j = 0;
        $binary = '';

        for ($i = 0, $iMax = strlen($secret); $i < $iMax; $i++) {
            $n = $n << 5;
            $n = $n + strpos($base32chars, $secret[$i]);
            $j = $j + 5;

            if ($j >= 8) {
                $j = $j - 8;
                $binary .= chr(($n & (0xFF << $j)) >> $j);
            }
        }

        return $binary;
    }
}