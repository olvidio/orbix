<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use PDO;
use Throwable;
use src\devel_db_admin\infrastructure\DBAlterSchema;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBRol;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Tras {@see VerificarEstadoRenombrarEsquema}: reaplica ALTER COLUMN (defaults), y si el renombre en PostgreSQL
 * ya está hecho (esquema viejo ausente y nuevo presente) sincroniza claves en .inc y filas en db_idschema.
 * Sin esquema de origen pero con región y delegación, solo reaplica defaults sobre el nombre destino (véase {@see RenombrarEsquemaVerificacionContexto::soloDestinoVerificacion}).
 */
final class CorregirEstadoRenombrarEsquema
{
    public function __construct(
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
        private readonly RenombrarEsquema $renombrarEsquema,
    ) {
    }

    /**
     * @return array{acciones: list<string>, avisos: list<string>, verificacion: array<string, mixed>}
     */
    public function ejecutar(string $esquemaOrigen, string $region, string $dl, int $comun, int $sv, int $sf): array
    {
        /** @var list<string> $acciones */
        $acciones = [];
        /** @var list<string> $avisos */
        $avisos = [];

        $campoOrigen = trim($esquemaOrigen);
        $regionT = trim($region);
        $dlT = trim($dl);
        if ($campoOrigen === '' && $regionT !== '' && $dlT !== '') {
            $ctx = RenombrarEsquemaVerificacionContexto::soloDestinoVerificacion($region, $dl, $comun, $sv, $sf);
        } else {
            $ctx = RenombrarEsquemaVerificacionContexto::desdeEntrada($esquemaOrigen, $region, $dl, $comun, $sv, $sf);
        }
        if (is_array($ctx)) {
            return [
                'acciones' => [],
                'avisos' => [_('No se aplicó ninguna corrección: parámetros inválidos.')],
                'verificacion' => $ctx,
            ];
        }

        if ($ctx->soloDestinoComprobacion) {
            $acciones[] = _('Modo solo destino: se reaplican solo los defaults (ALTER COLUMN) en los esquemas del nombre región–delegación; no se sincronizan .inc ni db_idschema sin nombre de origen.');
        }

        if (!$ctx->sinRenombreEfectivo && !$ctx->soloDestinoComprobacion) {
            try {
                foreach ($this->renombrarEsquema->reanudarRenombrePostgreSQL($ctx) as $av) {
                    $avisos[] = $av;
                }
                $acciones[] = _('Renombre PostgreSQL reanudado donde faltaba (esquemas/roles/.inc).');
            } catch (Throwable $e) {
                $avisos[] = sprintf(_('Renombre PostgreSQL: %s'), $e->getMessage());
            }
        }

        try {
            $this->aplicarDefaultsAlter($ctx, $acciones);
        } catch (Throwable $e) {
            $avisos[] = sprintf(_('Defaults: %s'), $e->getMessage());
        }

        try {
            $this->alinearPropietariosEsquema($ctx, $acciones, $avisos);
        } catch (Throwable $e) {
            $avisos[] = sprintf(_('Propietario de esquema: %s'), $e->getMessage());
        }

        if (!$ctx->sinRenombreEfectivo) {
            try {
                $this->sincronizarIncYDbIdschemaTrasRenamePg($ctx, $acciones, $avisos);
            } catch (Throwable $e) {
                $avisos[] = sprintf(_('Sincronización .inc/db_idschema: %s'), $e->getMessage());
            }
        }

        $verificacion = (new VerificarEstadoRenombrarEsquema())->ejecutar(
            $ctx->esquemaOrigenCampo,
            $ctx->region,
            $ctx->dl,
            $comun,
            $sv,
            $sf,
        );

        return [
            'acciones' => $acciones,
            'avisos' => $avisos,
            'verificacion' => $verificacion,
        ];
    }

    /**
     * @param list<string> $acciones
     */
    private function aplicarDefaultsAlter(RenombrarEsquemaVerificacionContexto $ctx, array &$acciones): void
    {
        $oImportar = new ConfigDB('importar');

        if ($ctx->comun !== 0) {
            $pdo = $this->pdoDesdeImportar($oImportar, 'public');
            if ($pdo !== null && $this->existeEsquema($pdo, $ctx->esquemaNew)) {
                $this->runSetDefaults(
                    $pdo,
                    $ctx->esquemaNew,
                    RenombrarEsquemaDefaultsCatalog::comun($ctx->esquemaNew, $ctx->region, $ctx->dl),
                    $acciones,
                    _('comun (principal)'),
                );
            }
            if (!$ctx->isDocker) {
                $pdoS = $this->pdoDesdeImportar($oImportar, 'public_select');
                if ($pdoS !== null && $this->existeEsquema($pdoS, $ctx->esquemaNew)) {
                    $this->runSetDefaults(
                        $pdoS,
                        $ctx->esquemaNew,
                        RenombrarEsquemaDefaultsCatalog::comun($ctx->esquemaNew, $ctx->region, $ctx->dl),
                        $acciones,
                        _('comun_select'),
                    );
                }
            }
        }

        if ($ctx->sv !== 0) {
            $pdo = $this->pdoDesdeImportar($oImportar, 'publicv');
            if ($pdo !== null && $this->existeEsquema($pdo, $ctx->esquemaNewv)) {
                $this->runSetDefaults(
                    $pdo,
                    $ctx->esquemaNewv,
                    RenombrarEsquemaDefaultsCatalog::sv($ctx->esquemaNewv, $ctx->region, $ctx->dl),
                    $acciones,
                    _('sv'),
                );
            }
            $pdoE = $this->pdoDesdeImportar($oImportar, 'publicv-e');
            if ($pdoE !== null && $this->existeEsquema($pdoE, $ctx->esquemaNewv)) {
                $this->runSetDefaults(
                    $pdoE,
                    $ctx->esquemaNewv,
                    RenombrarEsquemaDefaultsCatalog::svE($ctx->esquemaNewv, $ctx->dl),
                    $acciones,
                    _('sv-e'),
                );
            }
            if (!$ctx->isDocker) {
                $pdoEs = $this->pdoDesdeImportar($oImportar, 'publicv-e_select');
                if ($pdoEs !== null && $this->existeEsquema($pdoEs, $ctx->esquemaNewv)) {
                    $this->runSetDefaults(
                        $pdoEs,
                        $ctx->esquemaNewv,
                        RenombrarEsquemaDefaultsCatalog::svE($ctx->esquemaNewv, $ctx->dl),
                        $acciones,
                        _('sv-e_select'),
                    );
                }
            }
        }
    }

    /**
     * @param list<string> $acciones
     * @param list<string> $avisos
     */
    private function alinearPropietariosEsquema(RenombrarEsquemaVerificacionContexto $ctx, array &$acciones, array &$avisos): void
    {
        $oImportar = new ConfigDB('importar');
        $pares = [];
        if ($ctx->comun !== 0) {
            $pares[] = [$this->pdoDesdeImportar($oImportar, 'public'), $ctx->esquemaNew, _('comun (principal)')];
            if (!$ctx->isDocker) {
                $pares[] = [$this->pdoDesdeImportar($oImportar, 'public_select'), $ctx->esquemaNew, _('comun_select')];
            }
        }
        if ($ctx->sv !== 0) {
            $pares[] = [$this->pdoDesdeImportar($oImportar, 'publicv'), $ctx->esquemaNewv, _('sv')];
            $pares[] = [$this->pdoDesdeImportar($oImportar, 'publicv-e'), $ctx->esquemaNewv, _('sv-e')];
            if (!$ctx->isDocker) {
                $pares[] = [$this->pdoDesdeImportar($oImportar, 'publicv-e_select'), $ctx->esquemaNewv, _('sv-e_select')];
            }
        }
        if ($ctx->sf !== 0) {
            $pares[] = [$this->pdoDesdeImportar($oImportar, 'publicf'), $ctx->esquemaNewf, _('sf')];
        }

        foreach ($pares as [$pdo, $schema, $tag]) {
            if ($pdo === null || $schema === '' || !$this->existeEsquema($pdo, $schema)) {
                continue;
            }
            if (!$this->existeRol($pdo, $schema)) {
                $avisos[] = sprintf(_('Propietario (%s): no se repara «%s»: no existe un rol con ese nombre en esta BD.'), $tag, $schema);

                continue;
            }
            $oRol = new DBRol();
            $oRol->setDbConexion($pdo);
            if (!$oRol->repararEsquemaPostRenombre($schema)) {
                $avisos[] = sprintf(
                    _('Reparación de esquema «%s» (%s): no se pudo alinear dueños/privilegios (revisar log o permisos del superusuario de importar).'),
                    $schema,
                    $tag,
                );
            }
        }
    }

    private function existeRol(PDO $pdo, string $r): bool
    {
        $st = $pdo->prepare('SELECT 1 FROM pg_roles WHERE rolname = :r LIMIT 1');
        $st->execute(['r' => $r]);

        return (bool) $st->fetchColumn();
    }

    /**
     * @param list<string> $acciones
     * @param list<string> $avisos
     */
    private function sincronizarIncYDbIdschemaTrasRenamePg(
        RenombrarEsquemaVerificacionContexto $ctx,
        array &$acciones,
        array &$avisos,
    ): void {
        $oImportar = new ConfigDB('importar');
        $repo = $this->dbSchemaRepository;

        $pdoComun = $this->pdoDesdeImportar($oImportar, 'public');
        if ($this->renamePgHecho($pdoComun, $ctx->esquemaOld, $ctx->esquemaNew)) {
            if ($this->incDebeRenombrarClave('comun', $ctx->esquemaOld, $ctx->esquemaNew)) {
                try {
                    (new ConfigDB('comun'))->renombrarListaEsquema('comun', $ctx->esquemaOld, $ctx->esquemaNew);
                    $acciones[] = sprintf(
                        _('%s: claves de esquema alineadas con el nuevo nombre.'),
                        ConfigDB::ficheroIncNombre('comun'),
                    );
                } catch (Throwable $e) {
                    $avisos[] = sprintf(_('%s: %s'), ConfigDB::ficheroIncNombre('comun'), $e->getMessage());
                }
            }
            try {
                $repo->cambiarNombre($ctx->esquemaOld, $ctx->esquemaNew, 'comun');
                $acciones[] = _('db_idschema (comun): sincronizado (UPDATE nombres de esquema).');
            } catch (Throwable $e) {
                $avisos[] = sprintf(_('db_idschema (comun): %s'), $e->getMessage());
            }
        }

        if (!$ctx->isDocker) {
            $pdoSel = $this->pdoDesdeImportar($oImportar, 'public_select');
            if ($this->renamePgHecho($pdoSel, $ctx->esquemaOld, $ctx->esquemaNew)) {
                if ($this->incDebeRenombrarClave('comun_select', $ctx->esquemaOld, $ctx->esquemaNew)) {
                    try {
                        (new ConfigDB('comun'))->renombrarListaEsquema('comun_select', $ctx->esquemaOld, $ctx->esquemaNew);
                        $acciones[] = sprintf(_('%s: claves alineadas.'), ConfigDB::ficheroIncNombre('comun_select'));
                    } catch (Throwable $e) {
                        $avisos[] = sprintf(_('%s: %s'), ConfigDB::ficheroIncNombre('comun_select'), $e->getMessage());
                    }
                }
            }
        }

        $pdoSv = $this->pdoDesdeImportar($oImportar, 'publicv');
        if ($this->renamePgHecho($pdoSv, $ctx->esquemaOldv, $ctx->esquemaNewv)) {
            if ($this->incDebeRenombrarClave('sv', $ctx->esquemaOldv, $ctx->esquemaNewv)) {
                try {
                    (new ConfigDB('sv'))->renombrarListaEsquema('sv', $ctx->esquemaOldv, $ctx->esquemaNewv);
                    $acciones[] = sprintf(_('%s: claves alineadas.'), ConfigDB::ficheroIncNombre('sv'));
                } catch (Throwable $e) {
                    $avisos[] = sprintf(_('%s: %s'), ConfigDB::ficheroIncNombre('sv'), $e->getMessage());
                }
            }
            try {
                $repo->cambiarNombre($ctx->esquemaOld, $ctx->esquemaNew, 'sv');
                $acciones[] = _('db_idschema (sv): sincronizado.');
            } catch (Throwable $e) {
                $avisos[] = sprintf(_('db_idschema (sv): %s'), $e->getMessage());
            }
        }

        $pdoSve = $this->pdoDesdeImportar($oImportar, 'publicv-e');
        if ($this->renamePgHecho($pdoSve, $ctx->esquemaOldv, $ctx->esquemaNewv)) {
            if ($this->incDebeRenombrarClave('sv-e', $ctx->esquemaOldv, $ctx->esquemaNewv)) {
                try {
                    (new ConfigDB('sv-e'))->renombrarListaEsquema('sv-e', $ctx->esquemaOldv, $ctx->esquemaNewv);
                    $acciones[] = sprintf(_('%s: claves alineadas.'), ConfigDB::ficheroIncNombre('sv-e'));
                } catch (Throwable $e) {
                    $avisos[] = sprintf(_('%s: %s'), ConfigDB::ficheroIncNombre('sv-e'), $e->getMessage());
                }
            }
            try {
                $repo->cambiarNombre($ctx->esquemaOld, $ctx->esquemaNew, 'sv-e');
                $acciones[] = _('db_idschema (sv-e): sincronizado.');
            } catch (Throwable $e) {
                $avisos[] = sprintf(_('db_idschema (sv-e): %s'), $e->getMessage());
            }
        }

        if (!$ctx->isDocker) {
            if ($this->renamePgHecho($this->pdoDesdeImportar($oImportar, 'publicv-e_select'), $ctx->esquemaOldv, $ctx->esquemaNewv)) {
                if ($this->incDebeRenombrarClave('sv-e_select', $ctx->esquemaOldv, $ctx->esquemaNewv)) {
                    try {
                        (new ConfigDB('sv-e'))->renombrarListaEsquema('sv-e_select', $ctx->esquemaOldv, $ctx->esquemaNewv);
                        $acciones[] = sprintf(_('%s: claves alineadas.'), ConfigDB::ficheroIncNombre('sv-e_select'));
                    } catch (Throwable $e) {
                        $avisos[] = sprintf(_('%s: %s'), ConfigDB::ficheroIncNombre('sv-e_select'), $e->getMessage());
                    }
                }
            }
        }

        if ($ctx->sf !== 0) {
            $pdoSf = $this->pdoDesdeImportar($oImportar, 'publicf');
            if ($this->renamePgHecho($pdoSf, $ctx->esquemaOldf, $ctx->esquemaNewf)) {
                if ($this->incDebeRenombrarClave('sf', $ctx->esquemaOldf, $ctx->esquemaNewf)) {
                    try {
                        (new ConfigDB('sf'))->renombrarListaEsquema('sf', $ctx->esquemaOldf, $ctx->esquemaNewf);
                        $acciones[] = sprintf(_('%s: claves alineadas.'), ConfigDB::ficheroIncNombre('sf'));
                    } catch (Throwable $e) {
                        $avisos[] = sprintf(_('%s: %s'), ConfigDB::ficheroIncNombre('sf'), $e->getMessage());
                    }
                }
                try {
                    $repo->cambiarNombre($ctx->esquemaOld, $ctx->esquemaNew, 'sf');
                    $acciones[] = _('db_idschema (sf): sincronizado.');
                } catch (Throwable $e) {
                    $avisos[] = sprintf(_('db_idschema (sf): %s'), $e->getMessage());
                }
            }
        }
    }

    private function renamePgHecho(?PDO $pdo, string $schemaOld, string $schemaNew): bool
    {
        return $pdo !== null
            && !$this->existeEsquema($pdo, $schemaOld)
            && $this->existeEsquema($pdo, $schemaNew);
    }

    private function incDebeRenombrarClave(string $ficheroBase, string $oldKey, string $newKey): bool
    {
        $keys = ConfigDB::clavesEnFicheroRoles($ficheroBase);

        return in_array($oldKey, $keys, true) && !in_array($newKey, $keys, true);
    }

    /**
     * @param list<array{tabla: string, campo: string, valor: string}> $defs
     * @param list<string> $acciones
     */
    private function runSetDefaults(PDO $pdo, string $schema, array $defs, array &$acciones, string $etiqueta): void
    {
        $defs = $this->normalizarDefs($defs);
        $o = new DBAlterSchema();
        $o->setDbConexion($pdo);
        $o->setSchema($schema);
        $o->setDefaults($defs);
        $acciones[] = sprintf(_('Defaults ALTER reaplicados (%s).'), $etiqueta);
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

    private function existeEsquema(PDO $pdo, string $n): bool
    {
        $st = $pdo->prepare('SELECT 1 FROM pg_namespace WHERE nspname = :n LIMIT 1');
        $st->execute(['n' => $n]);

        return (bool) $st->fetchColumn();
    }

    /**
     * @param array<int, mixed> $defs
     * @return list<array{tabla: string, campo: string, valor: string}>
     */
    private function normalizarDefs(array $defs): array
    {
        $normalizados = [];
        foreach ($defs as $def) {
            if (!is_array($def)) {
                continue;
            }
            $tabla = $def['tabla'] ?? null;
            $campo = $def['campo'] ?? null;
            $valor = $def['valor'] ?? null;
            if (!is_string($tabla) || !is_string($campo) || !is_string($valor)) {
                continue;
            }
            $normalizados[] = [
                'tabla' => $tabla,
                'campo' => $campo,
                'valor' => $valor,
            ];
        }

        return $normalizados;
    }
}
