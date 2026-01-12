<?php

namespace src\notas\infrastructure\repositories;

use core\ClaseRepository;
use PDO;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\entity\Nota;
use src\notas\domain\value_objects\NotaSituacion;
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
class ConstantNotaRepository extends ClaseRepository implements NotaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('e_notas_situacion');
    }

    public function getArrayNotasNoSuperadas(): array
    {
        $aNoSuperadas = [
            NotaSituacion::DESCONOCIDO,
            NotaSituacion::CURSADA,
            NotaSituacion::PREVISTA_CA,
            NotaSituacion::PREVISTA_INV,
            NotaSituacion::NO_HECHA_CA,
            NotaSituacion::NO_HECHA_INV,
            NotaSituacion::EXAMINADO,
            NotaSituacion::FALTA_CERTIFICADO,
        ];

        return $aNoSuperadas;
    }

    public function getArrayNotasSuperadas(): array
    {
        $aSuperadas = [
            NotaSituacion::SUPERADA,
            NotaSituacion::MAGNA,
            NotaSituacion::SUMMA,
            NotaSituacion::CONVALIDADA,
            NotaSituacion::NUMERICA,
            NotaSituacion::EXENTO,
        ];

        return $aSuperadas;
    }

    public function getArrayNotas(): array
    {
        $aNotas = NotaSituacion::getArraySituacionTxt();
        return $aNotas;
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Nota $Nota): bool
    {
        $id_situacion = $Nota->getIdSituacionVo()->value();
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
        $id_situacion = $Nota->getIdSituacionVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_situacion);

        $aDatos = $Nota->toArrayForDatabase();
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
            $aDatos['id_situacion'] = $Nota->getIdSituacionVo()->value();
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
        return Nota::fromArray($aDatos);
    }
}