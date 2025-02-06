<?php

namespace notas\model\entity;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use core\Set;
use notas\model\PersonaNota;
use web\DateTimeLocal;
use stdClass;

class GestorPersonaNotaOtraRegionStgrDB extends GestorPersonaNotaDB
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

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

    public function addCertificado(int $id_nom, string $certificado,  $oF_certificado)
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
            $gesPersonaNotas = new GestorPersonaNotaDB();
            $aWhere = ['id_nom' => $id_nom,
                'id_nivel' => $oPersonaNotaOtraRegionStgr->getId_nivel(),
                'id_asignatura' => $oPersonaNotaOtraRegionStgr->getId_asignatura(),
                'tipo_acta' => PersonaNota::FORMATO_CERTIFICADO,
                'id_situacion' => Nota::FALTA_CERTIFICADO,
            ];
            $cPersonNotas = $gesPersonaNotas->getPersonaNotas($aWhere);
            $oPersonaNota = $cPersonNotas[0];
            if (!empty($oPersonaNota)) {
                $oPersonaNota->DBCarregar();
                $oPersonaNota->setId_situacion($oPersonaNotaOtraRegionStgr->getId_situacion());
                $oPersonaNota->setF_acta($oF_certificado);
                $oPersonaNota->setActa($certificado);
                //$oPersonaNota->setDetalle($detalle);
                $oPersonaNota->setPreceptor($oPersonaNotaOtraRegionStgr->getPreceptor());
                $oPersonaNota->setId_preceptor($oPersonaNotaOtraRegionStgr->getId_preceptor());
                $oPersonaNota->setEpoca($oPersonaNotaOtraRegionStgr->getEpoca());
                $oPersonaNota->setId_activ($oPersonaNotaOtraRegionStgr->getId_activ());
                $oPersonaNota->setNota_num($oPersonaNotaOtraRegionStgr->getNota_num());
                $oPersonaNota->setNota_max($oPersonaNotaOtraRegionStgr->getNota_max());
                $oPersonaNota->DBGuardar();
            }
        }

    }

    public function deleteCertificado(?string $certificado)
    {
        $cPersonaNotasOtraRegionStgr = $this->getPersonaNotasConCertificado($certificado);
        foreach ($cPersonaNotasOtraRegionStgr as $oPersonaNotaOtraRegionStgr) {
            $a_json_certificados = (array)$oPersonaNotaOtraRegionStgr->getJson_certificados();
            foreach ($a_json_certificados as $key => $json_certificado) {
                if ($json_certificado->certificado === $certificado) {
                    unset($a_json_certificados[$key]);
                    // miro de guardarlo en su dl (poner que falta certificado).
                    $gesPersonaNotaDB = new GestorPersonaNotaDB();
                    $aWhere = ['id_nom' => $oPersonaNotaOtraRegionStgr->getId_nom(),
                        'id_nivel' => $oPersonaNotaOtraRegionStgr->getId_nivel(),
                        'id_asignatura' => $oPersonaNotaOtraRegionStgr->getId_asignatura(),
                        'tipo_acta' => PersonaNota::FORMATO_CERTIFICADO,
                        'acta' => $certificado,
                    ];
                    $personaNotasDB = $gesPersonaNotaDB->getPersonaNotas($aWhere);
                    $oPersonaNotaDB = $personaNotasDB[0]?? '';
                    if (!empty($oPersonaNotaDB)) {
                        $oPersonaNotaDB->DBCarregar();
                        $oPersonaNotaDB->setId_situacion(Nota::FALTA_CERTIFICADO);
                        $oPersonaNotaDB->setF_acta('');
                        $oPersonaNotaDB->setActa(_("falta certificado"));
                        $oPersonaNotaDB->DBGuardar();
                    }
                }
            }
            $oPersonaNotaOtraRegionStgr->setJson_certificados($a_json_certificados);
            $oPersonaNotaOtraRegionStgr->DBGuardar();
        }
    }

    private function getPersonaNotasConCertificado(?string $certificado, ?string $estado = null): false|array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oPersonaNotaOtraRegionStgrDBSet = new Set();

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

        if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
            $sClauError = 'GestorPersonaNotaOtraRegionStgr.NotasConCertificado.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute()) === FALSE) {
            $sClauError = 'GestorPersonaNotaOtraRegionStgr.NotasConCertificado.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $oPersonaNotaOtraRegionStgrDB = new PersonaNotaOtraRegionStgrDB($this->esquema_region_stgr);
            $oPersonaNotaOtraRegionStgrDB->setId_nom($aDades['id_nom']);
            $oPersonaNotaOtraRegionStgrDB->setId_nivel($aDades['id_nivel']);
            $oPersonaNotaOtraRegionStgrDB->setId_asignatura($aDades['id_asignatura']);
            $oPersonaNotaOtraRegionStgrDB->setId_situacion($aDades['id_situacion']);
            $oPersonaNotaOtraRegionStgrDB->setTipo_acta($aDades['tipo_acta']);
            $oPersonaNotaOtraRegionStgrDBSet->add($oPersonaNotaOtraRegionStgrDB);
        }
        return $oPersonaNotaOtraRegionStgrDBSet->getTot();
    }

}
