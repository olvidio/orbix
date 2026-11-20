<?php

declare(strict_types=1);

namespace frontend\shared\security;

/**
 * Qué peticiones pueden llegar sin la firma `h` de {@see HashF}.
 *
 * La regla: una petición que trae datos tiene que venir firmada. Sin firma solo se admite lo que no
 * trae datos que el servidor vaya a creer, o lo que ya se protege por otro medio:
 *
 * - sin parámetros (o solo los de la interfaz: `PHPSESSID`, `nav`, selección y scroll);
 * - solo cápsulas `ctx*` de {@see \src\shared\security\HashB}, o una cápsula firmada para esta sesión
 *   junto a los campos de su formulario (la acción la autoriza la cápsula, que ya va firmada);
 * - una descarga con el token `tk` ({@see \src\shared\security\SignedDownloadToken});
 * - la portada (`index.php`: entrar, salir, cambiar de grupo de menú) y el menú `udm4`;
 * - los endpoints que la app móvil llama directamente ({@see RUTAS_CLIENTE_MOVIL}).
 *
 * `ORBIX_HASHF_SIN_FIRMA=aviso` (en `.env` o en el entorno) deja de cortar: solo se apunta, como antes.
 */
final class HashFSinFirma
{
    /**
     * Endpoints `/src` del cliente móvil (`orbix-android`), que no firma con HashF. Es la lista de
     * `docs/catalogo/_endpoints_cliente_movil.md`: se cambian a la vez (lo comprueba `HashFSinFirmaTest`).
     */
    public const RUTAS_CLIENTE_MOVIL = [
        '/src/actividadessacd/comunicacion_activ_sacd_data',
        '/src/encargossacd/sacd_ausencias_get_data',
        '/src/encargossacd/sacd_ausencias_jefe_zona_data',
        '/src/encargossacd/sacd_ausencias_update',
        '/src/encargossacd/sacd_select_data',
        '/src/menus/grupmenu_coleccion',
        '/src/misas/buscar_plan_ctr_data',
        '/src/misas/buscar_plan_sacd_data',
        '/src/misas/cambiar_status_data',
        '/src/misas/crear_nuevo_periodo_data',
        '/src/misas/cuadricula_update',
        '/src/misas/desplegable_encargos',
        '/src/misas/desplegable_sacd',
        '/src/misas/eliminar_encargo_centro',
        '/src/misas/eliminar_encargo_zona',
        '/src/misas/guardar_encargo_centro',
        '/src/misas/guardar_encargo_zona',
        '/src/misas/importar_plantilla_data',
        '/src/misas/modificar_encargos_centros_data',
        '/src/misas/modificar_encargos_data',
        '/src/misas/modificar_iniciales_sacd_zona_data',
        '/src/misas/nuevo_status',
        '/src/misas/plan_de_misas_pantalla_data',
        '/src/misas/update_iniciales',
        '/src/misas/ver_cuadricula_zona_data',
        '/src/misas/ver_encargos_centros_data',
        '/src/misas/ver_encargos_zona_data',
        '/src/misas/ver_iniciales_zona_data',
        '/src/misas/ver_plan_ctr_data',
        '/src/misas/ver_plan_sacd_data',
        '/src/planning/planning_casa_que_data',
        '/src/planning/planning_casa_ver_data',
        '/src/planning/planning_zones_que_data',
        '/src/planning/planning_zones_select_data',
        '/src/ubis/casas_opciones_data',
        '/src/usuarios/app_login',
        '/src/usuarios/app_session',
    ];

    /** Parámetros de la interfaz que no son datos de la pantalla. */
    private const PARAMETROS_NEUTROS = ['PHPSESSID', 'nav', 'id_sel', 'scroll_id', 'atras'];

    /** Parámetros con los que se elige la ruta `/src` (los pone el servidor web o el proxy). */
    private const PARAMETROS_DE_RUTA = ['r', 'dir', '_orbix_src'];

    /** Firmador de URLs para la suite E2E; el propio fichero responde 404 fuera de la instancia de pruebas. */
    public const RUTA_FIRMADOR_E2E = '/frontend/shared/controller/e2e_firmar.php';

    /**
     * ¿Tiene que traer firma esta petición? (Se pregunta cuando ha llegado sin `h`.)
     *
     * @param string $ruta ruta que se va a servir: la `/src/...` que despacha el enrutador o, en una pantalla,
     *                     el path de la petición
     * @param array<array-key, mixed> $post
     * @param array<array-key, mixed> $get
     * @param (callable(string): bool)|null $capsulaDeLaSesion dice si un valor es una cápsula firmada para
     *                                                         esta sesión ({@see \src\shared\security\HashB::esDeEstaSesion()})
     */
    public static function exigida(string $ruta, array $post, array $get, ?callable $capsulaDeLaSesion = null): bool
    {
        $datos = $post !== [] ? $post : $get;
        $nombres = self::nombresConDatos($datos);
        if ($nombres === []) {
            return false;
        }
        if (self::soloCapsulas($nombres)) {
            return false;
        }
        if ($capsulaDeLaSesion !== null && self::traeCapsulaDeLaSesion($datos, $capsulaDeLaSesion)) {
            return false;
        }
        if ($post === [] && $nombres === ['tk']) {
            return false;
        }

        return !self::rutaExenta($ruta);
    }

    /** `false` con `ORBIX_HASHF_SIN_FIRMA=aviso`: la petición sin firma solo se apunta. */
    public static function seCorta(): bool
    {
        return ($_ENV['ORBIX_HASHF_SIN_FIRMA'] ?? getenv('ORBIX_HASHF_SIN_FIRMA')) !== 'aviso';
    }

    /**
     * Ruta canónica de un endpoint `/src`: sin la carpeta de controladores, sin `.php` y sin query.
     * Lo demás se devuelve solo sin la query.
     */
    public static function rutaCanonica(string $ruta): string
    {
        $ruta = (string) strtok($ruta, '?');
        $corta = preg_replace('~^(.*/src/[^/]+/)infrastructure/ui/http/controllers/([^/]+)\.php$~', '$1$2', $ruta);

        return is_string($corta) ? $corta : $ruta;
    }

    /**
     * @param array<array-key, mixed> $parametros
     * @return list<string>
     */
    private static function nombresConDatos(array $parametros): array
    {
        $nombres = [];
        foreach (array_keys($parametros) as $nombre) {
            $nombre = (string) $nombre;
            if (in_array($nombre, self::PARAMETROS_NEUTROS, true)
                || in_array($nombre, self::PARAMETROS_DE_RUTA, true)
                || str_starts_with($nombre, 'scroll_id_')
            ) {
                continue;
            }
            $nombres[] = $nombre;
        }

        return $nombres;
    }

    /**
     * @param array<array-key, mixed> $datos
     * @param callable(string): bool $capsulaDeLaSesion
     */
    private static function traeCapsulaDeLaSesion(array $datos, callable $capsulaDeLaSesion): bool
    {
        foreach ($datos as $nombre => $valor) {
            if (!str_starts_with((string) $nombre, 'ctx')) {
                continue;
            }
            foreach (is_array($valor) ? $valor : [$valor] as $capsula) {
                if (is_string($capsula) && $capsula !== '' && $capsulaDeLaSesion($capsula)) {
                    return true;
                }
            }
        }

        return false;
    }

    /** @param list<string> $nombres */
    private static function soloCapsulas(array $nombres): bool
    {
        foreach ($nombres as $nombre) {
            if (!str_starts_with($nombre, 'ctx')) {
                return false;
            }
        }

        return true;
    }

    private static function rutaExenta(string $ruta): bool
    {
        $ruta = self::rutaCanonica($ruta);
        $posSrc = strpos($ruta, '/src/');
        if ($posSrc !== false) {
            return in_array(substr($ruta, $posSrc), self::RUTAS_CLIENTE_MOVIL, true);
        }
        if (str_contains($ruta, 'udm4') || str_ends_with($ruta, self::RUTA_FIRMADOR_E2E)) {
            return true;
        }

        // La portada: `/<webdir>/index.php` o `/<webdir>/`. No vale un `index.php` de más adentro.
        return preg_match('~^/[^/]*/?(index\.php)?$~', $ruta) === 1;
    }
}
