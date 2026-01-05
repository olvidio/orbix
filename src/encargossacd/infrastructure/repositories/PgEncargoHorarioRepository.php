<?php

namespace src\encargossacd\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoHorario;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla encargo_horario a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class PgEncargoHorarioRepository extends ClaseRepository implements EncargoHorarioRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('encargo_horario');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo EncargoHorario
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo EncargoHorario
     */
    public function getEncargoHorarios(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $EncargoHorarioSet = new Set();
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
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $aDatos['h_ini'] = (new ConverterDate('time', $aDatos['h_ini']))->fromPg();
            $aDatos['h_fin'] = (new ConverterDate('time', $aDatos['h_fin']))->fromPg();
            $EncargoHorario =  EncargoHorario::fromArray($aDatos);
            $EncargoHorarioSet->add($EncargoHorario);
        }
        return $EncargoHorarioSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoHorario $EncargoHorario): bool
    {
        $id_item_h = $EncargoHorario->getId_item_h();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item_h = $id_item_h";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(EncargoHorario $EncargoHorario): bool
    {
        $id_item_h = $EncargoHorario->getId_item_h();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item_h);

        $aDatos = [];
        $aDatos['id_enc'] = $EncargoHorario->getId_enc();
        $aDatos['dia_ref'] = $EncargoHorario->getDia_ref();
        $aDatos['dia_num'] = $EncargoHorario->getDia_num();
        $aDatos['mas_menos'] = $EncargoHorario->getMas_menos();
        $aDatos['dia_inc'] = $EncargoHorario->getDia_inc();
        $aDatos['h_ini'] = $EncargoHorario->getH_ini();
        $aDatos['h_fin'] = $EncargoHorario->getH_fin();
        $aDatos['n_sacd'] = $EncargoHorario->getN_sacd();
        $aDatos['mes'] = $EncargoHorario->getMes();
        // para las horas
        $aDatos['h_ini'] = (new ConverterDate('time', $EncargoHorario->getH_ini()))->toPg();
        $aDatos['h_fin'] = (new ConverterDate('time', $EncargoHorario->getH_fin()))->toPg();
        // para las fechas
        $aDatos['f_ini'] = (new ConverterDate('date', $EncargoHorario->getF_ini()))->toPg();
        $aDatos['f_fin'] = (new ConverterDate('date', $EncargoHorario->getF_fin()))->toPg();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_enc                    = :id_enc,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					dia_ref                  = :dia_ref,
					dia_num                  = :dia_num,
					mas_menos                = :mas_menos,
					dia_inc                  = :dia_inc,
					h_ini                    = :h_ini,
					h_fin                    = :h_fin,
					n_sacd                   = :n_sacd,
					mes                      = :mes";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item_h = $id_item_h";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $aDatos['id_item_h'] = $id_item_h;
            $aDatos['id_enc'] = $EncargoHorario->getId_enc();
            $campos = "(id_enc,id_item_h,f_ini,f_fin,dia_ref,dia_num,mas_menos,dia_inc,h_ini,h_fin,n_sacd,mes)";
            $valores = "(:id_enc,:id_item_h,:f_ini,:f_fin,:dia_ref,:dia_num,:mas_menos,:dia_inc,:h_ini,:h_fin,:n_sacd,:mes)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item_h): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_h = $id_item_h";
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
     * @param int $id_item_h
     * @return array|bool
     */
    public function datosById(int $id_item_h): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_h = $id_item_h";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $aDatos['h_ini'] = (new ConverterDate('time', $aDatos['h_ini']))->fromPg();
            $aDatos['h_fin'] = (new ConverterDate('time', $aDatos['h_fin']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_enc en la base de datos .
     */
    public function findById(int $id_item_h): ?EncargoHorario
    {
        $aDatos = $this->datosById($id_item_h);
        if (empty($aDatos)) {
            return null;
        }
        return EncargoHorario::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('encargo_horario_id_item_h_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}