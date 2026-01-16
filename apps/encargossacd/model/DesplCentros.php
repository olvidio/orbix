<?php

namespace encargossacd\model;

use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use web\Desplegable;

class DesplCentros
{

    private ?int $id_zona = null;

    public function getDesplPorFiltro($filtro_ctr)
    {
        switch ($filtro_ctr) {
            case EncargoGrupo::CENTRO_SV:
                $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                // añado el ctr de oficiales de la dl (14.VIII.07).
                //$sql_ctr="SELECT id_ubi,nombre_ubi FROM u_centros_dl WHERE tipo_ctr ~ 'a.|n.|s[j|m]|of' AND active='t' ORDER BY nombre_ubi";
                $query = "WHERE tipo_ctr ~ '^a|n|s[^s]|of' AND active='t'";
                $aOpciones = $GesCentros->getArrayCentros($query);
                $oDesplCtr = new Desplegable();
                $oDesplCtr->setOpciones($aOpciones);
                break;
            case EncargoGrupo::CENTRO_SF:
                $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                $aOpciones = $GesCentros->getArrayCentros();
                $oDesplCtr = new Desplegable();
                $oDesplCtr->setOpciones($aOpciones);
                break;
            case EncargoGrupo::CENTRO_SSSC:
                $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $query = "WHERE tipo_ctr ~ '^ss' AND active='t'";
                $aOpciones = $GesCentros->getArrayCentros($query);
                $oDesplCtr = new Desplegable();
                $oDesplCtr->setOpciones($aOpciones);
                break;
            case EncargoGrupo::IGL:
                $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $query = "WHERE tipo_ctr ~ '^igl' AND active='t'";
                $aOpciones = $GesCentros->getArrayCentros($query);
                $oDesplCtr = new Desplegable();
                $oDesplCtr->setOpciones($aOpciones);
                break;
            case EncargoGrupo::CGI:
                $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $query = "WHERE tipo_ctr ~ 'cgioc|oc|cgi' AND active='t' ";
                $opciones_sv = $GesCentros->getArrayCentros($query);
                $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                $query = "WHERE tipo_ctr ~ 'cgioc|oc|cgi' AND active='t' ";
                $opciones_sf = $GesCentros->getArrayCentros($query);
                $a_opciones = array_merge($opciones_sv, $opciones_sf);
                $aa_op = [];
                foreach ($a_opciones as $a_vector) {
                    $aa_op[$a_vector['id_ubi']] = $a_vector['nombre_ubi'];
                }
                $oDesplCtr = new Desplegable();
                $oDesplCtr->setBlanco(TRUE);
                $oDesplCtr->setOpciones($aa_op);
                break;
            case EncargoGrupo::ZONAS_MISAS:
                if (!empty($this->id_zona)) {
                    $GesCentrosDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                    $query = "WHERE id_zona = $this->id_zona AND active='t' ";
                    $opciones_sv = $GesCentrosDl->getArrayCentros($query);
                    $GesCentrosSf = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                    $opciones_sf = $GesCentrosSf->getArrayCentros($query);
                    $a_opciones = array_merge($opciones_sv, $opciones_sf);
                    $aa_op = [];
                    foreach ($a_opciones as $a_vector) {
                        $aa_op[$a_vector['id_ubi']] = $a_vector['nombre_ubi'];
                    }
                    $oDesplCtr = new Desplegable();
                    $oDesplCtr->setBlanco(TRUE);
                    $oDesplCtr->setOpciones($aa_op);
                } else {
                    $oDesplCtr = new Desplegable();
                    $oDesplCtr->setOpciones([]);
                }
                break;
            default:
                $oDesplCtr = new Desplegable();
                $oDesplCtr->setOpciones([]);
        }

        return $oDesplCtr;
    }

    public function setIdZona(?int $id_zona): void
    {
        $this->id_zona = $id_zona;
    }
}