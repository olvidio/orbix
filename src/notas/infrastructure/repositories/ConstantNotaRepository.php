<?php

namespace src\notas\infrastructure\repositories;

use PDO;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\entity\Nota;
use src\shared\traits\HandlesPdoErrors;
use function core\is_true;

/**
 * Clase que adapta la tabla e_notas_situacion a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class zzConstantNotaRepository implements NotaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        Nota::traduccion_init();
    }

    public function getArrayNotasNoSuperadas(): array
    {
        $aNoSuperadas = [
            Nota::DESCONOCIDO,
            Nota::CURSADA,
            Nota::PREVISTA_CA,
            Nota::PREVISTA_INV,
            Nota::NO_HECHA_CA,
            Nota::NO_HECHA_INV,
            Nota::EXAMINADO,
            Nota::FALTA_CERTIFICADO,
        ];

        return $aNoSuperadas;
    }

    public function getArrayNotasSuperadas(): array
    {
        $aSuperadas = [
            Nota::SUPERADA,
            Nota::MAGNA,
            Nota::SUMMA,
            Nota::CONVALIDADA,
            Nota::NUMERICA,
            Nota::EXENTO,
        ];

        return $aSuperadas;
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Nota $Nota): bool
    {
        $id_situacion = $Nota->getId_situacion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_situacion = $id_situacion";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Nota $Nota): bool
    {
        $id_situacion = $Nota->getId_situacion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_situacion);

        $aDatos = [];
        $aDatos['descripcion'] = $Nota->getDescripcion();
        $aDatos['superada'] = $Nota->isSuperada();
        $aDatos['breve'] = $Nota->getBreve();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['superada'])) {
            $aDatos['superada'] = 'true';
        } else {
            $aDatos['superada'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					descripcion              = :descripcion,
					superada                 = :superada,
					breve                    = :breve";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_situacion = $id_situacion";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
            //INSERT
            $aDatos['id_situacion'] = $Nota->getId_situacion();
            $campos = "(id_situacion,descripcion,superada,breve)";
            $valores = "(:id_situacion,:descripcion,:superada,:breve)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_situacion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_situacion = $id_situacion";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_situacion
     * @return array|bool
     */
    public function datosById(int $id_situacion): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_situacion = $id_situacion";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Busca la clase con id_situacion en la base de datos .
     */
    public function findById(int $id_situacion): ?Nota
    {
        $aDatos = $this->datosById($id_situacion);
        if (empty($aDatos)) {
            return null;
        }
        return (new Nota())->setAllAttributes($aDatos);
    }
}