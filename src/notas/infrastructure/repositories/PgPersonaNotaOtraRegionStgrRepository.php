<?php

namespace src\notas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigDB;
use core\ConfigGlobal;
use core\ConverterDate;
use core\ConverterJson;
use core\DBConnection;
use core\Set;
use notas\model\EditarPersonaNota;
use PDO;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\notas\domain\value_objects\NotaSituacion;
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

    function __construct(string $esquema_region_stgr)
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

    public function addCertificado(int $id_nom, string $certificado, $oF_certificado)
    {
        $cPersonaNotaOtraRegionStgr = $this->getPersonaNotas(['id_nom' => $id_nom]);
        foreach ($cPersonaNotaOtraRegionStgr as $oPersonaNotaOtraRegionStgr) {
            // miro los que hay para añadir este
            $a_json_certificados = (array)$oPersonaNotaOtraRegionStgr->getJson_certificados();
            $oCert = new stdClass();
            $oCert->certificado = $certificado;
            $oCert->estado = 'guardado';
            $a_json_certificados[] = $oCert;
            $oPersonaNotaOtraRegionStgr->setJson_certificados($a_json_certificados);
            $oPersonaNotaOtraRegionStgr->DBGuardar();

            // miro de guardarlo en su dl.
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PgPersonaNotaRepository::class);
            $aWhere = ['id_nom' => $id_nom,
                'id_nivel' => $oPersonaNotaOtraRegionStgr->getIdNivelVo()->value(),
                'id_asignatura' => $oPersonaNotaOtraRegionStgr->getIdAsignaturaVo()->value(),
                'tipo_acta' => TipoActa::FORMATO_CERTIFICADO, // si no habrá 2, una con formato acta y otra certificado
                //'id_situacion' => NotaSituacion::FALTA_CERTIFICADO,
            ];
            $cPersonNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhere);
            if (empty($cPersonNotas)) {
                $id_asignatura = $oPersonaNotaOtraRegionStgr->getIdAsignaturaVo()->value();
                $msg = sprintf(_("Nota no encontrada. id_asignatura: %s, id_nom: %s"), $id_asignatura, $id_nom);
                //throw new \Exception($msg);
                $oPersonaNota = new PersonaNota();
                $oPersonaNota->setIdNivel($oPersonaNotaOtraRegionStgr->getIdNivelVo()->value());
                $oPersonaNota->setIdAsignatura($id_asignatura);
                $oPersonaNota->setIdNom($id_nom);
                $oPersonaNota->setTipoActa(TipoActa::FORMATO_CERTIFICADO);
                $oPersonaNota->setIdSituacion($oPersonaNotaOtraRegionStgr->getIdSituacionVo()->value());
                $oPersonaNota->setActaVo($oPersonaNotaOtraRegionStgr->getActaVo()->value());
                $oPersonaNota->setDetalle($oPersonaNotaOtraRegionStgr->getDetalleVo()->value());
                $oPersonaNota->setFacta($oF_certificado);
                $oPersonaNota->setPreceptor($oPersonaNotaOtraRegionStgr->isPreceptor());
                $oPersonaNota->setIdpreceptor($oPersonaNotaOtraRegionStgr->getId_preceptor());
                $oPersonaNota->setEpocaVo($oPersonaNotaOtraRegionStgr->getEpocaVo()->value());
                $oPersonaNota->setIdactiv($oPersonaNotaOtraRegionStgr->getIdActividadVo()->value());
                $oPersonaNota->setNotanum($oPersonaNotaOtraRegionStgr->getNotaNumVo()->value());
                $oPersonaNota->setNotamax($oPersonaNotaOtraRegionStgr->getNotaMaxVo()->value());

                $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
                $rta = $oEditarPersonaNota->nuevoSolamenteDl();
                if (!empty($rta['nota_certificado'])) {
                    $oPersonaNota = $rta['nota_certificado'];
                    $oPersonaNota->setActaVo($certificado);
                    $oPersonaNota->setIdSituacionVo(NotaSituacion::NUMERICA);
                    $PersonaNotaDBRepository->Guardar($oPersonaNota);
                }

            } else {
                $oPersonaNota = $cPersonNotas[0];
                if (!empty($oPersonaNota)) {
                    $oPersonaNota->setIdSituacionVo($oPersonaNotaOtraRegionStgr->getIdSituacionVo()->value());
                    $oPersonaNota->setF_acta($oF_certificado);
                    $oPersonaNota->setActaVo($certificado);
                    //$oPersonaNota->setDetalleVo($detalle); // dejo lo que hay
                    $oPersonaNota->setPreceptor($oPersonaNotaOtraRegionStgr->isPreceptor());
                    $oPersonaNota->setId_preceptor($oPersonaNotaOtraRegionStgr->getId_preceptor());
                    $oPersonaNota->setEpocaVo($oPersonaNotaOtraRegionStgr->getEpocaVo()->value());
                    $oPersonaNota->setIdActividadVo($oPersonaNotaOtraRegionStgr->getIdActividadVo()->value());
                    $oPersonaNota->setNotaNumVo($oPersonaNotaOtraRegionStgr->getNotaNumVo()->value());
                    $oPersonaNota->setNotaMaxVo($oPersonaNotaOtraRegionStgr->getNotaMaxVo()->value());
                    $PersonaNotaDBRepository->Guardar($oPersonaNota);
                }
            }
        }

    }

    public function deleteCertificado(?string $certificado)
    {
        $cPersonaNotasOtraRegionStgr = $this->getPersonaNotasConCertificado($certificado);
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PgPersonaNotaRepository::class);
        foreach ($cPersonaNotasOtraRegionStgr as $oPersonaNotaOtraRegionStgr) {
            $a_json_certificados = (array)$oPersonaNotaOtraRegionStgr->getJson_certificados();
            foreach ($a_json_certificados as $key => $json_certificado) {
                if ($json_certificado->certificado === $certificado) {
                    unset($a_json_certificados[$key]);
                    // miro de guardarlo en su dl (poner que falta certificado).
                    $aWhere = ['id_nom' => $oPersonaNotaOtraRegionStgr->getId_nom(),
                        'id_nivel' => $oPersonaNotaOtraRegionStgr->getIdNivelVo()->value(),
                        'id_asignatura' => $oPersonaNotaOtraRegionStgr->getIdAsignaturaVo()->value(),
                        'tipo_acta' => TipoActa::FORMATO_CERTIFICADO,
                        'acta' => $certificado,
                    ];
                    $personaNotasDB = $PersonaNotaDBRepository->getPersonaNotas($aWhere);
                    $oPersonaNotaDB = $personaNotasDB[0] ?? '';
                    if (!empty($oPersonaNotaDB)) {
                        $oPersonaNotaDB->setIdSituacionVo(NotaSituacion::FALTA_CERTIFICADO);
                        $oPersonaNotaDB->setF_acta('');
                        $oPersonaNotaDB->setActaVo(_("falta certificado"));
                        $PersonaNotaDBRepository->Guardar($oPersonaNotaDB);
                    }
                }
            }
            $oPersonaNotaOtraRegionStgr->setJson_certificados($a_json_certificados);
            $this->Guardar($oPersonaNotaOtraRegionStgr);
        }
    }

    private function getPersonaNotasConCertificado(?string $certificado, ?string $estado = null): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oPersonaNotaOtraRegionStgrSet = new Set();

        $json = '';
        if (!empty($certificado)) {
            $json .= empty($json) ? '' : ',';
            $json .= "\"certificado\":\"$certificado\"";

        }
        if (!empty($estado)) {
            $json .= empty($json) ? '' : ',';
            $json .= "\"estado\":\"$estado\"";
        }

        if (!empty($json)) {
            $Where_json = "json_certificados @> '[{" . $json . "}]'";
        }

        if (empty($json)) {
            $where_condi = '';
        } else {
            $where_condi = $Where_json;
        }
        $where_condi = empty($where_condi) ? '' : "WHERE " . $where_condi;

        $sQry = "SELECT * FROM $nom_tabla $where_condi ";

        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);

        foreach ($stmt as $aDatos) {
            $oPersonaNotaOtraRegionStgr = new PersonaNotaOtraRegionStgr();
            $oPersonaNotaOtraRegionStgr->setId_nom($aDatos['id_nom']);
            $oPersonaNotaOtraRegionStgr->setId_nivel($aDatos['id_nivel']);
            $oPersonaNotaOtraRegionStgr->setIdAsignaturaVo($aDatos['id_asignatura']);
            $oPersonaNotaOtraRegionStgr->setIdSituacionVo($aDatos['id_situacion']);
            $oPersonaNotaOtraRegionStgr->setTipoActaVo($aDatos['tipo_acta']);
            $oPersonaNotaOtraRegionStgrSet->add($oPersonaNotaOtraRegionStgr);
        }
        return $oPersonaNotaOtraRegionStgrSet->getTot();
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActaTribunalDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array Una colección de objetos de tipo ActaTribunalDl
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
            $aDatos['f_acta'] = (new ConverterDate('date', $aDatos['f_acta']))->fromPg();
            // para los json
            $aDatos['json_certificados'] = (new ConverterJson($aDatos['json_certificados'], false))->fromPg();

            $a_pkey = array('id_nom' => $aDatos['id_nom'],
                'id_nivel' => $aDatos['id_nivel'],
                'tipo_acta' => $aDatos['tipo_acta']);
            $PersonaNota = $this->chooseNewObject($a_pkey);
            //$PersonaNota->setAllAttributes($aDatos);
            $PersonaNota = $PersonaNota::fromArray($aDatos);
            $PersonaNotaSet->add($PersonaNota);
        }
        return $PersonaNotaSet->getTot();
    }

    protected function chooseNewObject($a_pkey): PersonaNota|PersonaNotaOtraRegionStgr
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
        $id_nivel = $personaNotaOtraRegionStgr->getId_nivel();
        $tipo_acta = $personaNotaOtraRegionStgr->getTipoActaVo()->value();
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
        $id_nivel = $personaNotaOtraRegionStgr->getId_nivel();
        $tipo_acta = $personaNotaOtraRegionStgr->getTipoActaVo()->value();
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
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_nom, int $id_nivel, int $tipo_acta): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom AND id_nivel=$id_nivel AND tipo_acta=$tipo_acta";
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
     * @param int $id_item
     * @return array|bool
     */
    public function datosById(int $id_nom, int $id_nivel, int $tipo_acta): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE  id_nom = $id_nom AND id_nivel=$id_nivel AND tipo_acta=$tipo_acta";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_acta'] = (new ConverterDate('date', $aDatos['f_acta']))->fromPg();
        }
        return $aDatos;
    }

    public function datosByPk(PersonaNotaPk $pk): array|bool
    {
        return $this->datosById($pk->idNom(), $pk->idNivel(), $pk->tipoActa());
    }

    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_nom, int $id_nivel, int $tipo_acta): ?PersonaNotaOtraRegionStgr
    {
        $aDatos = $this->datosById($id_nom, $id_nivel, $tipo_acta);
        if (empty($aDatos)) {
            return null;
        }
        return PersonaNotaOtraRegionStgr::fromArray($aDatos);
    }

    public function findByPk(PersonaNotaPk $pk): ?PersonaNotaOtraRegionStgr
    {
        return $this->findById($pk->idNom(), $pk->idNivel(), $pk->tipoActa());
    }

}