<?php

namespace src\shared\traits;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Trait para unificar manejo de errores PDO en repositorios.
 *
 * - Registra el error con GestorErrores SIN lanzar desde el logger.
 * - Setea ErrorTxt en la clase que use el trait.
 * - Decide si lanzar excepción (por defecto) o devolver false mediante $throwOnError.
 */
trait HandlesPdoErrors
{
    // Requiere que la clase que use el trait tenga un método setErrorTxt(string): self
    // (provisto por core\ClaseRepository como método protegido). No se declara abstract para
    // mantener compatibilidad de visibilidad y tipo de retorno.

    /**
     * Si es true, el trait lanza RuntimeException al fallar; si es false, devuelve false.
     */
    protected bool $throwOnError = true;

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
            $_SESSION['oGestorErrores']->addErrorAppLastErrorNoThrowText($errorText, $errorKey . '.prepare', (string)$line, $file);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText, 0, $e);
            }
            return false;
        }

        if ($stmt === false) {
            $errorText = 'PDO prepare error';
            $_SESSION['oGestorErrores']->addErrorAppLastErrorNoThrowText($errorText, $errorKey . '.prepare', (string)$line, $file);
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
     */
    protected function pdoExecute(PDOStatement $stmt, array $params, string $errorKey, string $file, int $line): bool
    {
        try {
            $ok = $stmt->execute($params);
        } catch (PDOException $e) {
            $errorText = $e->errorInfo[2] ?? $e->getMessage();
            $_SESSION['oGestorErrores']->addErrorAppLastErrorNoThrowText($errorText, $errorKey . '.execute', (string)$line, $file);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText, 0, $e);
            }
            return false;
        }

        if ($ok === false) {
            $info = $stmt->errorInfo();
            $errorText = $info[2] ?? 'PDO execute error';
            $_SESSION['oGestorErrores']->addErrorAppLastErrorNoThrowText($errorText, $errorKey . '.execute', (string)$line, $file);
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
            $_SESSION['oGestorErrores']->addErrorAppLastErrorNoThrowText($errorText, $errorKey . '.exec', (string)$line, $file);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText, 0, $e);
            }
            return false;
        }

        if ($rows === false) {
            $errorText = 'PDO exec error';
            $_SESSION['oGestorErrores']->addErrorAppLastErrorNoThrowText($errorText, $errorKey . '.exec', (string)$line, $file);
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
            $_SESSION['oGestorErrores']->addErrorAppLastErrorNoThrowText($errorText, $errorKey . '.query', (string)$line, $file);
            $this->setErrorTxt($errorText);
            if ($this->throwOnError) {
                throw new \RuntimeException($errorText, 0, $e);
            }
            return false;
        }

        if ($stmt === false) {
            $errorText = 'PDO query error';
            $_SESSION['oGestorErrores']->addErrorAppLastErrorNoThrowText($errorText, $errorKey . '.query', (string)$line, $file);
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
