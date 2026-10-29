<?php

namespace src\encargossacd\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\encargossacd\domain\criteria\EncargoTipoField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use PDO;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTipo;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla encargo_tipo a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class PgEncargoTipoRepository extends ClaseRepository implements EncargoTipoRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('encargo_tipo');
    }

    /**
     * Devuelve el número del tipo de encargo para hacer una selección SQL.
     *
     *     En función de los parámetros que se le pasan:
     *        $grupo        ctr,cgi,igl,otros,personales
     *        $nom tipo    (el encargo en concreto)
     *    Si un parámetro se omite, se pone un punto (.) para que la búsqueda sea cualquier número
     *    ejemplo: 12....
     */
    public function id_tipo_encargo(int|string $grupo, string $nom_tipo): string
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $condta1 = '.';
        $condta2 = '.';
        $condta3 = '..';
        $condta = $condta1 . $condta2 . $condta3;

        if ($nom_tipo !== '' && $nom_tipo !== '...') {
            $condicion = "id_tipo_enc::text ~ '" . $condta . "'";
            $query = "SELECT * FROM $nom_tabla where $condicion";
            $stmt = $this->pdoQuery($oDbl, $query, __METHOD__, __FILE__, __LINE__);
            if ($stmt === false) {
                return $condta;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (is_array($row) && isset($row['id_tipo_enc']) && is_scalar($row['id_tipo_enc'])) {
                $condta = (string) $row['id_tipo_enc'];
            }
        }

        return $condta;
    }

    /**
     * Devuelve los parámetros de un encargo en función del tipo de encargo.
     *
     * Es la función inversa de "id_tipo_encargo()".
     * Se le pasa el id_tipo_encargo, y devuelve un array ($tipo) con los siguientes valores:
     *
     *        grupo        ctr,cgi,igl,otros,personales
     *        nom_tipo    (el encargo en concreto)
     *
     * @author    Daniel Serrabou
     * @since        28/2/06.
     *
     */
    /**
     * @return array<string, mixed>
     */
    public function encargo_de_tipo(int|string $id_tipo_enc): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $t_grupo = EncargoTipo::GRUPO;

        $id_tipo_enc = (string) $id_tipo_enc;
        $ta1 = substr($id_tipo_enc, 0, 1);
        $ta2 = substr($id_tipo_enc, 1, 3);

        if ($ta1 === ".") {
            ksort($t_grupo);
            $grupo = $t_grupo;
        } else {
            $grupo = $t_grupo[$ta1];
        }

        $query = "SELECT * FROM $nom_tabla where id_tipo_enc::text ~ '^" . $id_tipo_enc . "' order by tipo_enc";
        $stmt = $this->pdoQuery($oDbl, $query, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return ['grupo' => $grupo, 'nom_tipo' => []];
        }

        $nom_tipo = [];
        if ($ta2 === "...") {
            $i = 0;
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $idKey = isset($row['id_tipo_enc']) && is_scalar($row['id_tipo_enc']) ? (string) $row['id_tipo_enc'] : '';
                $tipoVal = isset($row['tipo_enc']) && is_scalar($row['tipo_enc']) ? (string) $row['tipo_enc'] : '';
                if ($idKey !== '') {
                    $nom_tipo[$idKey] = $tipoVal;
                }
                $i++;
            }
        } else {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $nom_tipo = is_array($row) && isset($row['tipo_enc']) && is_scalar($row['tipo_enc'])
                ? (string) $row['tipo_enc']
                : '';
        }

        $tipo = array(
            "grupo" => $grupo,
            "nom_tipo" => $nom_tipo
        );

        return $tipo;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * EncargoTipo que cumplen el criterio.
     *
     * @return list<EncargoTipo>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(EncargoTipoField::class, self::COLUMNAS);
        return array_map(EncargoTipo::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<EncargoTipo>
     */
    public function getEncargoTipos(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(EncargoTipoField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoTipo $EncargoTipo): bool
    {
        $id_tipo_enc = $EncargoTipo->getId_tipo_enc();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tipo_enc = $id_tipo_enc";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(EncargoTipo $EncargoTipo): bool
    {
        $id_tipo_enc = $EncargoTipo->getId_tipo_enc();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_enc);

        $aDatos = $EncargoTipo->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tipo_enc']);
            $update = "
					tipo_enc                 = :tipo_enc,
					mod_horario              = :mod_horario";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tipo_enc = $id_tipo_enc";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_tipo_enc,tipo_enc,mod_horario)";
            $valores = "(:id_tipo_enc,:tipo_enc,:mod_horario)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_enc): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_enc = $id_tipo_enc";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_tipo_enc
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_enc): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_enc = $id_tipo_enc";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }


    /**
     * Busca la clase con id_tipo_enc en la base de datos .
     */
    public function findById(int $id_tipo_enc): ?EncargoTipo
    {
        $aDatos = $this->datosById($id_tipo_enc);
        if ($aDatos === false) {
            return null;
        }
        return EncargoTipo::fromArray($aDatos);
    }
}