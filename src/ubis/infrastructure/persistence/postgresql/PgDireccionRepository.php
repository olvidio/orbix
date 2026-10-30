<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\ubis\domain\criteria\DireccionField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\ConverterDate;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\HandlesPgBytea;
use src\ubis\domain\contracts\DireccionRepositoryInterface;
use src\ubis\domain\entity\Direccion;

/**
 * Clase que adapta la tabla u_dir_ctr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class PgDireccionRepository extends ClaseRepository implements DireccionRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];
    use HandlesPgBytea;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBP');
        $oDbl_Select = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_dir_ctr');
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayPoblaciones(string $sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT DISTINCT initcap(poblacion) AS poblacion, initcap(poblacion) AS poblacion1 FROM $nom_tabla $sCondicion ORDER BY initcap(poblacion)";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
            $poblacion = $row[0];
            $poblacion1 = $row[1];

            $aOpciones[$poblacion] = $poblacion1;
        }

        return $aOpciones;
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayPaises(string $sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT DISTINCT initcap(pais) AS pais, initcap(pais) AS pais1 FROM $nom_tabla $sCondicion ORDER BY initcap(pais)";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
            $pais = $row[0];
            if ($pais === null) {
                continue;
            }
            $pais1 = $row[1];

            $aOpciones[$pais] = $pais1;
        }

        return $aOpciones;
    }
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Direccion que cumplen el criterio.
     *
     * @return list<Direccion>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(DireccionField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $aDatos['plano_doc'] = $this->normalizeBytea($this->readByteaField($aDatos['plano_doc']));
            $aDatos['f_direccion'] = (new ConverterDate('date', $aDatos['f_direccion']))->fromPg();
            $items[] = Direccion::fromArray($aDatos);
        }
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Direccion $Direccion): bool
    {
        $id_direccion = $Direccion->getId_direccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_direccion = $id_direccion";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Direccion $Direccion): bool
    {
        $id_direccion = $Direccion->getId_direccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_direccion);

        $aDatos = $Direccion->toArrayForDatabase([
            'plano_doc' => fn($v) => ($v ? ('\\x' . bin2hex($v)) : null),
            'f_direccion' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_direccion']);
            $update = "
                    direccion                = :direccion,
                    c_p                      = :c_p,
                    poblacion                = :poblacion,
                    provincia                = :provincia,
                    a_p                      = :a_p,
                    pais                     = :pais,
                    f_direccion              = :f_direccion,
                    observ                   = :observ,
                    cp_dcha                  = :cp_dcha,
                    latitud                  = :latitud,
                    longitud                 = :longitud,
                    plano_doc                = :plano_doc,
                    plano_extension          = :plano_extension,
                    plano_nom                = :plano_nom,
                    nom_sede                 = :nom_sede";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_direccion = $id_direccion";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_direccion,direccion,c_p,poblacion,provincia,a_p,pais,f_direccion,observ,cp_dcha,latitud,longitud,plano_doc,plano_extension,plano_nom,nom_sede)";
            $valores = "(:id_direccion,:direccion,:c_p,:poblacion,:provincia,:a_p,:pais,:f_direccion,:observ,:cp_dcha,:latitud,:longitud,:plano_doc,:plano_extension,:plano_nom,:nom_sede)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_direccion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_direccion = $id_direccion";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
     * @param int $id_direccion
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_direccion): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_direccion = $id_direccion";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }

        // para los bytea: (resources)
        $planoDoc = $aDatos['plano_doc'] ?? null;
        $aDatos['plano_doc'] = $this->normalizeBytea($this->readByteaField($planoDoc));

        // para las fechas del postgres (texto iso)
        $fDireccion = $aDatos['f_direccion'] ?? null;
        $aDatos['f_direccion'] = (new ConverterDate('date', $fDireccion))->fromPg();

        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }


    /**
     * Busca la clase con id_direccion en la base de datos .
     */
    public function findById(int $id_direccion): ?Direccion
    {
        $aDatos = $this->datosById($id_direccion);
        if ($aDatos === false) {
            return null;
        }
        return Direccion::fromArray($aDatos);
    }
}