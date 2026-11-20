<?php

declare(strict_types=1);

namespace frontend\shared\session;

/**
 * Puente del login web con `src/`. `login.php` corre en cada petición antes de que haya sesión
 * (lo incluyen `src/shared/global_object.inc` y `FrontBootstrap`), sin contenedor ni `PostRequest`,
 * así que llama directamente a `LoginProcesar` y a la lista de esquemas. Único punto del frontend
 * que lo hace (como `FrontBootstrap` con el arranque).
 *
 * Las apps (orbix-android…) no pasan por aquí: entran por `/src/usuarios/app_login`
 * (`AppMobileLogin`); de `login.php` solo dependen de la respuesta JSON `auth_required` de las
 * rutas `/src/...` sin sesión (`e2e/specs/authenticated/app-movil-contrato.spec.ts`).
 */
final class LoginBridge
{
    /**
     * @return list<string>
     */
    public static function posiblesEsquemasWeb(): array
    {
        $oDBPropiedades = new \src\shared\infrastructure\persistence\postgresql\DBPropiedades();
        $raw = $oDBPropiedades->array_posibles_esquemas(false, true);
        if ($raw === false) {
            return [];
        }

        /** @var list<string> $out */
        $out = array_values($raw);

        return $out;
    }

    /** HTML del &lt;select&gt; de esquemas (mismo contrato que DBPropiedades::posibles_esquemas). */
    /** Sesión caducada (inactividad o tiempo total): queda vacía, como la de quien no ha entrado. */
    public static function comprobarVigenciaDeLaSesion(): void
    {
        \src\shared\infrastructure\ui\http\SesionVigencia::alArrancar();
    }

    /** Tras entrar: las marcas de vigencia empiezan de nuevo. */
    public static function sesionRecienIniciada(): void
    {
        unset($_SESSION[\src\shared\infrastructure\ui\http\SesionVigencia::CLAVE]);
    }

    public static function desplegableEsquemas(string $esquema): string
    {
        $oDBPropiedades = new \src\shared\infrastructure\persistence\postgresql\DBPropiedades();

        return $oDBPropiedades->posibles_esquemas($esquema);
    }

    /**
     * @param array{username?: string, password?: string, esquema?: string, verification_code?: string} $loginInput
     * @return array{
     *     ok: bool,
     *     error?: int,
     *     redirect_ayuda_2fa?: array{username: string, ubicacion: string, esquema: string},
     *     session_auth?: array<string, mixed>,
     *     session_config?: array<string, mixed>,
     *     esquema?: string,
     *     idioma?: string,
     *     sfsv?: int
     * }
     */
    public static function loginProcesar(array $loginInput, string $esquemaWeb, string $ubicacion): array
    {
        return self::useCase()->execute($loginInput, $esquemaWeb, $ubicacion);
    }

    /**
     * Login de un proceso CLI lanzado desde la web con un ticket de un solo uso (sin contraseña).
     *
     * @return array{ok: bool, error?: int, session_auth?: array<string, mixed>, session_config?: array<string, mixed>, esquema?: string, idioma?: string, sfsv?: int}
     */
    public static function loginConTicketCli(string $ticket, string $ubicacion): array
    {
        return self::useCase()->executeConTicketCli($ticket, $ubicacion);
    }

    private static function useCase(): \src\usuarios\application\LoginProcesar
    {
        return new \src\usuarios\application\LoginProcesar(
            new \src\usuarios\infrastructure\persistence\FicheroLoginCliTicketRepository(),
            new \src\usuarios\infrastructure\persistence\postgresql\PgLoginIntentosRepository()
        );
    }

    public static function invalidatePermisosActividadesCache(): void
    {
        \src\shared\application\HydratePermisosActividades::invalidateSessionCache();
    }

    /**
     * @param string|array<int|string, mixed> $data
     */
    public static function contestarJson(string $error, string|array $data = 'ok'): never
    {
        \src\shared\web\ContestarJson::enviar($error, $data);
        exit;
    }
}
