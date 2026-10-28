<?php

namespace src\personas\infrastructure\persistence\postgresql;

use PDO;
use src\personas\domain\PersonaPublicacion;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\personas\infrastructure\persistence\postgresql\traits\PersonaGlobalListsTrait;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\personas\domain\criteria\PersonaField;
use src\personas\infrastructure\persistence\postgresql\traits\PersonaCriteriaTrait;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;


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
    use PersonaGlobalListsTrait;
    use PersonaCriteriaTrait;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBC'));
        $this->setoDbl_select(GlobalPdo::get('oDBC_Select'));
        $this->setNomTabla('cp_sacd');
    }

    /**
     * Crea una entidad PersonaSacd desde un array de datos.
     * No usar PersonaPub: es hermana, no padre; el listado de comunicación
     * descarta todo lo que no sea instanceof PersonaSacd.
     */
    /** @param array<string, mixed> $aDatos */
    protected function createEntityFromArray(array $aDatos): PersonaSacd
    {
        return PersonaSacd::fromArray($aDatos);
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
     * Personas que cumplen el criterio.
     *
     * @return list<PersonaSacd>
     */
    public function matching(Criteria $criteria): array
    {
        $result = [];
        foreach ($this->filasPersonasMatching($criteria) as $aDatos) {
            // Cada repositorio hijo crea su tipo específico
            $result[] = $this->createEntityFromArray($aDatos);
        }

        return $result;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PersonaSacd>
     */
    public function getPersonas(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(PersonaField::class, []);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
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
        // `cp_sacd` no tiene estas columnas; el SQL no las enlaza.
        unset($aDatos['edad'], $aDatos['profesor_stgr'], $aDatos['publicado_para']);

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
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_nom): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
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
     * @param int $id_nom
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_nom): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if (!is_array($aDatos)) {
            return false;
        }
        $aDatos['f_nacimiento'] = (new ConverterDate('date', $aDatos['f_nacimiento']))->fromPg();
            $aDatos['f_situacion'] = (new ConverterDate('date', $aDatos['f_situacion']))->fromPg();
            $aDatos['f_inc'] = (new ConverterDate('date', $aDatos['f_inc']))->fromPg();
        $aDatos = PersonaPublicacion::hydrateRow($aDatos);
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }


    /**
     * Busca la clase con id_nom en la base de datos .
     */
    public function findById(int $id_nom): ?PersonaSacd
    {
        $aDatos = $this->datosById($id_nom);
        if ($aDatos === false) {
            return null;
        }
        return PersonaSacd::fromArray($aDatos);
    }

}