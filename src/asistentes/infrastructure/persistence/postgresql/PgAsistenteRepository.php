<?php

namespace src\asistentes\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\asistentes\domain\value_objects\AsistentePk;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\traits\DispatchesDomainEvents;
use src\shared\traits\HandlesPdoErrors;


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
    use DispatchesDomainEvents;

    protected UnitOfWorkInterface $unitOfWork;

    public function __construct(UnitOfWorkInterface $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
        $oDbl = GlobalPdo::get('oDBEP');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBEP_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_asistentes_all');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Asistente
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Asistente>
     */
    public function getAsistentes(array $aWhere = [], array $aOperators = []): array
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
        if (isset($aWhere['_ordre']) && is_string($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && is_scalar($aWhere['_limit']) && (string) $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Asistente = Asistente::fromArray($aDatos);
            $AsistenteSet->add($Asistente);
        }
        return array_values($AsistenteSet->getTot());
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Asistente $Asistente, bool $registrarCambios = true): bool
    {
        $id_activ = $Asistente->getId_activ();
        $id_nom = $Asistente->getId_nom();

        // Obtener datos actuales antes de eliminar (solo si hay que registrar cambios)
        $datosActuales = $registrarCambios ? $this->datosById($id_activ, $id_nom) : false;

        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ AND id_nom = $id_nom";
        $success = $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        if ($registrarCambios && $success && is_array($datosActuales)) {
            // Marcar como eliminada
            $this->markAsDeleted($Asistente, $datosActuales);
        }

        return $success;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Asistente $Asistente, bool $registrarCambios = true): bool
    {
        $id_activ = $Asistente->getId_activ();
        $id_nom = $Asistente->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ, $id_nom);

        // Obtener datos actuales si es UPDATE (solo si hay que registrar cambios)
        $datosActuales = [];
        if ($registrarCambios && !$bInsert) {
            $datosActuales = $this->datosById($id_activ, $id_nom) ?: [];
        }

        $aDatos = $Asistente->toArrayForDatabase();
        unset($aDatos['domainEvents']);

        $requiere_id_schema = ($nom_tabla === 'd_asistentes_de_paso' || $nom_tabla === 'd_asistentes_all');

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_activ']);
            unset($aDatos['id_nom']);
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
					observ_est               = :observ_est,
					cama                     = :cama";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ AND id_nom = $id_nom";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            if ($requiere_id_schema) {
                $campos = "(id_schema,id_activ,id_nom,propio,est_ok,cfi,cfi_con,falta,encargo,dl_responsable,observ,id_tabla,plaza,propietario,observ_est,cama)";
                $valores = "(:id_schema,:id_activ,:id_nom,:propio,:est_ok,:cfi,:cfi_con,:falta,:encargo,:dl_responsable,:observ,:id_tabla,:plaza,:propietario,:observ_est,:cama)";
            } else {
                $campos = "(id_activ,id_nom,propio,est_ok,cfi,cfi_con,falta,encargo,dl_responsable,observ,id_tabla,plaza,propietario,observ_est,cama)";
                $valores = "(:id_activ,:id_nom,:propio,:est_ok,:cfi,:cfi_con,:falta,:encargo,:dl_responsable,:observ,:id_tabla,:plaza,:propietario,:observ_est,:cama)";
            }
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($stmt === false) {
            return false;
        }

        if ($bInsert && $requiere_id_schema) {
            $sid = ConfigGlobal::mi_id_schema();
            $idSchema = is_numeric($sid) ? (int) $sid : (int) filter_var((string) $sid, FILTER_VALIDATE_INT);
            if ($idSchema < 1) {
                throw new \RuntimeException(_('Falta id_schema de sesión (mi_id_schema) para persistir el asistente.'));
            }
            $aDatos['id_schema'] = $idSchema;
        }

        $success = $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);

        if ($registrarCambios && $success) {
            // Marcar evento de dominio
            if ($bInsert) {
                $this->markAsNew($Asistente, $datosActuales);
            } else {
                $this->markAsModified($Asistente, $datosActuales);
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
     * @param int $id_activ
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_activ, int $id_nom): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ AND id_nom = $id_nom";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }

        $row = [];
        foreach ($aDatos as $key => $value) {
            $row[(string) $key] = $value;
        }

        return $row;
    }

    public function datosByPk(AsistentePk $pk): array|false
    {
        return $this->datosById($pk->IdActiv(), $pk->IdNom());
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_activ, int $id_nom): ?Asistente
    {
        $aDatos = $this->datosById($id_activ, $id_nom);
        if (!is_array($aDatos)) {
            return null;
        }
        return Asistente::fromArray($aDatos);
    }

    public function findByPk(AsistentePk $pk): ?Asistente
    {
        return $this->findById($pk->IdActiv(), $pk->IdNom());
    }
}
