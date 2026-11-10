<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\application;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\usuarios\domain\PasswordHasher;

/**
 * Usuario de prueba para los tests de login (`LoginProcesar` y `AppMobileLogin`).
 *
 * Los dos casos de uso abren sus propias conexiones (`new ConfigDB` / `new DBConnection`), así que no
 * admiten mocks: se prueban contra la BD de desarrollo con UN usuario de id alto (4990021) en
 * `"H-dlbv".aux_usuarios`, que se borra siempre en `tearDown`. No se toca ningún otro dato.
 *
 * También guarda y restaura lo que el login modifica fuera de la BD: `$_SESSION`, las variables de
 * entorno `ESQUEMA` / `PRIVATE` y `ConfigGlobal::$dmz`.
 */
trait UsuarioDePruebaLogin
{
    protected const ID_USUARIO = 4990021;
    protected const USUARIO = 'tstu_login';
    protected const CLAVE = 'Clave-de-Prueba#2026';
    protected const ESQUEMA = 'H-dlbv';
    /** Rol «SuperAdmin»: sin acceso desde la DMZ. */
    protected const ROL_SIN_DMZ = 1;
    /** Rol «p-sacd»: con acceso desde la DMZ y `pau` = sacd. */
    protected const ROL_CON_DMZ = 20;
    protected const ROL_INEXISTENTE = 999999;
    protected const SECRETO_2FA = 'JBSWY3DPEHPK3PXP';

    private PDO $pdoUsuarios;
    /** @var array<string, mixed> */
    private array $sesionPrevia = [];
    private bool $dmzPrevio = false;
    /** @var array<string, string|false> */
    private array $entornoPrevio = [];

    protected function prepararEscenarioLogin(): void
    {
        $this->pdoUsuarios = GlobalPdo::get('oDBE');
        $this->sesionPrevia = $_SESSION;
        $this->dmzPrevio = ConfigGlobal::$dmz;
        foreach (['ESQUEMA', 'PRIVATE'] as $var) {
            $this->entornoPrevio[$var] = getenv($var);
        }
        $this->borrarUsuarioDePrueba();
    }

    protected function restaurarEscenarioLogin(): void
    {
        $this->borrarUsuarioDePrueba();
        ConfigGlobal::$dmz = $this->dmzPrevio;
        foreach ($this->entornoPrevio as $var => $valor) {
            putenv($valor === false ? $var : $var . '=' . $valor);
        }
        $_SESSION = $this->sesionPrevia;
    }

    private function borrarUsuarioDePrueba(): void
    {
        $this->pdoUsuarios->exec('DELETE FROM web_preferencias WHERE id_usuario = ' . self::ID_USUARIO);
        $this->pdoUsuarios->exec('DELETE FROM aux_usuarios WHERE id_usuario = ' . self::ID_USUARIO);
    }

    /**
     * @param array{id_role?: ?int, password?: ?string, email?: ?string, has_2fa?: bool, secret_2fa?: ?string, cambio_password?: bool, usuario?: string} $datos
     *        `password` es el HASH que se guarda (por defecto, el de self::CLAVE).
     */
    protected function crearUsuario(array $datos = []): void
    {
        $datos += [
            'usuario' => self::USUARIO,
            'id_role' => self::ROL_SIN_DMZ,
            'password' => (new PasswordHasher())->encode(self::CLAVE),
            'email' => 'tstu_login@example.test',
            'has_2fa' => false,
            'secret_2fa' => null,
            'cambio_password' => false,
        ];
        $stmt = $this->pdoUsuarios->prepare(
            'INSERT INTO aux_usuarios (id_usuario, usuario, id_role, password, email, has_2fa, secret_2fa, cambio_password)
             VALUES (:id, :usuario, :id_role, :password, :email, :has_2fa, :secret_2fa, :cambio_password)'
        );
        $stmt->bindValue('id', self::ID_USUARIO, PDO::PARAM_INT);
        $stmt->bindValue('usuario', $datos['usuario']);
        $stmt->bindValue('id_role', $datos['id_role'], $datos['id_role'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue('password', $datos['password'], $datos['password'] === null ? PDO::PARAM_NULL : PDO::PARAM_LOB);
        $stmt->bindValue('email', $datos['email']);
        $stmt->bindValue('has_2fa', $datos['has_2fa'], PDO::PARAM_BOOL);
        $stmt->bindValue('secret_2fa', $datos['secret_2fa']);
        $stmt->bindValue('cambio_password', $datos['cambio_password'], PDO::PARAM_BOOL);
        $stmt->execute();
    }

    protected function guardarPreferencia(string $tipo, string $valor): void
    {
        $stmt = $this->pdoUsuarios->prepare(
            'INSERT INTO web_preferencias (id_usuario, tipo, preferencia) VALUES (:id, :tipo, :valor)'
        );
        $stmt->execute(['id' => self::ID_USUARIO, 'tipo' => $tipo, 'valor' => $valor]);
    }

    /**
     * Código TOTP (RFC 6238, SHA-1, 6 dígitos, paso de 30 s) calculado de forma independiente de
     * `Verify2fa`; `$pasos` desplaza la ventana (‑1 = anterior, 0 = actual, …).
     */
    protected function codigoTotp(string $secreto, int $pasos = 0): string
    {
        $alfabeto = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split(strtoupper($secreto)) as $c) {
            $bits .= str_pad(decbin((int) strpos($alfabeto, $c)), 5, '0', STR_PAD_LEFT);
        }
        $clave = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $clave .= chr((int) bindec($byte));
            }
        }
        $contador = intdiv(time(), 30) + $pasos;
        $hash = hash_hmac('sha1', pack('N2', 0, $contador), $clave, true);
        $offset = ord($hash[19]) & 0x0F;
        $valor = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;

        return str_pad((string) ($valor % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /** Código de 6 dígitos que seguro no es válido en ninguna de las tres ventanas aceptadas. */
    protected function codigoTotpIncorrecto(string $secreto): string
    {
        $validos = [
            $this->codigoTotp($secreto, -1),
            $this->codigoTotp($secreto),
            $this->codigoTotp($secreto, 1),
        ];
        foreach (['000000', '111111', '222222', '333333'] as $candidato) {
            if (!in_array($candidato, $validos, true)) {
                return $candidato;
            }
        }

        return '444444';
    }

    /** Aplana cualquier respuesta a texto para buscar fugas (hash, secreto, SQL…). */
    protected function comoTexto(mixed $valor): string
    {
        return (string) json_encode($valor, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    protected function hashGuardado(): string
    {
        $stmt = $this->pdoUsuarios->query('SELECT password FROM aux_usuarios WHERE id_usuario = ' . self::ID_USUARIO);
        $valor = null;
        $stmt->bindColumn(1, $valor, PDO::PARAM_STR);
        $stmt->fetch(PDO::FETCH_BOUND);

        return (string) $valor;
    }
}
