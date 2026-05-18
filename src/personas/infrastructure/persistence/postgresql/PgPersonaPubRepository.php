<?php

namespace src\personas\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaPub;
use src\personas\infrastructure\persistence\postgresql\traits\PersonaGlobalListsTrait;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\RegionStgrAviso;


/**
 * Clase que adapta la tabla personas_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PgPersonaPubRepository extends ClaseRepository implements PersonaPubRepositoryInterface
{
    use HandlesPdoErrors;
    use PersonaGlobalListsTrait;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('v_personas_pub');
    }

    /**
     * Crea una entidad PersonaPub desde un array de datos
     */
    protected function createEntityFromArray(array $aDatos): PersonaPub
    {
        return PersonaPub::fromArray($this->withIdSchema($aDatos));
    }

    /**
     * v_personas_pub no incluye id_schema; se obtiene del esquema de la dl de origen.
     */
    private function withIdSchema(array $aDatos): array
    {
        if (isset($aDatos['id_schema']) && $aDatos['id_schema'] !== '' && $aDatos['id_schema'] !== null) {
            return $aDatos;
        }
        $dl = $aDatos['dl'] ?? '';
        if ($dl === '' || $dl === null) {
            return $aDatos;
        }
        $gesDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $aDatos['id_schema'] = $gesDelegacion->mi_region_stgr((string) $dl)['mi_id_schema'];

        return $aDatos;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo PersonaDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|bool Una colección de objetos de tipo PersonaDl
     */
    public function getPersonas(array $aWhere = [], array $aOperators = []): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $PersonaDlSet = new Set();
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
            // para las fechas del postgres (texto iso)
            $aDatos['f_nacimiento'] = (new ConverterDate('date', $aDatos['f_nacimiento']))->fromPg();
            $aDatos['f_situacion'] = (new ConverterDate('date', $aDatos['f_situacion']))->fromPg();
            $aDatos['f_inc'] = (new ConverterDate('date', $aDatos['f_inc']))->fromPg();

            // Cada repositorio hijo crea su tipo específico
            $Persona = $this->createEntityFromArray($aDatos);
            $PersonaDlSet->add($Persona);
        }
        return $PersonaDlSet->getTot();
    }

    /**
     * @return array<int, PersonaPub>
     */
    public function getPersonasParaListado(
        array $aWhere,
        array $aOperators,
        string &$avisosRegionStgr,
        array &$sinRegionStgrPorIdNom = [],
    ): array {
        $sinRegionStgrPorIdNom = [];
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $PersonaDlSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre' || $camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN' || $sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = ' WHERE ' . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        unset($aWhere['_ordre']);
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        unset($aWhere['_limit']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $aDatos) {
            $aDatos['f_nacimiento'] = (new ConverterDate('date', $aDatos['f_nacimiento']))->fromPg();
            $aDatos['f_situacion'] = (new ConverterDate('date', $aDatos['f_situacion']))->fromPg();
            $aDatos['f_inc'] = (new ConverterDate('date', $aDatos['f_inc']))->fromPg();
            $marcaAviso = false;
            $persona = $this->createEntityParaListado($aDatos, $avisosRegionStgr, $marcaAviso);
            if ($marcaAviso) {
                $sinRegionStgrPorIdNom[$persona->getId_nom()] = true;
            }
            $PersonaDlSet->add($persona);
        }

        return $PersonaDlSet->getTot();
    }

    public function findByIdParaListado(int $id_nom, string &$avisosRegionStgr, bool &$marcaAvisoRegionStgr): ?PersonaPub
    {
        $marcaAvisoRegionStgr = false;
        $aDatos = $this->datosById($id_nom);
        if (empty($aDatos)) {
            return null;
        }

        return $this->createEntityParaListado($aDatos, $avisosRegionStgr, $marcaAvisoRegionStgr);
    }

    private function createEntityParaListado(
        array $aDatos,
        string &$avisosRegionStgr,
        bool &$marcaAvisoRegionStgr = false,
    ): PersonaPub {
        $marcaAvisoRegionStgr = false;
        try {
            return PersonaPub::fromArray($this->withIdSchema($aDatos));
        } catch (\RuntimeException $e) {
            if (!RegionStgrAviso::esDlSinRegion($e)) {
                throw $e;
            }
            $avisosRegionStgr = RegionStgrAviso::append($avisosRegionStgr, $e->getMessage());
            $marcaAvisoRegionStgr = true;
            $aDatos['id_schema'] = 0;

            return PersonaPub::fromArray($aDatos);
        }
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    protected function isNew(int $id_nom): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
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
     * @param int $id_nom
     * @return array|bool
     */
    public function datosById(int $id_nom): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_nacimiento'] = (new ConverterDate('date', $aDatos['f_nacimiento']))->fromPg();
            $aDatos['f_situacion'] = (new ConverterDate('date', $aDatos['f_situacion']))->fromPg();
            $aDatos['f_inc'] = (new ConverterDate('date', $aDatos['f_inc']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_nom en la base de datos .
     */
    public function findById(int $id_nom): ?PersonaPub
    {
        $aDatos = $this->datosById($id_nom);
        if (empty($aDatos)) {
            return null;
        }
        return $this->createEntityFromArray($aDatos);
    }
}