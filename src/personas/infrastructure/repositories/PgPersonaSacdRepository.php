<?php

namespace src\personas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaPub;
use src\personas\domain\entity\PersonaSacd;
use src\personas\infrastructure\repositories\traits\PersonaGlobalListsTrait;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla p_numerarios a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PgPersonaSacdRepository extends ClaseRepository implements PersonaSacdRepositoryInterface
{
    use HandlesPdoErrors;
    use PersonaGlobalListsTrait;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('cp_sacd');
    }

    /**
     * Crea una entidad PersonaPub desde un array de datos
     */
    protected function createEntityFromArray(array $aDatos): PersonaPub
    {
        return PersonaPub::fromArray($aDatos);
    }


    public function getSacdsBySelect(int $Qseleccion_sacd): array
    {
        // selecciono según la variable selecion_sacd ('2'=> n y agd, '4'=> de paso, '8'=> sssc, '16'=>cp)
        $a_tipos_tablas = [];
        if (empty($Qseleccion_sacd) || ($Qseleccion_sacd & 2)) {
            $a_tipos_tablas[] = 'n';
            $a_tipos_tablas[] = 'a';
        }
        if ($Qseleccion_sacd & 4) {
            $a_tipos_tablas[] = 'pn';
            $a_tipos_tablas[] = 'pa';
        }
        if ($Qseleccion_sacd & 8) {
            $a_tipos_tablas[] = 'sssc';
        }

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tabla'] = "'" . implode("','", $a_tipos_tablas) . "'";
        $aOperador['id_tabla'] = 'IN';
        $aWhere['sacd'] = 't';
        $aWhere['situacion'] = 'A';
        $aWhere['_ordre'] = 'apellido1,apellido2,nom';
        $cPersonas = $this->getPersonas($aWhere, $aOperador);

        return $cPersonas;
    }

    public function getArraySacdyCheckBox(int $Qseleccion_sacd): array
    {
        /* lista sacd posibles */
        // selecciono según la variable selecion_sacd ('2'=> n y agd, '4'=> de paso, '8'=> sssc, '16'=>cp)
        $chk_prelatura = '';
        $chk_de_paso = '';
        $chk_sssc = '';
        $a_tipos_tablas = [];
        if (empty($Qseleccion_sacd) || ($Qseleccion_sacd & 2)) {
            $a_tipos_tablas[] = 'n';
            $a_tipos_tablas[] = 'a';
            $chk_prelatura = 'checked';
        }
        if ($Qseleccion_sacd & 4) {
            $a_tipos_tablas[] = 'pn';
            $a_tipos_tablas[] = 'pa';
            $chk_de_paso = 'checked';
        }
        if ($Qseleccion_sacd & 8) {
            $a_tipos_tablas[] = 'sssc';
            $chk_sssc = 'checked';
        }

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tabla'] = "'" . implode("','", $a_tipos_tablas) . "'";
        $aOperador['id_tabla'] = 'IN';
        $aWhere['sacd'] = 't';
        $aWhere['situacion'] = 'A';
        $aWhere['_ordre'] = 'apellido1,apellido2,nom';
        $cPersonas = $this->getPersonas($aWhere, $aOperador);
        $aOpciones = [];
        foreach ($cPersonas as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            $apellidos_nombre = $oPersona->getApellidosNombre();
            $aOpciones[$id_nom] = $apellidos_nombre;
        }

        return array($chk_prelatura, $chk_de_paso, $chk_sssc, $aOpciones);
    }
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo PersonaDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo PersonaDl
     */
    public function getPersonas(array $aWhere = [], array $aOperators = []): array|false
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

    public function Eliminar(PersonaSacd $PersonaSacd): bool
    {
        $id_nom = $PersonaSacd->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_nom = $id_nom";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(PersonaSacd $PersonaSacd): bool
    {
        $id_nom = $PersonaSacd->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_nom);

        $aDatos = $PersonaSacd->toArrayForDatabase([
            'f_nacimiento' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_situacion' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_inc' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_nom']);
            $update = "
					id_tabla                 = :id_tabla,
					dl                       = :dl,
					sacd                     = :sacd,
					trato                    = :trato,
					nom                      = :nom,
					nx1                      = :nx1,
					apellido1                = :apellido1,
					nx2                      = :nx2,
					apellido2                = :apellido2,
					f_nacimiento             = :f_nacimiento,
					idioma_preferido         = :idioma_preferido,
					situacion                = :situacion,
					f_situacion              = :f_situacion,
					apel_fam                 = :apel_fam,
					inc                      = :inc,
					f_inc                    = :f_inc,
					nivel_stgr               = :nivel_stgr,
					profesion                = :profesion,
					eap                      = :eap,
					observ                   = :observ,
					id_ctr                   = :id_ctr,
					lugar_nacimiento         = :lugar_nacimiento";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_nom = $id_nom";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_nom,id_tabla,dl,sacd,trato,nom,nx1,apellido1,nx2,apellido2,f_nacimiento,idioma_preferido,situacion,f_situacion,apel_fam,inc,f_inc,nivel_stgr,profesion,eap,observ,id_ctr,lugar_nacimiento)";
            $valores = "(:id_nom,:id_tabla,:dl,:sacd,:trato,:nom,:nx1,:apellido1,:nx2,:apellido2,:f_nacimiento,:idioma_preferido,:situacion,:f_situacion,:apel_fam,:inc,:f_inc,:nivel_stgr,:profesion,:eap,:observ,:id_ctr,:lugar_nacimiento)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_nom): bool
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
    public function findById(int $id_nom): ?PersonaSacd
    {
        $aDatos = $this->datosById($id_nom);
        if (empty($aDatos)) {
            return null;
        }
        return PersonaSacd::fromArray($aDatos);
    }

}