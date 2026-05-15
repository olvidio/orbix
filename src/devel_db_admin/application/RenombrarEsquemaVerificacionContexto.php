<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\config\ServerConf;

/**
 * Parámetros normalizados compartidos por {@see VerificarEstadoRenombrarEsquema},
 * {@see CorregirEstadoRenombrarEsquema} y {@see RenombrarEsquema}.
 *
 * Contrato habitual: nombre base de origen + región y delegación de destino (sin inferir destino desde el origen).
 * Para {@see VerificarEstadoRenombrarEsquema} y {@see CorregirEstadoRenombrarEsquema} existe {@see self::soloDestinoVerificacion} (origen vacío:
 * solo se opera sobre el esquema destino región–dl; en verificación no se contrasta un nombre antiguo; en corrección solo se reaplican defaults, no .inc/db_idschema).
 */
final class RenombrarEsquemaVerificacionContexto
{
    private function __construct(
        public readonly string $esquemaOrigenCampo,
        public readonly string $esquemaOld,
        public readonly string $esquemaOldv,
        public readonly string $esquemaOldf,
        public readonly string $region,
        public readonly string $dl,
        public readonly string $esquemaNew,
        public readonly string $esquemaNewv,
        public readonly string $esquemaNewf,
        public readonly int $comun,
        public readonly int $sv,
        public readonly int $sf,
        public readonly bool $sinRenombreEfectivo,
        public readonly bool $isDocker,
        public readonly bool $soloDestinoComprobacion,
    ) {
    }

    /**
     * Normaliza el valor del formulario (base `ct-01` o referencia con sufijo `ct-01v` / `ct-01f`) al nombre base.
     */
    public static function baseDesdeCampoOrigen(string $campo): string
    {
        $t = trim($campo);
        if ($t === '') {
            return '';
        }
        $last = substr($t, -1);
        if (($last === 'v' || $last === 'f') && strlen($t) > 1) {
            return substr($t, 0, -1);
        }

        return $t;
    }

    /**
     * @return self|array{listo: bool, resumen: string, bloques: list<array{nombre: string, items: list<array{texto: string, estado: string}>}>, meta: array<string, mixed>}
     */
    public static function desdeEntrada(
        string $esquemaOrigenCampo,
        string $region,
        string $dl,
        int $comun,
        int $sv,
        int $sf,
    ): self|array {
        $campo = trim($esquemaOrigenCampo);
        if ($campo === '') {
            return self::errorParametros(
                _('Falta el esquema de origen (nombre base antiguo).'),
                '',
                trim($region),
                trim($dl),
            );
        }

        $esquema_old = self::baseDesdeCampoOrigen($campo);
        if ($esquema_old === '') {
            return self::errorParametros(
                _('El esquema de origen no es válido.'),
                '',
                trim($region),
                trim($dl),
            );
        }

        $regionIn = trim($region);
        $dlIn = trim($dl);
        if ($regionIn === '' || $dlIn === '') {
            return self::errorParametros(
                _('Debe indicar región y delegación de destino (no se deducen del nombre de origen).'),
                $esquema_old,
                $regionIn,
                $dlIn,
            );
        }

        $region = $regionIn;
        $dl = $dlIn;

        $esquema_new = "$region-$dl";
        $esquema_newv = $esquema_new . 'v';
        $esquema_newf = $esquema_new . 'f';

        $esquema_oldv = $esquema_old . 'v';
        $esquema_oldf = $esquema_old . 'f';

        $sinRenombreEfectivo = ($esquema_old === $esquema_new);
        $isDocker = (bool) preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR);

        return new self(
            $campo,
            $esquema_old,
            $esquema_oldv,
            $esquema_oldf,
            $region,
            $dl,
            $esquema_new,
            $esquema_newv,
            $esquema_newf,
            $comun,
            $sv,
            $sf,
            $sinRenombreEfectivo,
            $isDocker,
            false,
        );
    }

    /**
     * Comprobación frente al esquema destino (región-dl) sin nombre de origen: útil cuando el antiguo ya no aparece en los listados.
     *
     * @return self|array{listo: bool, resumen: string, bloques: list<array{nombre: string, items: list<array{texto: string, estado: string}>}>, meta: array<string, mixed>}
     */
    public static function soloDestinoVerificacion(
        string $region,
        string $dl,
        int $comun,
        int $sv,
        int $sf,
    ): self|array {
        $regionIn = trim($region);
        $dlIn = trim($dl);
        if ($regionIn === '' || $dlIn === '') {
            return self::errorParametros(
                _('Para comprobar o corregir solo defaults sin esquema de origen debe indicar región y delegación de destino.'),
                '',
                $regionIn,
                $dlIn,
            );
        }

        $base = "$regionIn-$dlIn";
        $basev = $base . 'v';
        $basef = $base . 'f';
        $isDocker = (bool) preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR);

        return new self(
            '',
            $base,
            $basev,
            $basef,
            $regionIn,
            $dlIn,
            $base,
            $basev,
            $basef,
            $comun,
            $sv,
            $sf,
            true,
            $isDocker,
            true,
        );
    }

    /**
     * @return array{listo: bool, resumen: string, bloques: list<array{nombre: string, items: list<array{texto: string, estado: string}>}>, meta: array<string, mixed>}
     */
    private static function errorParametros(string $resumen, string $origenBase, string $region, string $dl): array
    {
        $isDockerMeta = (bool) preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR);

        return [
            'listo' => false,
            'resumen' => $resumen,
            'bloques' => [
                [
                    'nombre' => _('Parámetros'),
                    'items' => [
                        [
                            'texto' => sprintf(
                                _('Origen (base): «%s». Región: «%s». Delegación: «%s». Indique esquema de origen, región y delegación (salvo comprobar solo destino: región y delegación sin origen).'),
                                $origenBase !== '' ? $origenBase : '—',
                                $region !== '' ? $region : '—',
                                $dl !== '' ? $dl : '—',
                            ),
                            'estado' => 'error',
                        ],
                    ],
                ],
            ],
            'meta' => [
                'esquema_origen' => $origenBase,
                'objetivo_base' => ($region !== '' && $dl !== '') ? "$region-$dl" : '',
                'origen_base' => $origenBase,
                'docker' => $isDockerMeta,
                'region_efectiva' => $region,
                'dl_efectiva' => $dl,
                'sin_renombre_efectivo' => false,
            ],
        ];
    }
}
