<?php

declare(strict_types=1);

namespace src\menus\infrastructure\persistence\postgresql;

use PDO;
use PDOException;

/**
 * Sustituye los menús de la aplicación (`aux_grupmenu`, `aux_grupmenu_rol`, `aux_menus`, en sv-e) por los de una
 * plantilla (`ref_grupmenu`, `ref_grupmenu_rol`, `ref_menus`, en comun).
 *
 * Lee la plantilla antes de tocar nada: si no tiene menús, avisa y no cambia nada. El vaciado y la copia van en una
 * transacción (o en un punto de guardado, si la conexión ya está en una): si algo falla, quedan los menús de antes.
 */
final class PgMenusPlantillaImportador
{
    /** Tablas en orden de copia: destino => [origen, columnas, secuencia, columna de la secuencia]. */
    private const TABLAS = [
        'aux_grupmenu' => ['ref_grupmenu', ['id_grupmenu', 'grup_menu', 'orden'], 'aux_grupmenu_id_grupmenu_seq', 'id_grupmenu'],
        'aux_grupmenu_rol' => ['ref_grupmenu_rol', ['id_item', 'id_grupmenu', 'id_role'], 'aux_grupmenu_rol_id_item_seq', 'id_item'],
        'aux_menus' => ['ref_menus', ['id_menu', 'orden', 'menu', 'parametros', 'id_metamenu', 'menu_perm', 'id_grupmenu', 'ok'], 'aux_menus_id_menu_seq', 'id_menu'],
    ];

    private const SAVEPOINT = 'menus_importar';

    public function __construct(
        private PDO $oDB,
        private PDO $oDBPC,
    ) {
    }

    /**
     * @return string texto de error; vacío si se ha importado.
     */
    public function importar(int $idTemplate): string
    {
        try {
            $filas = [];
            foreach (self::TABLAS as $destino => [$origen, $columnas]) {
                $filas[$destino] = $this->leerPlantilla($origen, $columnas, $idTemplate);
            }
        } catch (PDOException $e) {
            return _("No se ha podido leer la plantilla de menús: no se ha cambiado nada.") . "\n" . $e->getMessage();
        }
        if ($filas['aux_menus'] === []) {
            return _("La plantilla de menús no tiene menús: no se ha cambiado nada.");
        }

        $enTransaccionPrevia = $this->oDB->inTransaction();
        $modoErrores = $this->oDB->getAttribute(PDO::ATTR_ERRMODE);
        $this->oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $this->empezar($enTransaccionPrevia);
            try {
                foreach (self::TABLAS as $destino => [, $columnas, $secuencia, $columnaSecuencia]) {
                    $this->oDB->exec("TRUNCATE TABLE $destino RESTART IDENTITY CASCADE");
                    $this->insertar($destino, $columnas, $filas[$destino]);
                    $this->oDB->query("SELECT setval('$secuencia', (SELECT MAX($columnaSecuencia) FROM $destino))");
                }
                $this->terminar($enTransaccionPrevia);
            } catch (PDOException $e) {
                $this->deshacer($enTransaccionPrevia);

                return _("Hay un error, no se han importado los menús: siguen los de antes.") . "\n" . $e->getMessage();
            }
        } finally {
            $this->oDB->setAttribute(PDO::ATTR_ERRMODE, $modoErrores);
        }

        return '';
    }

    /**
     * @param list<string> $columnas
     * @return list<array<string, mixed>>
     */
    private function leerPlantilla(string $origen, array $columnas, int $idTemplate): array
    {
        $st = $this->oDBPC->prepare('SELECT ' . implode(',', $columnas) . " FROM $origen WHERE id_template_menu = :id_template_menu");
        if ($st === false || !$st->execute(['id_template_menu' => $idTemplate])) {
            throw new PDOException("No se puede leer $origen");
        }

        return array_values(array_filter($st->fetchAll(PDO::FETCH_ASSOC), 'is_array'));
    }

    /**
     * @param list<string> $columnas
     * @param list<array<string, mixed>> $filas
     */
    private function insertar(string $destino, array $columnas, array $filas): void
    {
        if ($filas === []) {
            return;
        }
        $marcas = implode(',', array_map(static fn(string $c): string => ":$c", $columnas));
        $st = $this->oDB->prepare("INSERT INTO $destino (" . implode(',', $columnas) . ") VALUES ($marcas)");
        foreach ($filas as $fila) {
            $st->execute($fila);
        }
    }

    private function empezar(bool $enTransaccionPrevia): void
    {
        if ($enTransaccionPrevia) {
            $this->oDB->exec('SAVEPOINT ' . self::SAVEPOINT);
        } else {
            $this->oDB->beginTransaction();
        }
    }

    private function terminar(bool $enTransaccionPrevia): void
    {
        if ($enTransaccionPrevia) {
            $this->oDB->exec('RELEASE SAVEPOINT ' . self::SAVEPOINT);
        } else {
            $this->oDB->commit();
        }
    }

    private function deshacer(bool $enTransaccionPrevia): void
    {
        try {
            if ($enTransaccionPrevia) {
                $this->oDB->exec('ROLLBACK TO SAVEPOINT ' . self::SAVEPOINT);
            } else {
                $this->oDB->rollBack();
            }
        } catch (PDOException) {
            // la conexión ya no tiene la transacción: no hay nada que deshacer
        }
    }
}
