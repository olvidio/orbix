<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use PDO;
use src\devel_db_admin\infrastructure\DBAlterSchema;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use Throwable;

/**
 * Comprueba el estado intermedio/final de un renombre de esquema (BD + ficheros .inc + db_idschema + defaults de columnas),
 * alineado con {@see RenombrarEsquema}. Claves de esquema: {@see ConfigDB::clavesEnFicheroRoles} / {@see ConfigDB::ficheroIncNombre}
 * (`comun.roles.inc` en formato partido, o `pruebas-comun.inc` / `comun.inc` en legado).
 * Origen + región + delegación para comprobar un renombre; solo región + delegación si se usa comprobación «solo destino» (véase {@see RenombrarEsquemaVerificacionContexto::soloDestinoVerificacion}).
 */
final class VerificarEstadoRenombrarEsquema
{
    /** En sv/sv-e la tabla vive en `public`; el search_path apunta a publicv y `db_idschema` sin calificar falla. */
    private const TABLA_DB_IDSCHEMA = 'public.db_idschema';

    /** @return array{listo: bool, resumen: string, bloques: list<array{nombre: string, items: list<array{texto: string, estado: string}>}>, meta: array<string, mixed>} */
    public function ejecutar(
        string $esquemaOrigen,
        string $region,
        string $dl,
        int $comun,
        int $sv,
        int $sf,
    ): array {
        $campoOrigen = trim($esquemaOrigen);
        $regionT = trim($region);
        $dlT = trim($dl);
        if ($campoOrigen === '' && $regionT !== '' && $dlT !== '') {
            $ctx = RenombrarEsquemaVerificacionContexto::soloDestinoVerificacion($region, $dl, $comun, $sv, $sf);
        } else {
            $ctx = RenombrarEsquemaVerificacionContexto::desdeEntrada($esquemaOrigen, $region, $dl, $comun, $sv, $sf);
        }
        if (is_array($ctx)) {
            return $ctx;
        }

        $esquema_old = $ctx->esquemaOld;
        $esquema_oldv = $ctx->esquemaOldv;
        $esquema_oldf = $ctx->esquemaOldf;
        $region = $ctx->region;
        $dl = $ctx->dl;
        $esquema_new = $ctx->esquemaNew;
        $esquema_newv = $ctx->esquemaNewv;
        $esquema_newf = $ctx->esquemaNewf;
        $comun = $ctx->comun;
        $sv = $ctx->sv;
        $sf = $ctx->sf;
        $sinRenombreEfectivo = $ctx->sinRenombreEfectivo;
        $isDocker = $ctx->isDocker;

        $soloDestino = $ctx->soloDestinoComprobacion;

        $bloques = [];
        $oImportar = new ConfigDB('importar');

        $bloques[] = $this->bloqueBd(
            _('comun (principal)'),
            $this->pdoDesdeImportar($oImportar, 'public'),
            $esquema_old,
            $esquema_new,
            $esquema_old,
            $esquema_new,
            'comun',
            [$esquema_old, $esquema_oldv, $esquema_oldf, $esquema_new, $esquema_newv, $esquema_newf],
        );

        if (!$isDocker) {
            $bloques[] = $this->bloqueBd(
                _('comun (réplica / select)'),
                $this->pdoDesdeImportar($oImportar, 'public_select'),
                $esquema_old,
                $esquema_new,
                $esquema_old,
                $esquema_new,
                'comun',
                [$esquema_old, $esquema_oldv, $esquema_oldf, $esquema_new, $esquema_newv, $esquema_newf],
            );
        }

        $bloques[] = $this->bloqueBd(
            _('sv'),
            $this->pdoDesdeImportar($oImportar, 'publicv'),
            $esquema_oldv,
            $esquema_newv,
            $esquema_oldv,
            $esquema_newv,
            'sv',
            [$esquema_old, $esquema_oldv, $esquema_oldf, $esquema_new, $esquema_newv, $esquema_newf],
        );

        $bloques[] = $this->bloqueBd(
            _('sv-e'),
            $this->pdoDesdeImportar($oImportar, 'publicv-e'),
            $esquema_oldv,
            $esquema_newv,
            $esquema_oldv,
            $esquema_newv,
            'sv-e',
            [$esquema_old, $esquema_oldv, $esquema_oldf, $esquema_new, $esquema_newv, $esquema_newf],
        );

        if (!$isDocker) {
            $bloques[] = $this->bloqueBd(
                _('sv-e (réplica / select)'),
                $this->pdoDesdeImportar($oImportar, 'publicv-e_select'),
                $esquema_oldv,
                $esquema_newv,
                $esquema_oldv,
                $esquema_newv,
                'sv-e',
                [$esquema_old, $esquema_oldv, $esquema_oldf, $esquema_new, $esquema_newv, $esquema_newf],
            );
        }

        if ($sf !== 0) {
            $bloques[] = $this->bloqueBd(
                _('sf'),
                $this->pdoDesdeImportar($oImportar, 'publicf'),
                $esquema_oldf,
                $esquema_newf,
                $esquema_oldf,
                $esquema_newf,
                'sf',
                [$esquema_old, $esquema_oldv, $esquema_oldf, $esquema_new, $esquema_newv, $esquema_newf],
            );
        }

        if ($comun !== 0) {
            $specComun = RenombrarEsquemaDefaultsCatalog::comun($esquema_new, $region, $dl);
            $bloques[] = $this->bloqueDefaultsColumnas(
                _('Defaults de columnas (comun)'),
                $this->pdoDesdeImportar($oImportar, 'public'),
                $esquema_new,
                $specComun,
            );
            if (!$isDocker) {
                $bloques[] = $this->bloqueDefaultsColumnas(
                    _('Defaults de columnas (comun_select)'),
                    $this->pdoDesdeImportar($oImportar, 'public_select'),
                    $esquema_new,
                    $specComun,
                );
            }
        }

        if ($sv !== 0) {
            $specSv = RenombrarEsquemaDefaultsCatalog::sv($esquema_newv, $region, $dl);
            $specSve = RenombrarEsquemaDefaultsCatalog::svE($esquema_newv, $dl);
            $bloques[] = $this->bloqueDefaultsColumnas(
                _('Defaults de columnas (sv)'),
                $this->pdoDesdeImportar($oImportar, 'publicv'),
                $esquema_newv,
                $specSv,
            );
            $bloques[] = $this->bloqueDefaultsColumnas(
                _('Defaults de columnas (sv-e)'),
                $this->pdoDesdeImportar($oImportar, 'publicv-e'),
                $esquema_newv,
                $specSve,
            );
            if (!$isDocker) {
                $bloques[] = $this->bloqueDefaultsColumnas(
                    _('Defaults de columnas (sv-e_select)'),
                    $this->pdoDesdeImportar($oImportar, 'publicv-e_select'),
                    $esquema_newv,
                    $specSve,
                );
            }
        }

        $bloques[] = $this->bloqueInc(
            _('Ficheros de contraseña (.inc)'),
            $isDocker,
            $esquema_old,
            $esquema_new,
            $esquema_oldv,
            $esquema_newv,
            $esquema_oldf,
            $esquema_newf,
            $sf !== 0,
        );

        $bloques[] = $this->bloqueOpcionesFormulario($comun, $sv, $sf, $sinRenombreEfectivo, $soloDestino);

        $listo = $this->calcularListo($bloques);
        if ($listo) {
            if ($soloDestino) {
                $resumen = _('Todo cuadra con la instalación del esquema destino (comprobación sin nombre de origen: no se valida que un nombre antiguo haya desaparecido).');
            } elseif ($sinRenombreEfectivo) {
                $resumen = _('Todo cuadra con la instalación actual (comprobación frente al propio esquema, sin cambio de nombre de destino): esquemas/roles, db_idschema, claves .inc y defaults según el catálogo.');
            } else {
                $resumen = _('Todo cuadra con el objetivo: nombres nuevos presentes, viejos ausentes, claves e idschema coherentes, y defaults de columnas según el catálogo del renombre.');
            }
        } else {
            if ($soloDestino) {
                $resumen = _('Hay pendientes o incoherencias en el esquema destino (defaults, .inc, db_idschema, etc.); no se ha contrastado un nombre antiguo concreto.');
            } elseif ($sinRenombreEfectivo) {
                $resumen = _('Hay pendientes o incoherencias respecto al esquema actual (incluidos defaults ALTER COLUMN); revise la lista.');
            } else {
                $resumen = _('Hay pendientes o incoherencias (incluidos defaults ALTER COLUMN); revise la lista y puede volver a ejecutar el renombre para completar.');
            }
        }

        return [
            'listo' => $listo,
            'resumen' => $resumen,
            'bloques' => $bloques,
            'meta' => [
                'esquema_origen' => $ctx->esquemaOrigenCampo,
                'objetivo_base' => $esquema_new,
                'origen_base' => $esquema_old,
                'docker' => $isDocker,
                'region_efectiva' => $region,
                'dl_efectiva' => $dl,
                'sin_renombre_efectivo' => $sinRenombreEfectivo,
                'comprobacion_solo_destino' => $soloDestino,
            ],
        ];
    }

    private function bloqueOpcionesFormulario(
        int $comun,
        int $sv,
        int $sf,
        bool $sinRenombreEfectivo,
        bool $soloDestinoComprobacion,
    ): array {
        $items = [];
        $items[] = [
            'texto' => _('Esta comprobación revisa esquemas/roles, db_idschema, ficheros .inc y los ALTER COLUMN SET DEFAULT del renombre (réplicas omitidas en docker; bloques de defaults de comun/sv solo si los checkboxes correspondientes están marcados).'),
            'estado' => 'ok',
        ];
        if ($soloDestinoComprobacion) {
            $items[] = [
                'texto' => _('Modo solo destino: no hay esquema de origen en el formulario; se comprueba únicamente la coherencia del nombre región–delegación elegido (no si un nombre antiguo sigue o ha desaparecido). «Corregir» sin origen solo reaplica defaults en ese nombre, no sincroniza .inc ni db_idschema. Para la transición origen→destino rellene el origen o use «Cambiar nombre» con ambos datos.'),
                'estado' => 'ok',
            ];
        } else {
            $items[] = [
                'texto' => _('Con esquema de origen y destino definidos se valida también la transición (ausencia del viejo / presencia del nuevo donde aplique).'),
                'estado' => 'ok',
            ];
        }
        if ($sinRenombreEfectivo && !$soloDestinoComprobacion) {
            $items[] = [
                'texto' => _('Objetivo de nombre igual al esquema actual: se comprueba coherencia de la instalación (no el estado intermedio de un renombre).'),
                'estado' => 'ok',
            ];
        }
        $items[] = [
            'texto' => sprintf(
                _('Checkboxes actuales (solo afectan al paso ALTER de defaults al ejecutar el renombre): comun=%s, sv=%s, sf=%s'),
                $comun !== 0 ? '1' : '0',
                $sv !== 0 ? '1' : '0',
                $sf !== 0 ? '1' : '0',
            ),
            'estado' => 'ok',
        ];

        return ['nombre' => _('Alcance de la comprobación'), 'items' => $items];
    }

    /**
     * @param list<string> $schemasIdschema
     * @return array{nombre: string, items: list<array{texto: string, estado: string}>}
     */
    private function bloqueBd(
        string $nombre,
        ?PDO $pdo,
        string $schemaOld,
        string $schemaNew,
        string $roleOld,
        string $roleNew,
        string $dbIdschemaLabel,
        array $schemasIdschema,
    ): array {
        if ($schemaOld === $schemaNew && $roleOld === $roleNew) {
            return $this->bloqueBdSinRenombre($nombre, $pdo, $schemaOld, $roleOld, $dbIdschemaLabel, $schemasIdschema);
        }

        $items = [];
        if ($pdo === null) {
            $items[] = ['texto' => _('No se ha podido conectar a esta base (revisar importar.inc / red).'), 'estado' => 'error'];

            return ['nombre' => $nombre, 'items' => $items];
        }

        $so = $this->existeEsquema($pdo, $schemaOld);
        $sn = $this->existeEsquema($pdo, $schemaNew);
        $ro = $this->existeRol($pdo, $roleOld);
        $rn = $this->existeRol($pdo, $roleNew);

        if ($so && !$sn) {
            $items[] = [
                'texto' => sprintf(
                    _('Renombre a medias en esta instancia: sigue el esquema «%1$s» y aún no existe «%2$s». Suele ocurrir si «Cambiar nombre del esquema» se interrumpió antes de terminar en esta base (p. ej. comun vs sv).'),
                    $schemaOld,
                    $schemaNew,
                ),
                'estado' => 'error',
            ];
        }
        if ($ro && !$rn) {
            $items[] = [
                'texto' => sprintf(
                    _('Renombre de rol a medias: el rol «%1$s» sigue existiendo y no existe «%2$s».'),
                    $roleOld,
                    $roleNew,
                ),
                'estado' => 'error',
            ];
        }

        $items[] = $this->itemEsperado(
            sprintf(_('Esquema antiguo «%s»'), $schemaOld),
            !$so,
            _('Ya no existe en esta BD (correcto tras el renombre).'),
            _('Sigue existiendo: falta completar el renombre o eliminarlo manualmente.'),
        );
        $items[] = $this->itemEsperado(
            sprintf(_('Esquema nuevo «%s»'), $schemaNew),
            $sn,
            _('Existe en esta BD.'),
            _('No existe: el renombre no ha llegado a crear este esquema en esta instancia.'),
        );
        $items[] = $this->itemEsperado(
            sprintf(_('Rol antiguo «%s»'), $roleOld),
            !$ro,
            _('Ya no existe (correcto tras el renombre).'),
            _('Sigue existiendo: falta renombrar el rol o queda huérfano.'),
        );
        $items[] = $this->itemEsperado(
            sprintf(_('Rol nuevo «%s»'), $roleNew),
            $rn,
            _('Existe en esta instancia.'),
            _('No existe: falta crear/renombrar el usuario en esta instancia.'),
        );

        if ($so && $sn) {
            $items[] = [
                'texto' => _('Incoherencia: coexisten esquema antiguo y nuevo en la misma BD.'),
                'estado' => 'error',
            ];
        }
        if ($ro && $rn) {
            $items[] = [
                'texto' => _('Incoherencia: coexisten rol antiguo y nuevo (misma instancia PostgreSQL).'),
                'estado' => 'error',
            ];
        }

        if ($so) {
            $propO = $this->propietarioEsquema($pdo, $schemaOld);
            $items[] = $this->itemEsperado(
                sprintf(_('Propietario del esquema antiguo «%s» (= rol homónimo)'), $schemaOld),
                $propO !== null && $propO === $schemaOld,
                sprintf(_('Coincide («%s»).'), (string) $propO),
                sprintf(
                    _('No coincide: el propietario es «%s». Use «Corregir» o ALTER SCHEMA … OWNER TO «%s».'),
                    $propO ?? _('desconocido'),
                    $schemaOld,
                ),
            );
        }
        if ($sn) {
            $propN = $this->propietarioEsquema($pdo, $schemaNew);
            $items[] = $this->itemEsperado(
                sprintf(_('Propietario del esquema nuevo «%s» (= rol homónimo)'), $schemaNew),
                $propN !== null && $propN === $schemaNew,
                sprintf(_('Coincide («%s»).'), (string) $propN),
                sprintf(
                    _('No coincide: el propietario es «%s». Use «Corregir» o ALTER SCHEMA … OWNER TO «%s».'),
                    $propN ?? _('desconocido'),
                    $schemaNew,
                ),
            );
            $duAu = $this->duenyoRelacion($pdo, $schemaNew, 'aux_usuarios');
            if ($duAu !== null) {
                $items[] = $this->itemEsperado(
                    sprintf(_('Dueño de «%s.aux_usuarios» (= rol «%s»)'), $schemaNew, $schemaNew),
                    $duAu === $schemaNew,
                    sprintf(_('Correcto («%s»).'), $duAu),
                    sprintf(
                        _('El dueño es «%s»: la sesión del rol puede dar error 42501 en aux_usuarios. Use «Corregir».'),
                        $duAu,
                    ),
                );
            }
        }

        foreach ($this->analizarDbIdschema($pdo, $schemasIdschema) as $line) {
            $items[] = $line;
        }

        return ['nombre' => $nombre . ' — ' . _('tabla db_idschema') . ' (' . $dbIdschemaLabel . ')', 'items' => $items];
    }

    /**
     * Misma BD que bloqueBd cuando origen y destino coinciden (p. ej. comprobar solo con el esquema elegido).
     *
     * @param list<string> $schemasIdschema Seis entradas (trío viejo + trío nuevo); aquí solo se usa el trío base.
     * @return array{nombre: string, items: list<array{texto: string, estado: string}>}
     */
    private function bloqueBdSinRenombre(
        string $nombre,
        ?PDO $pdo,
        string $schema,
        string $role,
        string $dbIdschemaLabel,
        array $schemasIdschema,
    ): array {
        $items = [];
        if ($pdo === null) {
            $items[] = ['texto' => _('No se ha podido conectar a esta base (revisar importar.inc / red).'), 'estado' => 'error'];

            return ['nombre' => $nombre, 'items' => $items];
        }

        $se = $this->existeEsquema($pdo, $schema);
        $re = $this->existeRol($pdo, $role);
        $items[] = $this->itemEsperado(
            sprintf(_('Esquema «%s» (debe existir en esta BD)'), $schema),
            $se,
            _('Existe.'),
            _('No existe en esta BD. Si pretendía comprobar un renombre hacia otro nombre (origen ≠ destino), rellene región y delegación objetivo en el formulario; si van vacíos, el objetivo se deduce del desplegable y puede coincidir con el origen.'),
        );
        $items[] = $this->itemEsperado(
            sprintf(_('Rol «%s» (debe existir en esta instancia)'), $role),
            $re,
            _('Existe.'),
            _('No existe en esta instancia.'),
        );

        if ($se && $re) {
            $p = $this->propietarioEsquema($pdo, $schema);
            $items[] = $this->itemEsperado(
                sprintf(_('Propietario del esquema «%s» (= rol homónimo)'), $schema),
                $p !== null && $p === $schema,
                sprintf(_('Coincide («%s»).'), (string) $p),
                sprintf(
                    _('No coincide: el propietario es «%s». Use «Corregir» o ALTER SCHEMA … OWNER TO «%s».'),
                    $p ?? _('desconocido'),
                    $schema,
                ),
            );
            $duAu = $this->duenyoRelacion($pdo, $schema, 'aux_usuarios');
            if ($duAu !== null) {
                $items[] = $this->itemEsperado(
                    sprintf(_('Dueño de «%s.aux_usuarios» (= rol «%s»)'), $schema, $schema),
                    $duAu === $schema,
                    sprintf(_('Correcto («%s»).'), $duAu),
                    sprintf(
                        _('El dueño es «%s»: la sesión del rol puede dar error 42501 en aux_usuarios. Use «Corregir».'),
                        $duAu,
                    ),
                );
            }
        }

        $trio = [$schemasIdschema[0] ?? '', $schemasIdschema[1] ?? '', $schemasIdschema[2] ?? ''];
        foreach ($this->analizarDbIdschemaTrioActual($pdo, $trio) as $line) {
            $items[] = $line;
        }

        return ['nombre' => $nombre . ' — ' . _('tabla db_idschema') . ' (' . $dbIdschemaLabel . ')', 'items' => $items];
    }

    /**
     * @param list<string> $trio Esquemas base, v y f del mismo ámbito (comun o sv/sv-e/sf).
     * @return list<array{texto: string, estado: string}>
     */
    private function analizarDbIdschemaTrioActual(PDO $pdo, array $trio): array
    {
        $items = [];
        $trio = array_values(array_filter($trio, static fn (string $s): bool => $s !== ''));
        if ($trio === []) {
            return $items;
        }
        try {
            $placeholders = implode(',', array_fill(0, count($trio), '?'));
            $st = $pdo->prepare('SELECT schema FROM ' . self::TABLA_DB_IDSCHEMA . " WHERE schema IN ($placeholders)");
            $st->execute($trio);
            $found = $st->fetchAll(PDO::FETCH_COLUMN);
            $found = is_array($found) ? $found : [];
        } catch (Throwable) {
            $items[] = ['texto' => _('No se pudo leer public.db_idschema (¿tabla ausente o sin permisos?).'), 'estado' => 'aviso'];

            return $items;
        }

        $missing = [];
        foreach ($trio as $s) {
            if (!in_array($s, $found, true)) {
                $missing[] = $s;
            }
        }
        foreach ($missing as $s) {
            $items[] = [
                'texto' => sprintf(_('db_idschema: falta fila para esquema "%s".'), $s),
                'estado' => 'falta',
            ];
        }
        if ($missing === [] && $trio !== []) {
            $items[] = ['texto' => _('db_idschema: trío de esquemas registrado.'), 'estado' => 'ok'];
        }

        return $items;
    }

    /**
     * @param list<string> $schemas
     * @return list<array{texto: string, estado: string}>
     */
    private function analizarDbIdschema(PDO $pdo, array $schemas): array
    {
        $items = [];
        try {
            $placeholders = implode(',', array_fill(0, count($schemas), '?'));
            $st = $pdo->prepare('SELECT schema FROM ' . self::TABLA_DB_IDSCHEMA . " WHERE schema IN ($placeholders)");
            $st->execute($schemas);
            $found = $st->fetchAll(PDO::FETCH_COLUMN);
            $found = is_array($found) ? $found : [];
        } catch (Throwable) {
            $items[] = ['texto' => _('No se pudo leer public.db_idschema (¿tabla ausente o sin permisos?).'), 'estado' => 'aviso'];

            return $items;
        }

        $oldTrio = [$schemas[0], $schemas[1], $schemas[2]];
        $newTrio = [$schemas[3], $schemas[4], $schemas[5]];
        $oldPresent = array_values(array_intersect($oldTrio, $found));
        $newPresent = array_values(array_intersect($newTrio, $found));

        foreach ($oldPresent as $s) {
            $items[] = [
                'texto' => sprintf(_('db_idschema: sigue fila con esquema antiguo "%s" — falta UPDATE o quedó a medias.'), $s),
                'estado' => 'falta',
            ];
        }
        foreach ($newTrio as $s) {
            if (!in_array($s, $newPresent, true)) {
                $items[] = [
                    'texto' => sprintf(_('db_idschema: falta fila para esquema nuevo "%s".'), $s),
                    'estado' => 'falta',
                ];
            }
        }
        if ($oldPresent === [] && count($newPresent) === 3) {
            $items[] = ['texto' => _('db_idschema: trío nuevo completo, sin filas del trío antiguo.'), 'estado' => 'ok'];
        }

        return $items;
    }

    /**
     * @return array{nombre: string, items: list<array{texto: string, estado: string}>}
     */
    private function bloqueInc(
        string $nombre,
        bool $isDocker,
        string $oldBase,
        string $newBase,
        string $oldV,
        string $newV,
        string $oldF,
        string $newF,
        bool $incluirSf,
    ): array {
        $items = [];
        $defs = [
            ['fichero' => 'comun', 'old' => $oldBase, 'new' => $newBase],
            ['fichero' => 'sv', 'old' => $oldV, 'new' => $newV],
            ['fichero' => 'sv-e', 'old' => $oldV, 'new' => $newV],
        ];
        if (!$isDocker) {
            $defs[] = ['fichero' => 'comun_select', 'old' => $oldBase, 'new' => $newBase];
            $defs[] = ['fichero' => 'sv-e_select', 'old' => $oldV, 'new' => $newV];
        }
        if ($incluirSf) {
            $defs[] = ['fichero' => 'sf', 'old' => $oldF, 'new' => $newF];
        }

        foreach ($defs as $row) {
            $base = $row['fichero'];
            $archivo = ConfigDB::ficheroIncNombre($base);
            $keys = ConfigDB::clavesEnFicheroRoles($base);
            if ($row['old'] === $row['new']) {
                $clave = $row['old'];
                $has = $clave !== '' && in_array($clave, $keys, true);
                $items[] = [
                    'texto' => $has
                        ? sprintf(_('%s: clave "%s" presente.'), $archivo, $clave)
                        : sprintf(_('%s: falta la clave "%s".'), $archivo, $clave),
                    'estado' => $has ? 'ok' : 'falta',
                ];

                continue;
            }
            $hasOld = in_array($row['old'], $keys, true);
            $hasNew = in_array($row['new'], $keys, true);
            if ($hasNew && !$hasOld) {
                $items[] = [
                    'texto' => sprintf(_('%s: clave nueva "%s" presente, antigua "%s" ausente.'), $archivo, $row['new'], $row['old']),
                    'estado' => 'ok',
                ];
            } elseif ($hasOld && !$hasNew) {
                $items[] = [
                    'texto' => sprintf(_('%s: sigue la clave antigua "%s"; falta "%s".'), $archivo, $row['old'], $row['new']),
                    'estado' => 'falta',
                ];
            } elseif ($hasOld && $hasNew) {
                $items[] = [
                    'texto' => sprintf(_('%s: coexisten "%s" y "%s" (limpiar la antigua al terminar).'), $archivo, $row['old'], $row['new']),
                    'estado' => 'error',
                ];
            } else {
                $items[] = [
                    'texto' => sprintf(_('%s: no hay clave ni "%s" ni "%s" (¿otro nombre o fichero ausente?).'), $archivo, $row['old'], $row['new']),
                    'estado' => 'aviso',
                ];
            }
        }

        return ['nombre' => $nombre, 'items' => $items];
    }

    private function itemEsperado(string $titulo, bool $ok, string $detalleOk, string $detalleMal): array
    {
        return [
            'texto' => $titulo . ' — ' . ($ok ? $detalleOk : $detalleMal),
            'estado' => $ok ? 'ok' : 'falta',
        ];
    }

    private function existeEsquema(PDO $pdo, string $n): bool
    {
        $st = $pdo->prepare('SELECT 1 FROM pg_namespace WHERE nspname = :n LIMIT 1');
        $st->execute(['n' => $n]);

        return (bool) $st->fetchColumn();
    }

    private function existeRol(PDO $pdo, string $r): bool
    {
        $st = $pdo->prepare('SELECT 1 FROM pg_roles WHERE rolname = :r LIMIT 1');
        $st->execute(['r' => $r]);

        return (bool) $st->fetchColumn();
    }

    private function propietarioEsquema(PDO $pdo, string $schema): ?string
    {
        $st = $pdo->prepare(
            'SELECT r.rolname FROM pg_namespace n JOIN pg_roles r ON r.oid = n.nspowner WHERE n.nspname = :n LIMIT 1',
        );
        $st->execute(['n' => $schema]);
        $v = $st->fetchColumn();

        return $v !== false ? (string) $v : null;
    }

    /** Dueño de una relación concreta en el esquema (tabla/vista/MV/foreign table); null si no existe. */
    private function duenyoRelacion(PDO $pdo, string $schema, string $relname): ?string
    {
        $st = $pdo->prepare(
            'SELECT r.rolname FROM pg_class c
             INNER JOIN pg_namespace n ON n.oid = c.relnamespace
             INNER JOIN pg_roles r ON r.oid = c.relowner
             WHERE n.nspname = :s AND c.relname = :r
               AND c.relkind IN (\'r\', \'p\', \'v\', \'m\', \'f\')
             LIMIT 1',
        );
        $st->execute(['s' => $schema, 'r' => $relname]);
        $v = $st->fetchColumn();

        return $v !== false ? (string) $v : null;
    }

    private function pdoDesdeImportar(ConfigDB $importar, string $esquema): ?PDO
    {
        try {
            $cfg = $importar->getEsquema($esquema);

            return (new DBConnection($cfg))->getPDO();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Comprueba pg_attrdef frente al catálogo compartido con {@see RenombrarEsquema}.
     *
     * @param list<array{tabla: string, campo: string, valor: string}> $specs
     * @return array{nombre: string, items: list<array{texto: string, estado: string}>}
     */
    private function bloqueDefaultsColumnas(string $titulo, ?PDO $pdo, string $schema, array $specs): array
    {
        if ($pdo === null) {
            return ['nombre' => $titulo, 'items' => [['texto' => _('No hay conexión a la BD.'), 'estado' => 'error']]];
        }

        $o = new DBAlterSchema();
        $o->setDbConexion($pdo);
        $o->setSchema($schema);

        $seen = [];
        $ok = 0;
        $skipTabla = 0;
        $failures = [];

        foreach ($specs as $row) {
            $tabla = $row['tabla'];
            $campo = $row['campo'];
            $k = $tabla . "\0" . $campo;
            if (isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;

            $full = '"' . str_replace('"', '""', $schema) . '".' . $tabla;
            if (!$o->existeTabla($full)) {
                $skipTabla++;

                continue;
            }

            $cur = $this->leerDefaultColumna($pdo, $schema, $tabla, $campo);
            $exp = $row['valor'];
            if (!$this->expresionDefaultEquivale($cur, $exp)) {
                $dbTxt = $cur === null ? _('(sin default en catálogo)') : $cur;
                $failures[] = $tabla . '.' . $campo . ': ' . sprintf(_('no coincide; en BD: %s'), $dbTxt);
            } else {
                $ok++;
            }
        }

        $items = [];
        $items[] = [
            'texto' => sprintf(
                _('Defaults ALTER: %d columnas coinciden con el objetivo; tablas omitidas (no existen en el esquema): %d.'),
                $ok,
                $skipTabla,
            ),
            'estado' => $failures === [] ? 'ok' : 'falta',
        ];
        $max = 40;
        foreach (array_slice($failures, 0, $max) as $f) {
            $items[] = ['texto' => $f, 'estado' => 'falta'];
        }
        if (count($failures) > $max) {
            $items[] = [
                'texto' => sprintf(_('… y %d desajustes más de defaults.'), count($failures) - $max),
                'estado' => 'falta',
            ];
        }

        return ['nombre' => $titulo, 'items' => $items];
    }

    private function leerDefaultColumna(PDO $pdo, string $schema, string $tabla, string $col): ?string
    {
        $sql = <<<'SQL'
SELECT pg_get_expr(d.adbin, d.adrelid)
FROM pg_catalog.pg_attrdef d
INNER JOIN pg_catalog.pg_attribute a ON a.attrelid = d.adrelid AND a.attnum = d.adnum
INNER JOIN pg_catalog.pg_class c ON c.oid = a.attrelid
INNER JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
WHERE n.nspname = ?
  AND c.relname = ?
  AND a.attname = ?
  AND NOT a.attisdropped
  AND a.attnum > 0
SQL;
        try {
            $st = $pdo->prepare($sql);
            $st->execute([$schema, $tabla, $col]);
            $row = $st->fetch(PDO::FETCH_NUM);
        } catch (Throwable) {
            return null;
        }

        if ($row === false) {
            return null;
        }

        return $row[0] === null ? null : (string) $row[0];
    }

    private function expresionDefaultEquivale(?string $db, string $expected): bool
    {
        if ($db === null) {
            return false;
        }
        if ($this->normalizarExprDefault($db) === $this->normalizarExprDefault($expected)) {
            return true;
        }

        return strtolower(preg_replace('/\s+/', '', $db)) === strtolower(preg_replace('/\s+/', '', $expected));
    }

    private function normalizarExprDefault(string $s): string
    {
        $s = trim(preg_replace('/\s+/', ' ', $s));
        // pg_get_expr suele omitir el calificador public. en funciones del search_path.
        return (string) preg_replace('/\bpublic\./i', '', $s);
    }

    /**
     * @param list<array{nombre: string, items: list<array{texto: string, estado: string}>}> $bloques
     */
    private function calcularListo(array $bloques): bool
    {
        foreach ($bloques as $b) {
            foreach ($b['items'] as $it) {
                if (($it['estado'] ?? '') === 'falta' || ($it['estado'] ?? '') === 'error') {
                    return false;
                }
            }
        }

        return true;
    }
}
