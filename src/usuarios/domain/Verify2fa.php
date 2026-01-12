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
    public static function verify_2fa_code($code, $secret) {
        // Eliminar espacios y convertir a mayúsculas
        $code = strtoupper(str_replace(' ', '', $code));

        // Obtener el tiempo actual en intervalos de 30 segundos
        $time = floor(time() / 30);

        // Verificar el código actual y los adyacentes (para compensar desincronización)
        for ($i = -1; $i <= 1; $i++) {
            $timeSlice = $time + $i;
            if (self::calculate_totp($secret, $timeSlice) === $code) {
                return true;
            }
        }

        return false;
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
        $value = ((ord(substr($hash, $offset)) & 0x7F) << 24) |
            ((ord(substr($hash, $offset + 1)) & 0xFF) << 16) |
            ((ord(substr($hash, $offset + 2)) & 0xFF) << 8) |
            (ord(substr($hash, $offset + 3)) & 0xFF);

        // Generar código de 6 dígitos
        $modulo = pow(10, 6);
        $code = $value % $modulo;
        return str_pad($code, 6, '0', STR_PAD_LEFT);
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