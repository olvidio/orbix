<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use PDO;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use Throwable;

/**
 * Comprueba en las 4 BDs principales (comun, sv, sv-e, sf) que el esquema destino no exista ya
 * antes de {@see CrearEsquema}. Los roles del paso «crear usuarios» son esperables y no bloquean.
 */
final class ComprobarPrecondicionesCrearEsquema
{
    /**
     * @throws \RuntimeException si el esquema destino ya existe o hay un intento anterior a medias
     */
    public function asegurarDestinoLibre(
        string $region,
        string $dl,
        string $esquemaRefBase,
        int $comun,
        int $sv,
        int $sf,
    ): void {
        $esquema = "$region-$dl";
        $esquemav = $esquema . 'v';
        $esquemaf = $esquema . 'f';

        $refComun = $esquemaRefBase;
        $refSv = $esquemaRefBase . 'v';
        $refSf = $esquemaRefBase . 'f';

        $importar = new ConfigDB('importar');
        $conflictos = [];
        $faltaReferencia = [];

        $this->revisarBd(
            $importar,
            _('comun'),
            'public',
            $esquema,
            $refComun,
            $comun !== 0,
            $conflictos,
            $faltaReferencia,
        );
        $this->revisarBd(
            $importar,
            _('sv'),
            'publicv',
            $esquemav,
            $refSv,
            $sv !== 0,
            $conflictos,
            $faltaReferencia,
        );
        $this->revisarBd(
            $importar,
            _('sv-e'),
            'publicv-e',
            $esquemav,
            $refSv,
            $sv !== 0,
            $conflictos,
            $faltaReferencia,
        );
        $this->revisarBd(
            $importar,
            _('sf'),
            'publicf',
            $esquemaf,
            $refSf,
            $sf !== 0,
            $conflictos,
            $faltaReferencia,
        );

        if ($conflictos !== []) {
            throw new CrearEsquemaPrecondicionException($this->mensajeConflictos($esquema, $conflictos));
        }

        if ($faltaReferencia !== []) {
            throw new CrearEsquemaPrecondicionException($this->mensajeFaltaReferencia($esquemaRefBase, $faltaReferencia));
        }

        $this->asegurarRolesCreados($esquema, $esquemav, $esquemaf, $comun, $sv, $sf, $importar);
    }

    /**
     * Los roles del paso «1º crear usuarios» deben existir antes del paso 2 (CREATE SCHEMA … AUTHORIZATION).
     *
     * @throws CrearEsquemaPrecondicionException
     */
    public function asegurarRolesCreados(
        string $esquema,
        string $esquemav,
        string $esquemaf,
        int $comun,
        int $sv,
        int $sf,
        ?ConfigDB $importar = null,
    ): void {
        $importar ??= new ConfigDB('importar');
        $pdo = $this->pdoDesdeImportar($importar, 'public');
        if ($pdo === null) {
            throw new CrearEsquemaPrecondicionException(
                _('Aviso: no se pudo comprobar los roles (conexión a comun). Ejecute primero el paso «1º crear usuarios».'),
            );
        }

        $faltan = [];
        if ($comun !== 0 && !$this->existeRol($pdo, $esquema)) {
            $faltan[] = sprintf('• %s: rol «%s»', _('comun'), $esquema);
        }
        if ($sv !== 0 && !$this->existeRol($pdo, $esquemav)) {
            $faltan[] = sprintf('• %s: rol «%s» (%s / %s)', _('sv'), $esquemav, _('sv'), _('sv-e'));
        }
        if ($sf !== 0 && !$this->existeRol($pdo, $esquemaf)) {
            $faltan[] = sprintf('• %s: rol «%s»', _('sf'), $esquemaf);
        }

        if ($faltan !== []) {
            throw new CrearEsquemaPrecondicionException($this->mensajeFaltanRoles($esquema, $faltan));
        }
    }

    /**
     * @param list<string> $conflictos
     * @param list<string> $faltaReferencia
     */
    private function revisarBd(
        ConfigDB $importar,
        string $etiqueta,
        string $claveImportar,
        string $esquemaDestino,
        string $esquemaReferencia,
        bool $bloqueMarcado,
        array &$conflictos,
        array &$faltaReferencia,
    ): void {
        $pdo = $this->pdoDesdeImportar($importar, $claveImportar);
        if ($pdo === null) {
            $conflictos[] = sprintf('• %s: no se pudo conectar (%s).', $etiqueta, $claveImportar);

            return;
        }

        if (!$this->existeEsquema($pdo, $esquemaDestino)) {
            // Rol sin esquema: normal tras el paso 1 («crear usuarios»).
        } else {
            $tieneRol = $this->existeRol($pdo, $esquemaDestino);
            $numTablas = $this->contarTablas($pdo, $esquemaDestino);
            $conflictos[] = sprintf(
                '• %s: esquema «%s» ya existe (%s); rol «%s» %s.',
                $etiqueta,
                $esquemaDestino,
                $this->describirEstadoEsquema($tieneRol, $numTablas),
                $esquemaDestino,
                $tieneRol ? _('sí') : _('no'),
            );
        }

        if ($bloqueMarcado && !$this->existeEsquema($pdo, $esquemaReferencia)) {
            $faltaReferencia[] = sprintf('• %s: falta el esquema de referencia «%s».', $etiqueta, $esquemaReferencia);
        }
    }

    private function describirEstadoEsquema(bool $tieneRol, int $numTablas): string
    {
        if ($numTablas > 0) {
            return sprintf(_('%d tablas; parece creado por completo'), $numTablas);
        }
        if (!$tieneRol) {
            return _('vacío, sin rol asociado; a medias');
        }

        return _('vacío; a medias (reintento tras fallo del paso 2)');
    }

    /**
     * @param list<string> $lineas
     */
    private function mensajeConflictos(string $esquemaBase, array $lineas): string
    {
        return sprintf(
            _('No se puede crear «%1$s»: el esquema destino ya existe en alguna base (intento anterior del paso 2 o alta duplicada). Los roles del paso «crear usuarios» no impiden continuar.%2$s%2$s%3$s%2$s%2$sUse «Eliminar esquema» con la misma región/dl y las mismas bases marcadas para borrar esquemas (y roles) antes de volver a crear.'),
            $esquemaBase,
            "\n",
            implode("\n", $lineas),
        );
    }

    /**
     * @param list<string> $lineas
     */
    private function mensajeFaltaReferencia(string $esquemaRefBase, array $lineas): string
    {
        return sprintf(
            _('No se puede crear: falta el esquema de referencia «%1$s» en:%2$s%2$s%3$s'),
            $esquemaRefBase,
            "\n",
            implode("\n", $lineas),
        );
    }

    /**
     * @param list<string> $lineas
     */
    private function mensajeFaltanRoles(string $esquemaBase, array $lineas): string
    {
        return sprintf(
            _('Aviso: no se puede crear la estructura de esquemas para «%1$s». Primero ejecute el paso «1º crear usuarios» (misma región y delegación) y, si hace falta, copie las entradas en los ficheros .inc que indica ese paso.%2$s%2$sRoles que faltan en PostgreSQL:%2$s%3$s'),
            $esquemaBase,
            "\n",
            implode("\n", $lineas),
        );
    }

    private function pdoDesdeImportar(ConfigDB $importar, string $clave): ?PDO
    {
        try {
            return (new DBConnection($importar->getEsquema($clave)))->getPDO();
        } catch (Throwable) {
            return null;
        }
    }

    private function existeEsquema(PDO $pdo, string $nombre): bool
    {
        if ($nombre === '') {
            return false;
        }
        $st = $pdo->prepare('SELECT 1 FROM pg_namespace WHERE nspname = :n LIMIT 1');
        $st->execute(['n' => $nombre]);

        return (bool) $st->fetchColumn();
    }

    private function existeRol(PDO $pdo, string $rol): bool
    {
        if ($rol === '') {
            return false;
        }
        $st = $pdo->prepare('SELECT 1 FROM pg_roles WHERE rolname = :r LIMIT 1');
        $st->execute(['r' => $rol]);

        return (bool) $st->fetchColumn();
    }

    private function contarTablas(PDO $pdo, string $esquema): int
    {
        $st = $pdo->prepare(
            "SELECT COUNT(*) FROM pg_tables WHERE schemaname = :s AND tablename NOT LIKE 'pg\\_%'",
        );
        $st->execute(['s' => $esquema]);
        $n = $st->fetchColumn();

        return $n === false ? 0 : (int) $n;
    }
}
