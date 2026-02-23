<?php

namespace src\personas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\shared\traits\HandlesPdoErrors;
use src\personas\infrastructure\repositories\traits\PersonaGlobalListsTrait;

/**
 * Clase base abstracta para repositorios de PersonaDl y subtipos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/1/2026
 */
abstract class PgPersonaDlRepositoryBase extends ClaseRepository
{
    use HandlesPdoErrors;
    use PersonaGlobalListsTrait;

    /**
     * Método abstracto que cada hijo debe implementar para crear su tipo específico de entidad
     */
    abstract protected function createEntityFromArray(array $aDatos): PersonaDl;

    /* --------------------  BASIC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo PersonaDl (o subtipos)
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array Una colección de objetos de tipo PersonaDl o subtipos
     */
    public function getPersonas(array $aWhere = [], array $aOperators = []): array
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
     * Busca la clase con id_nom en la base de datos.
     * Devuelve el tipo específico según el repositorio hijo
     */
    public function findById(int $id_nom): ?PersonaDl
    {
        $aDatos = $this->datosById($id_nom);
        if (empty($aDatos)) {
            return null;
        }
        return $this->createEntityFromArray($aDatos);
    }
}
