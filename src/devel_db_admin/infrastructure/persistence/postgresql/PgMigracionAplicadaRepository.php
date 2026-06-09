<?php

declare(strict_types=1);

namespace src\devel_db_admin\infrastructure\persistence\postgresql;

use PDO;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\traits\HandlesPdoErrors;

final class PgMigracionAplicadaRepository extends ClaseRepository implements MigracionAplicadaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBPC'));
        // La tabla de control se crea en la BD principal y se consulta inmediatamente
        // tras escribir; por eso las lecturas usan la misma conexion de escritura.
        $this->setoDbl_select(GlobalPdo::get('oDBPC'));
        $this->setNomTabla('migracion_aplicada');
    }

    public function ensureTabla(): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS migracion_aplicada (
    id          SERIAL PRIMARY KEY,
    prefijo     VARCHAR(20)  NOT NULL,
    descripcion VARCHAR(200) NOT NULL,
    database    VARCHAR(20)  NOT NULL,
    tipo        VARCHAR(15)  NOT NULL,
    sha1        VARCHAR(40)  NOT NULL,
    aplicada_en TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario     VARCHAR(100),
    ok          BOOLEAN      NOT NULL DEFAULT TRUE,
    mensaje     TEXT,
    UNIQUE (prefijo, descripcion, database)
)
SQL;
        $this->pdoExec($this->getoDbl(), $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * @return array<int, MigracionAplicada>
     */
    public function aplicadas(): array
    {
        $this->ensureTabla();

        $sql = "SELECT * FROM {$this->getNomTabla()} ORDER BY prefijo, descripcion, database";
        $stmt = $this->pdoQuery($this->getoDbl_Select(), $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $migraciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $migraciones[] = MigracionAplicada::fromArray($row);
        }

        return $migraciones;
    }

    public function findByKey(string $prefijo, string $descripcion, string $database): ?MigracionAplicada
    {
        $this->ensureTabla();

        $sql = "SELECT * FROM {$this->getNomTabla()}
                WHERE prefijo = :prefijo
                  AND descripcion = :descripcion
                  AND database = :database";
        $stmt = $this->prepareAndExecute($this->getoDbl_Select(), $sql, [
            'prefijo' => $prefijo,
            'descripcion' => $descripcion,
            'database' => $database,
        ], __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row) || $row === []) {
            return null;
        }
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[(string) $key] = $value;
        }

        return MigracionAplicada::fromArray($normalized);
    }

    public function existe(string $prefijo, string $descripcion, string $database): bool
    {
        $migracion = $this->findByKey($prefijo, $descripcion, $database);

        return $migracion !== null && $migracion->isOk();
    }

    public function registrar(MigracionAplicada $migracion): bool
    {
        $this->ensureTabla();

        $sql = "INSERT INTO {$this->getNomTabla()}
                    (prefijo, descripcion, database, tipo, sha1, aplicada_en, usuario, ok, mensaje)
                VALUES
                    (:prefijo, :descripcion, :database, :tipo, :sha1, COALESCE(CAST(:aplicada_en AS timestamptz), CURRENT_TIMESTAMP), :usuario, :ok, :mensaje)
                ON CONFLICT (prefijo, descripcion, database)
                DO UPDATE SET
                    tipo = EXCLUDED.tipo,
                    sha1 = EXCLUDED.sha1,
                    aplicada_en = CURRENT_TIMESTAMP,
                    usuario = EXCLUDED.usuario,
                    ok = EXCLUDED.ok,
                    mensaje = EXCLUDED.mensaje";
        $stmt = $this->pdoPrepare($this->getoDbl(), $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        return $this->pdoExecute($stmt, [
            'prefijo' => $migracion->getPrefijo(),
            'descripcion' => $migracion->getDescripcion(),
            'database' => $migracion->getDatabase(),
            'tipo' => $migracion->getTipo(),
            'sha1' => $migracion->getSha1(),
            'aplicada_en' => $migracion->getAplicada_en(),
            'usuario' => $migracion->getUsuario(),
            'ok' => $migracion->isOk() ? 'true' : 'false',
            'mensaje' => $migracion->getMensaje(),
        ], __METHOD__, __FILE__, __LINE__);
    }

    public function Eliminar(MigracionAplicada $migracion): bool
    {
        $sql = "DELETE FROM {$this->getNomTabla()}
                WHERE prefijo = :prefijo
                  AND descripcion = :descripcion
                  AND database = :database";
        $stmt = $this->pdoPrepare($this->getoDbl(), $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        return $this->pdoExecute($stmt, [
            'prefijo' => $migracion->getPrefijo(),
            'descripcion' => $migracion->getDescripcion(),
            'database' => $migracion->getDatabase(),
        ], __METHOD__, __FILE__, __LINE__);
    }
}
