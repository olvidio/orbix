<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\config\ReplicaSelectPolicy;

/**
 * Crear / eliminar / llenar tablas de aplicación (herramienta apptables).
 */
final class ApptablesUpdate
{
    private const ACCIONES_ESQUEMA = [
        'crear_esquema',
        'eliminar_esquema',
        'llenar_esquema',
    ];

    /**
     * @param array<string, mixed> $post
     * @return array{ok: true, mensaje: string, bases: list<string>, replica: bool}
     */
    public function ejecutar(array $post): array
    {
        $idApp = (int) ($post['id_app'] ?? 0);
        $esquema = (string) ($post['esquema'] ?? '');
        $accion = (string) ($post['accion'] ?? '');

        $aTodasApps = $_SESSION['config']['a_apps'] ?? [];
        if (!is_array($aTodasApps)) {
            throw new \RuntimeException(_('No hay aplicaciones configuradas en la sesión.'));
        }

        $nomApp = array_search($idApp, $aTodasApps, true);
        if ($nomApp === false || $nomApp === '') {
            throw new \RuntimeException(_('Aplicación no válida.'));
        }

        if ($accion === '') {
            throw new \RuntimeException(_('Acción no indicada.'));
        }

        if (in_array($accion, self::ACCIONES_ESQUEMA, true) && $esquema === '') {
            throw new \RuntimeException(_('Debe elegir un esquema.'));
        }

        $claseGlobal = "$nomApp\\db\\DB";
        $claseEsquema = "$nomApp\\db\\DBEsquema";
        $claseGlobalSrc = 'src\\' . "$nomApp\\db\\DB";
        $claseEsquemaSrc = 'src\\' . "$nomApp\\db\\DBEsquema";

        $ejecutado = match ($accion) {
            'crear_global' => $this->ejecutarGlobal($claseGlobal, $claseGlobalSrc, 'createAll'),
            'eliminar_global' => $this->ejecutarGlobal($claseGlobal, $claseGlobalSrc, 'dropAll'),
            'crear_esquema' => $this->ejecutarEsquema($claseEsquema, $claseEsquemaSrc, $esquema, 'createAll'),
            'eliminar_esquema' => $this->ejecutarEsquema($claseEsquema, $claseEsquemaSrc, $esquema, 'dropAll'),
            'llenar_esquema' => $this->ejecutarEsquema($claseEsquema, $claseEsquemaSrc, $esquema, 'llenarAll'),
            default => throw new \RuntimeException(_('Acción no reconocida.')),
        };

        if (!$ejecutado) {
            throw new \RuntimeException(
                sprintf(_('La aplicación %s no define clases DB para esta operación.'), (string) $nomApp)
            );
        }

        $verificado = [];
        if ($accion === 'crear_global') {
            $verificado = (new ApptablesVerificarGlobal())->verificar((string) $nomApp);
        }

        return [
            'ok' => true,
            'mensaje' => $this->mensajeExito($accion, (string) $nomApp, $esquema),
            'bases' => $this->basesAfectadas($accion, (string) $nomApp),
            'replica' => ReplicaSelectPolicy::incluirSelect(),
            'verificado' => $verificado,
        ];
    }

    /**
     * Bases lógicas tocadas según la acción (informativo para apptables).
     *
     * @return list<string>
     */
    private function basesAfectadas(string $accion, string $nomApp): array
    {
        $bases = in_array($nomApp, self::APPS_GLOBAL_SV_E, true) ? ['sv-e'] : ['comun'];
        if (in_array($nomApp, self::APPS_GLOBAL_SFSV, true)) {
            $bases = ['sv'];
        }
        if (in_array($nomApp, self::APPS_GLOBAL_MIXTAS, true)) {
            $bases = ['comun', 'sv-e'];
        }

        if (
            ReplicaSelectPolicy::incluirSelect()
            && in_array($accion, ['crear_global', 'eliminar_global', 'crear_esquema', 'eliminar_esquema'], true)
        ) {
            if (in_array('comun', $bases, true)) {
                $bases[] = 'comun_select';
            }
            if (in_array('sv-e', $bases, true) || in_array('sv', $bases, true)) {
                $bases[] = 'sv-e_select';
            }
        }

        return array_values(array_unique($bases));
    }

    /** Apps cuyas tablas globales viven en sv-e (exterior). */
    private const APPS_GLOBAL_SV_E = [
        'encargossacd', 'actividadessacd', 'usuarios', 'zonassacd',
    ];

    /** Apps cuyas tablas globales viven en sv (publicv). */
    private const APPS_GLOBAL_SFSV = [
        'inventario', 'certificados', 'tablonanuncios', 'cartaspresentacion',
    ];

    /** Apps con tablas globales en comun y en sv-e. */
    private const APPS_GLOBAL_MIXTAS = [
        'procesos', 'cambios',
    ];

    /**
     * @return bool true si se invocó al menos una clase
     */
    private function ejecutarGlobal(string $claseLegacy, string $claseSrc, string $metodo): bool
    {
        $ejecutado = false;
        foreach ([$claseLegacy, $claseSrc] as $clase) {
            if (!class_exists($clase)) {
                continue;
            }
            $instancia = new $clase();
            if (!method_exists($instancia, $metodo)) {
                continue;
            }
            $instancia->{$metodo}();
            $ejecutado = true;
        }

        return $ejecutado;
    }

    /**
     * @return bool true si se invocó al menos una clase
     */
    private function ejecutarEsquema(string $claseLegacy, string $claseSrc, string $esquema, string $metodo): bool
    {
        $ejecutado = false;
        foreach ([$claseLegacy, $claseSrc] as $clase) {
            if (!class_exists($clase)) {
                continue;
            }
            $instancia = new $clase($esquema);
            if (!method_exists($instancia, $metodo)) {
                continue;
            }
            $instancia->{$metodo}();
            $ejecutado = true;
        }

        return $ejecutado;
    }

    private function mensajeExito(string $accion, string $nomApp, string $esquema): string
    {
        return match ($accion) {
            'crear_global' => sprintf(_('Tablas globales de %s creadas correctamente.'), $nomApp),
            'eliminar_global' => sprintf(_('Tablas globales de %s eliminadas correctamente.'), $nomApp),
            'crear_esquema' => sprintf(_('Tablas de esquema de %s creadas correctamente (%s).'), $nomApp, $esquema),
            'eliminar_esquema' => sprintf(_('Tablas de esquema de %s eliminadas correctamente (%s).'), $nomApp, $esquema),
            'llenar_esquema' => sprintf(_('Tablas de esquema de %s rellenadas correctamente (%s).'), $nomApp, $esquema),
            default => _('Operación completada correctamente.'),
        };
    }
}
