<?php

declare(strict_types=1);

namespace Tests\support;

use PDO;

/**
 * Para tests que trabajan sobre una persona **real** de la BD de desarrollo y vacían sus filas de una
 * tabla antes de crear las de prueba (p. ej. las notas en los tests de traslados).
 *
 * Antes de vaciar, el test llama a {@see self::conservarFilasDePersona()}: la primera vez se guardan
 * las filas reales de esa persona en esa tabla. Al acabar la clase ({@see self::tearDownAfterClass()})
 * se borra lo que haya dejado el test y se reponen las filas guardadas. Sin esto, cada ejecución
 * borraba las 79 notas reales de la persona de pruebas de `H-dlbv` (fase 5 del plan de tests).
 */
trait ConservaFilasDePersona
{
    /** @var array<string, array{pdo: PDO, tabla: string, id_nom: int, filas: list<array<string, mixed>>}> */
    private static array $filasConservadas = [];

    /**
     * @param PDO $pdo conexión con la que el test borra (su `search_path` resuelve `$tabla`)
     * @param string $tabla nombre de la tabla, sin esquema
     */
    protected function conservarFilasDePersona(PDO $pdo, string $tabla, int $idNom): void
    {
        if (preg_match('/^\w+$/', $tabla) !== 1) {
            throw new \InvalidArgumentException("Tabla no válida: $tabla");
        }
        $stmt = $pdo->query('SELECT n.nspname FROM pg_class c JOIN pg_namespace n ON n.oid = c.relnamespace'
            . " WHERE c.oid = to_regclass('" . $tabla . "')");
        $esquema = $stmt === false ? false : $stmt->fetchColumn();
        if (!is_string($esquema) || $esquema === '') {
            return; // la tabla no existe en esta conexión: no hay nada que conservar
        }
        $calificada = '"' . str_replace('"', '""', $esquema) . '".' . $tabla;
        $clave = $calificada . '#' . $idNom;
        if (isset(self::$filasConservadas[$clave])) {
            return;
        }
        $sel = $pdo->prepare("SELECT * FROM ONLY $calificada WHERE id_nom = :id_nom");
        $sel->execute(['id_nom' => $idNom]);
        /** @var list<array<string, mixed>> $filas */
        $filas = $sel->fetchAll(PDO::FETCH_ASSOC);
        self::$filasConservadas[$clave] = ['pdo' => $pdo, 'tabla' => $calificada, 'id_nom' => $idNom, 'filas' => $filas];
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::$filasConservadas as $c) {
            $pdo = $c['pdo'];
            $pdo->prepare("DELETE FROM ONLY {$c['tabla']} WHERE id_nom = :id_nom")->execute(['id_nom' => $c['id_nom']]);
            foreach ($c['filas'] as $fila) {
                $cols = array_keys($fila);
                $sql = "INSERT INTO {$c['tabla']} (" . implode(', ', array_map(static fn (string $k): string => '"' . $k . '"', $cols))
                    . ') VALUES (' . implode(', ', array_map(static fn (string $k): string => ':' . $k, $cols)) . ')';
                $ins = $pdo->prepare($sql);
                foreach ($fila as $col => $valor) {
                    if (is_bool($valor)) {
                        $ins->bindValue(':' . $col, $valor, PDO::PARAM_BOOL);
                    } elseif ($valor === null) {
                        $ins->bindValue(':' . $col, null, PDO::PARAM_NULL);
                    } else {
                        $ins->bindValue(':' . $col, is_scalar($valor) ? (string) $valor : $valor);
                    }
                }
                $ins->execute();
            }
        }
        self::$filasConservadas = [];
        parent::tearDownAfterClass();
    }
}
