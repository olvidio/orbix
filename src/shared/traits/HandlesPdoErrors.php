<?php

namespace src\shared\traits;

use PDO;
use PDOException;
use PDOStatement;
use src\shared\infrastructure\logging\GestorErrores;

/**
 * Trait para unificar manejo de errores PDO en repositorios.
 *
 * - Registra el error con GestorErrores SIN lanzar desde el logger.
 * - Setea ErrorTxt en la clase que use el trait.
 * - Decide si lanzar excepción (por defecto) o devolver false mediante $throwOnError.
 */
trait HandlesPdoErrors
{
    /**
     * Lo implementa ClaseRepository o StoresPdoErrorTxt.
     */
    abstract protected function setErrorTxt(string $sErrorTxt): object;

    /**
     * Si es true, el trait lanza RuntimeException al fallar; si es false, devuelve false.
     */
    protected bool $throwOnError = true;

    private function gestorErrores(): ?GestorErrores
    {
        $gestor = $_SESSION['oGestorErrores'] ?? null;

        return $gestor instanceof GestorErrores ? $gestor : null;
    }

    private function logPdoErrorText(string $errorText, string $errorKey, string $file, int $line): void
    {
        $gestor = $this->gestorErrores();
        if ($gestor !== null) {
            $gestor->addErrorAppLastErrorNoThrowText($errorText, $errorKey, (string) $line, $file);
        }
    }

    /**
     * Envuelve PDO::prepare con logging unificado.
     * @return PDOStatement|false
     */
    protected function pdoPrepare(PDO $db, string $sql, string $errorKey, string $file, int $line)
    {
        try {
            $stmt = $db->prepare($sql);
        } catch (PDOException $e) {
            $errorText = $e->errorInfo[2] ?? $e->getMessage();
            $this->logPdoErrorText($errorText, $errorKey . '.prepare', $file, $line);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText, 0, $e);
            }
            return false;
        }

        if ($stmt === false) {
            $errorText = 'PDO prepare error';
            $this->logPdoErrorText($errorText, $errorKey . '.prepare', $file, $line);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText);
            }
            return false;
        }
        return $stmt;
    }

    /**
     * Ejecuta un statement con try/catch, setea ErrorTxt y registra en GestorErrores si falla.
     *
     * @param array<int|string, mixed> $params
     */
    protected function pdoExecute(PDOStatement $stmt, array $params, string $errorKey, string $file, int $line): bool
    {
        try {
            $ok = $stmt->execute($params);
        } catch (PDOException $e) {
            $errorText = $e->errorInfo[2] ?? $e->getMessage();
            $this->logPdoErrorText($errorText, $errorKey . '.execute', $file, $line);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText, 0, $e);
            }
            return false;
        }

        if ($ok === false) {
            $info = $stmt->errorInfo();
            $errorText = $info[2] ?? 'PDO execute error';
            $this->logPdoErrorText($errorText, $errorKey . '.execute', $file, $line);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText);
            }
            return false;
        }
        return true;
    }

    /**
     * Envuelve PDO::exec para operaciones directas (DELETE/DDL). Captura error tanto por false como por excepción.
     */
    protected function pdoExec(PDO $db, string $sql, string $errorKey, string $file, int $line): bool
    {
        try {
            $rows = $db->exec($sql);
        } catch (PDOException $e) {
            $errorText = $e->errorInfo[2] ?? $e->getMessage();
            $this->logPdoErrorText($errorText, $errorKey . '.exec', $file, $line);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText, 0, $e);
            }
            return false;
        }

        if ($rows === false) {
            $errorText = 'PDO exec error';
            $this->logPdoErrorText($errorText, $errorKey . '.exec', $file, $line);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText);
            }
            return false;
        }
        return true;
    }

    /**
     * Envuelve PDO::query para consultas directas (SELECT sin parámetros). Devuelve el statement o false.
     */
    protected function pdoQuery(PDO $db, string $sql, string $errorKey, string $file, int $line): PDOStatement|false
    {
        try {
            $stmt = $db->query($sql);
        } catch (PDOException $e) {
            $errorText = $e->errorInfo[2] ?? $e->getMessage();
            $this->logPdoErrorText($errorText, $errorKey . '.query', $file, $line);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText, 0, $e);
            }
            return false;
        }

        if ($stmt === false) {
            $errorText = 'PDO query error';
            $this->logPdoErrorText($errorText, $errorKey . '.query', $file, $line);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText);
            }
            return false;
        }
        return $stmt;
    }

    /**
     * Atajo: prepara y ejecuta en una llamada, devolviendo el statement listo o false.
     *
     * @param array<int|string, mixed> $params
     */
    protected function prepareAndExecute(PDO $db, string $sql, array $params, string $errorKey, string $file, int $line): PDOStatement|false
    {
        $stmt = $this->pdoPrepare($db, $sql, $errorKey, $file, $line);
        if ($stmt === false) {
            return false;
        }
        if (!$this->pdoExecute($stmt, $params, $errorKey, $file, $line)) {
            return false;
        }
        return $stmt;
    }
}
