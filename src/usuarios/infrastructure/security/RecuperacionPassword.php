<?php

declare(strict_types=1);

namespace src\usuarios\infrastructure\security;

use Closure;
use PDO;
use src\shared\domain\helpers\OfuscarEmail;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\usuarios\domain\PasswordHasher;
use src\usuarios\domain\value_objects\Username;

/**
 * Recuperación de contraseña sin sesión (endpoints anónimos):
 *
 * 1. {@see solicitar()}: con el nombre de usuario envía al correo del usuario un enlace
 *    firmado y con caducidad ({@see EnlaceRecuperacionPassword}). **No cambia nada**: la
 *    contraseña actual sigue valiendo, así que pedirlo en nombre de otro no le deja sin
 *    acceso. Devuelve el correo enmascarado, nunca entero.
 * 2. {@see comprobar()}: al abrir el enlace, si sigue valiendo, devuelve el usuario.
 * 3. {@see restablecer()}: guarda la contraseña nueva que elige el usuario (con la misma
 *    política que el formulario de usuario). Al cambiar el hash, el enlace deja de valer.
 *
 * Trabaja directamente sobre `aux_usuarios` del esquema del usuario porque no hay sesión
 * con la que resolver los repositorios del contenedor.
 */
final class RecuperacionPassword
{
    /**
     * @param Closure(string): ?PDO $pdoParaEsquema conexión al esquema del usuario (null si no es válido)
     * @param Closure(string, string, string): bool $encolarMail (destinatario, asunto, mensaje)
     * @param string $urlBase base pública de la aplicación, terminada en «/»
     */
    public function __construct(
        private readonly Closure $pdoParaEsquema,
        private readonly Closure $encolarMail,
        private readonly string $urlBase,
        private readonly PasswordHasher $hasher = new PasswordHasher(),
    ) {
    }

    /** Conexiones y cola de correo reales. */
    public static function porDefecto(string $urlBase): self
    {
        $pdo = static function (string $esquema): ?PDO {
            $db = match (substr($esquema, -1)) {
                'v' => 'sv-e',
                'f' => 'sf-e',
                default => null,
            };
            if ($db === null) {
                return null;
            }
            $config = (new ConfigDB($db))->getEsquema($esquema);

            return (new DBConnection($config))->getPDO();
        };
        $mail = static fn (string $to, string $asunto, string $mensaje): bool
            => ColaMailSinSesion::encolar($to, $asunto, $mensaje, 'recuperar_password_mail.php');

        return new self(Closure::fromCallable($pdo), Closure::fromCallable($mail), $urlBase);
    }

    /**
     * @return array{success: bool, error_txt: string, email: string}
     */
    public function solicitar(string $username, string $esquema): array
    {
        $pdo = ($this->pdoParaEsquema)($esquema);
        if (!$pdo instanceof PDO) {
            return $this->resultado(_("Esquema no válido"));
        }
        $fila = $this->fila($pdo, 'usuario = :v', $username);
        if ($fila === null) {
            return $this->resultado(_("No se encontró ningún usuario con ese nombre"));
        }
        if ($fila['email'] === '') {
            return $this->resultado(_("No hay email asociado a este usuario"));
        }

        $tk = EnlaceRecuperacionPassword::emitir($fila['id_usuario'], $esquema, $fila['password']);
        $enlace = $this->urlBase . 'frontend/usuarios/controller/restablecer_password.php?' . http_build_query(['tk' => $tk]);
        $asunto = _("Recuperación de contraseña");
        $mensaje = sprintf(_("Hola %s,\n\nHas solicitado recuperar tu contraseña. Para elegir una nueva, abre este enlace (caduca en 30 minutos):\n%s\n\nSi no lo has pedido tú, ignora este correo: tu contraseña no ha cambiado.\n\nSaludos,\nEl equipo de administración"), $fila['usuario'], $enlace);
        $emailOfuscado = OfuscarEmail::ofuscarEmailParcial($fila['email'], 3, 2);
        if (!($this->encolarMail)($fila['email'], $asunto, $mensaje)) {
            return $this->resultado(_("Error al enviar el correo electrónico"), false, $emailOfuscado);
        }

        return $this->resultado('', true, $emailOfuscado);
    }

    /**
     * @return array{success: bool, error_txt: string, username: string}
     */
    public function comprobar(string $tk): array
    {
        $r = $this->usuarioDelEnlace($tk);
        if ($r === null) {
            return ['success' => false, 'error_txt' => self::enlaceNoValido(), 'username' => ''];
        }

        return ['success' => true, 'error_txt' => '', 'username' => $r['fila']['usuario']];
    }

    /**
     * @return array{success: bool, error_txt: string}
     */
    public function restablecer(string $tk, string $password, string $password2): array
    {
        $r = $this->usuarioDelEnlace($tk);
        if ($r === null) {
            return ['success' => false, 'error_txt' => self::enlaceNoValido()];
        }
        if ($password === '' || $password !== $password2) {
            return ['success' => false, 'error_txt' => _("Las dos contraseñas no coinciden")];
        }
        $usuario = $r['fila']['usuario'];
        $valida = $this->hasher->is_valid_password(new Username($usuario), $password);
        if (!$valida['success']) {
            return ['success' => false, 'error_txt' => $valida['mensaje']];
        }

        $st = $r['pdo']->prepare('UPDATE aux_usuarios SET password = :password, cambio_password = FALSE WHERE id_usuario = :id');
        if ($st === false || !$st->execute([':password' => $this->hasher->encode($password), ':id' => $r['fila']['id_usuario']])) {
            return ['success' => false, 'error_txt' => _("Error al actualizar la contraseña")];
        }

        return ['success' => true, 'error_txt' => ''];
    }

    public static function enlaceNoValido(): string
    {
        return _("El enlace ha caducado o es inválido. Por favor, solicita uno nuevo");
    }

    /**
     * @return array{pdo: PDO, fila: array{id_usuario: int, usuario: string, email: string, password: string}}|null
     */
    private function usuarioDelEnlace(string $tk): ?array
    {
        $claims = EnlaceRecuperacionPassword::abrir($tk);
        if ($claims === null) {
            return null;
        }
        $pdo = ($this->pdoParaEsquema)($claims['e']);
        if (!$pdo instanceof PDO) {
            return null;
        }
        $fila = $this->fila($pdo, 'id_usuario = :v', $claims['u']);
        if ($fila === null || !EnlaceRecuperacionPassword::huellaCoincide($claims['p'], $fila['password'])) {
            return null;
        }

        return ['pdo' => $pdo, 'fila' => $fila];
    }

    /**
     * @return array{id_usuario: int, usuario: string, email: string, password: string}|null
     */
    private function fila(PDO $pdo, string $where, int|string $valor): ?array
    {
        $st = $pdo->prepare("SELECT id_usuario, usuario, email, password FROM aux_usuarios WHERE $where");
        if ($st === false || !$st->execute([':v' => $valor])) {
            return null;
        }
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) {
            return null;
        }
        $password = $row['password'] ?? '';
        if (is_resource($password)) { // bytea
            $password = stream_get_contents($password);
        }

        $id = $row['id_usuario'] ?? null;
        $usuario = $row['usuario'] ?? null;
        if (!is_numeric($id) || !is_string($usuario)) {
            return null;
        }

        return [
            'id_usuario' => (int) $id,
            'usuario' => $usuario,
            'email' => is_string($row['email'] ?? null) ? trim($row['email']) : '',
            'password' => is_string($password) ? $password : '',
        ];
    }

    /**
     * @return array{success: bool, error_txt: string, email: string}
     */
    private function resultado(string $error, bool $ok = false, string $email = ''): array
    {
        return ['success' => $ok, 'error_txt' => $error, 'email' => $email];
    }
}
