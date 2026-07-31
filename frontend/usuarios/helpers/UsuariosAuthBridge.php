<?php

declare(strict_types=1);

namespace frontend\usuarios\helpers;

/**
 * Bridge de login/recovery: concentra FQCN a `src\` para que los controllers
 * no tengan `use src\...`.
 */
final class UsuariosAuthBridge
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
        $useCase = new \src\usuarios\application\LoginProcesar();

        return $useCase->execute($loginInput, $esquemaWeb, $ubicacion);
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

    /**
     * PDO para recovery 2FA según esquema / ubicación.
     *
     * @return array{pdo: \PDO, esquema: string, sfsv: int}
     */
    public static function recoveryPdo(string $esquema, string $ubicacion, string $private): array
    {
        $sfsv = 0;
        $oDB = null;
        $useSfDb = ($ubicacion === 'sf' || $private === 'sf');
        $esquemaActual = $esquema;

        if (substr($esquemaActual, -1) === 'v') {
            $sfsv = 1;
            $oConfigDB = new \src\shared\infrastructure\persistence\ConfigDB('sv-e');
            $config = $oConfigDB->getEsquema($esquemaActual);
            $oDB = (new \src\shared\infrastructure\persistence\DBConnection($config))->getPDO();
        } elseif (substr($esquemaActual, -1) === 'f') {
            if ($useSfDb) {
                try {
                    $sfsv = 2;
                    $oConfigDB = new \src\shared\infrastructure\persistence\ConfigDB('sf-e');
                    $config = $oConfigDB->getEsquema($esquemaActual);
                    $oDB = (new \src\shared\infrastructure\persistence\DBConnection($config))->getPDO();
                } catch (\Throwable $e) {
                    $esquemaActual = substr($esquemaActual, 0, -1);
                    $sfsv = 0;
                }
            } else {
                $esquemaActual = substr($esquemaActual, 0, -1);
            }
        }

        if ($oDB === null) {
            $oConfigDB = new \src\shared\infrastructure\persistence\ConfigDB('comun_select');
            $config = $oConfigDB->getEsquema($esquemaActual);
            $oDB = (new \src\shared\infrastructure\persistence\DBConnection($config))->getPDO();
        }

        return ['pdo' => $oDB, 'esquema' => $esquemaActual, 'sfsv' => $sfsv];
    }
}
