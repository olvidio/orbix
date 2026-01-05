<?php

namespace src\asistentes\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\shared\traits\HandlesPdoErrors;
use function core\is_true;


/**
 * Clase que adapta la tabla d_asistentes_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgAsistenteRepository extends ClaseRepository implements AsistenteRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_asistentes_dl');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Asistente
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Asistente
     */
    public function getAsistentes(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $AsistenteSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Asistente = Asistente::fromArray($aDatos);
            $AsistenteSet->add($Asistente);
        }
        return $AsistenteSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Asistente $Asistente): bool
    {
        $id_activ = $Asistente->getId_activ();
        $id_nom = $Asistente->getId_nom();

        // Obtener datos actuales antes de eliminar
        $datosActuales = $this->datosById($id_activ, $id_nom);

        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ AND id_nom = $id_nom";
        $success = $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        if ($success && $datosActuales) {
            // Marcar como eliminada (los eventos se despacharán por el UnitOfWork)
            $Asistente->marcarComoEliminada($datosActuales);
        }

        return $success;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Asistente $Asistente): bool
    {
        $id_activ = $Asistente->getId_activ();
        $id_nom = $Asistente->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ, $id_nom);

        // Obtener datos actuales si es UPDATE
        $datosActuales = $bInsert ? [] : ($this->datosById($id_activ, $id_nom) ?: []);

        $aDatos = [];
        $aDatos['propio'] = $Asistente->isPropio();
        $aDatos['est_ok'] = $Asistente->isEst_ok();
        $aDatos['cfi'] = $Asistente->isCfi();
        $aDatos['cfi_con'] = $Asistente->getCfi_con();
        $aDatos['falta'] = $Asistente->isFalta();
        $aDatos['encargo'] = $Asistente->getEncargo();
        $aDatos['dl_responsable'] = $Asistente->getDl_responsable();
        $aDatos['observ'] = $Asistente->getObserv();
        $aDatos['id_tabla'] = $Asistente->getId_tabla();
        $aDatos['plaza'] = $Asistente->getPlaza();
        $aDatos['propietario'] = $Asistente->getPropietario();
        $aDatos['observ_est'] = $Asistente->getObserv_est();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['propio'])) {
            $aDatos['propio'] = 'true';
        } else {
            $aDatos['propio'] = 'false';
        }
        if (is_true($aDatos['est_ok'])) {
            $aDatos['est_ok'] = 'true';
        } else {
            $aDatos['est_ok'] = 'false';
        }
        if (is_true($aDatos['cfi'])) {
            $aDatos['cfi'] = 'true';
        } else {
            $aDatos['cfi'] = 'false';
        }
        if (is_true($aDatos['falta'])) {
            $aDatos['falta'] = 'true';
        } else {
            $aDatos['falta'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					propio                   = :propio,
					est_ok                   = :est_ok,
					cfi                      = :cfi,
					cfi_con                  = :cfi_con,
					falta                    = :falta,
					encargo                  = :encargo,
					dl_responsable           = :dl_responsable,
					observ                   = :observ,
					id_tabla                 = :id_tabla,
					plaza                    = :plaza,
					propietario              = :propietario,
					observ_est               = :observ_est";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ AND id_nom = $id_nom";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $aDatos['id_activ'] = $Asistente->getId_activ();
            $aDatos['id_nom'] = $Asistente->getId_nom();
            $campos = "(id_activ,id_nom,propio,est_ok,cfi,cfi_con,falta,encargo,dl_responsable,observ,id_tabla,plaza,propietario,observ_est)";
            $valores = "(:id_activ,:id_nom,:propio,:est_ok,:cfi,:cfi_con,:falta,:encargo,:dl_responsable,:observ,:id_tabla,:plaza,:propietario,:observ_est)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        $success = $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);

        if ($success) {
            // Marcar evento de dominio (se despachará por el UnitOfWork)
            if ($bInsert) {
                $Asistente->marcarComoNueva($datosActuales);
            } else {
                $Asistente->marcarComoModificada($datosActuales);
            }
        }

        return $success;
    }

    private function isNew(int $id_activ, int $id_nom): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ AND id_nom = $id_nom";
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
     * @param int $id_activ
     * @return array|bool
     */
    public function datosById(int $id_activ, int $id_nom): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ AND id_nom = $id_nom";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_activ, int $id_nom): ?Asistente
    {
        $aDatos = $this->datosById($id_activ, $id_nom);
        if (empty($aDatos)) {
            return null;
        }
        return Asistente::fromArray($aDatos);
    }
}