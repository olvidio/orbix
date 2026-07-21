<?php

namespace src\notas\infrastructure\persistence\postgresql;

use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\ConverterJson;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\notas\domain\value_objects\PersonaNotaPk;
use src\notas\domain\value_objects\TipoActa;
use src\shared\traits\HandlesPdoErrors;
use stdClass;


/**
 * Clase que adapta la tabla e_actas_tribunal_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
class PgPersonaNotaOtraRegionStgrRepository extends ClaseRepository implements PersonaNotaOtraRegionStgrRepositoryInterface
{

    use HandlesPdoErrors;

    protected string $esquema_region_stgr;

    public function __construct(
        string                                            $esquema_region_stgr,
        private readonly PersonaNotaRepositoryInterface   $personaNotaRepository,
        private readonly DelegacionRepositoryInterface    $delegacionRepository,
        private readonly DbSchemaRepositoryInterface      $dbSchemaRepository,
        private readonly DossierRepositoryInterface       $dossierRepository,
        private readonly PersonaNotaDlRepositoryInterface $personaNotaDlRepository,
    )
    {
        $this->esquema_region_stgr = $esquema_region_stgr;
        $db = (ConfigGlobal::mi_sfsv() === 1) ? 'sv' : 'sf';
        // se debe conectar con la region del stgr padre
        $oConfigDB = new ConfigDB($db); //de la database sv/sf
        $config = $oConfigDB->getEsquema($esquema_region_stgr);
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();

        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_otra_region_stgr');
    }

    public function addCertificado(int $id_nom, string $certificado, DateTimeLocal|null $oF_certificado): void
    {
        // Modelo acta: el certificado es documental. Solo se anota en json_certificados
        // de filas legacy en e_notas_otra_region_stgr. No se crean/actualizan filas
        // FORMATO_CERTIFICADO en e_notas_dl (el expediente se lee vía publicv.e_notas).
        unset($oF_certificado);
        $cPersonaNotaOtraRegionStgr = $this->getPersonaNotas(['id_nom' => $id_nom]);
        foreach ($cPersonaNotaOtraRegionStgr as $oPersonaNotaOtraRegionStgr) {
            $rawCerts = $oPersonaNotaOtraRegionStgr->getJson_certificados(true);
            $a_json_certificados = is_array($rawCerts) ? $rawCerts : [];
            $oCert = new stdClass();
            $oCert->certificado = $certificado;
            $oCert->estado = 'guardado';
            $a_json_certificados[] = $oCert;
            $oPersonaNotaOtraRegionStgr->setJson_certificados($a_json_certificados);
            $this->Guardar($oPersonaNotaOtraRegionStgr);
        }
    }

    public function deleteCertificado(?string $certificado): void
    {
        // Solo limpia json_certificados en filas legacy; no restaura «falta certificado» en DL.
        $cPersonaNotasOtraRegionStgr = $this->getPersonaNotasConCertificado($certificado);
        foreach ($cPersonaNotasOtraRegionStgr as $oPersonaNotaOtraRegionStgr) {
            $a_json_certificados = (array)$oPersonaNotaOtraRegionStgr->getJson_certificados();
            foreach ($a_json_certificados as $key => $json_certificado) {
                if (is_object($json_certificado) && ($json_certificado->certificado ?? null) === $certificado) {
                    unset($a_json_certificados[$key]);
                }
            }
            $oPersonaNotaOtraRegionStgr->setJson_certificados(array_values($a_json_certificados));
            $this->Guardar($oPersonaNotaOtraRegionStgr);
        }
    }

    /**
     * @return list<\src\notas\domain\entity\PersonaNotaOtraRegionStgr>
     */
    private function getPersonaNotasConCertificado(?string $certificado, ?string $estado = null): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oPersonaNotaOtraRegionStgrSet = new Set();

        $jsonParts = [];
        if ($certificado !== null && $certificado !== '') {
            $jsonParts[] = "\"certificado\":\"$certificado\"";
        }
        if ($estado !== null && $estado !== '') {
            $jsonParts[] = "\"estado\":\"$estado\"";
        }

        $where_condi = '';
        if ($jsonParts !== []) {
            $json = implode(',', $jsonParts);
            $where_condi = "WHERE json_certificados @> '[{" . $json . "}]'";
        }

        $sQry = "SELECT * FROM $nom_tabla $where_condi ";

        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        foreach ($stmt as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string)$key] = $value;
            }
            $normalized['f_acta'] = (new ConverterDate('date', $normalized['f_acta'] ?? null))->fromPg();
            $jsonCertificados = $normalized['json_certificados'] ?? null;
            if (!is_string($jsonCertificados) && !is_array($jsonCertificados) && !($jsonCertificados instanceof \stdClass)) {
                $jsonCertificados = null;
            }
            $normalized['json_certificados'] = (new ConverterJson($jsonCertificados, false))->fromPg();
            $oPersonaNotaOtraRegionStgr = PersonaNotaOtraRegionStgr::fromArray($normalized);
            $oPersonaNotaOtraRegionStgrSet->add($oPersonaNotaOtraRegionStgr);
        }
        /** @var list<PersonaNotaOtraRegionStgr> $items */
        $items = array_values($oPersonaNotaOtraRegionStgrSet->getTot());
        return $items;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActaTribunalDl
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<\src\notas\domain\entity\PersonaNota|\src\notas\domain\entity\PersonaNotaOtraRegionStgr> Una colección de objetos PersonaNota
     */
    /** @param array<string, mixed> $aWhere */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<\src\notas\domain\entity\PersonaNotaOtraRegionStgr>
     */
    public function getPersonaNotas(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $PersonaNotaSet = new Set();
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
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string)$limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
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
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string)$key] = $value;
            }
            // para las fechas del postgres (texto iso)
            $normalized['f_acta'] = (new ConverterDate('date', $normalized['f_acta']))->fromPg();
            // para los json
            $jsonCertificados = $normalized['json_certificados'] ?? null;
            if (!is_string($jsonCertificados) && !is_array($jsonCertificados) && !($jsonCertificados instanceof \stdClass)) {
                $jsonCertificados = null;
            }
            $normalized['json_certificados'] = (new ConverterJson($jsonCertificados, false))->fromPg();

            $idNivelRaw = $normalized['id_nivel'] ?? null;
            $idNivel = is_numeric($idNivelRaw) ? (int)$idNivelRaw : null;
            $a_pkey = array('id_nom' => $normalized['id_nom'],
                'id_nivel' => NivelId::fromNullableInt($idNivel),
                'tipo_acta' => $normalized['tipo_acta']);
            $PersonaNota = $this->chooseNewObject($a_pkey);
            //$PersonaNota->setAllAttributes($aDatos);
            $PersonaNota = $PersonaNota::fromArray($normalized);
            $PersonaNotaSet->add($PersonaNota);
        }
        /** @var list<PersonaNotaOtraRegionStgr> $items */
        $items = array_values($PersonaNotaSet->getTot());
        return $items;
    }

    /**
     * @param array<string, mixed> $a_pkey
     */
    protected function chooseNewObject(array $a_pkey): PersonaNota|PersonaNotaOtraRegionStgr
    {
        if ($this->sNomTabla === "e_notas_otra_region_stgr") {
            $oPersonaNota = new PersonaNotaOtraRegionStgr($a_pkey);
        } else {
            $oPersonaNota = new PersonaNota($a_pkey);
        }
        return $oPersonaNota;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(PersonaNotaOtraRegionStgr $personaNotaOtraRegionStgr): bool
    {
        $id_nom = $personaNotaOtraRegionStgr->getId_nom();
        $id_nivel = $personaNotaOtraRegionStgr->getIdNivelVo()->value();
        $tipo_acta = $personaNotaOtraRegionStgr->getTipoActaVo()?->value() ?? TipoActa::FORMATO_ACTA;
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_nom=$id_nom AND id_nivel=$id_nivel AND tipo_acta=$tipo_acta";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(PersonaNotaOtraRegionStgr $personaNotaOtraRegionStgr): bool
    {
        $id_nom = $personaNotaOtraRegionStgr->getId_nom();
        $id_nivel = $personaNotaOtraRegionStgr->getIdNivelVo()->value();
        $tipo_acta = $personaNotaOtraRegionStgr->getTipoActaVo()?->value() ?? TipoActa::FORMATO_ACTA;
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_nom, $id_nivel, $tipo_acta);

        $aDatos = $personaNotaOtraRegionStgr->toArrayForDatabase([
            'f_acta' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'json_certificados' => fn($v) => (new ConverterJson($v, false))->toPg(false),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_nom']);
            $update = "
           	        id_nivel	             = :id_nivel,
					id_asignatura            = :id_asignatura,
					id_situacion             = :id_situacion,
					acta                     = :acta,
					f_acta                   = :f_acta,
					detalle                  = :detalle,
					preceptor                = :preceptor,
					id_preceptor             = :id_preceptor,
					epoca                    = :epoca,
					id_activ                 = :id_activ,
					nota_num                 = :nota_num,
					nota_max                 = :nota_max,
					tipo_acta                = :tipo_acta,
                    json_certificados        = :json_certificados";
            $sql = "UPDATE $nom_tabla SET $update WHERE  id_nom = $id_nom AND id_nivel=$id_nivel AND tipo_acta=$tipo_acta";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_nom,id_nivel,id_asignatura,id_situacion,acta,f_acta,detalle,preceptor,id_preceptor,epoca,id_activ,nota_num,nota_max,tipo_acta,json_certificados)";
            $valores = "(:id_nom,:id_nivel,:id_asignatura,:id_situacion,:acta,:f_acta,:detalle,:preceptor,:id_preceptor,:epoca,:id_activ,:nota_num,:nota_max,:tipo_acta,:json_certificados)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_nom, int $id_nivel, int $tipo_acta): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom AND id_nivel=$id_nivel AND tipo_acta=$tipo_acta";
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
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_nom, int $id_nivel, int $tipo_acta): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE  id_nom = $id_nom AND id_nivel=$id_nivel AND tipo_acta=$tipo_acta";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if (!is_array($aDatos)) {
            return false;
        }
        $aDatos['f_acta'] = (new ConverterDate('date', $aDatos['f_acta']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string)$key] = $value;
        }
        return $result;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(PersonaNotaPk $pk): array|false
    {
        return $this->datosById($pk->idNom(), $pk->idNivel(), $pk->tipoActa());
    }

    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_nom, int $id_nivel, int $tipo_acta): ?PersonaNotaOtraRegionStgr
    {
        $aDatos = $this->datosById($id_nom, $id_nivel, $tipo_acta);
        if ($aDatos === false) {
            return null;
        }
        return PersonaNotaOtraRegionStgr::fromArray($aDatos);
    }

    public function findByPk(PersonaNotaPk $pk): ?PersonaNotaOtraRegionStgr
    {
        return $this->findById($pk->idNom(), $pk->idNivel(), $pk->tipoActa());
    }

}