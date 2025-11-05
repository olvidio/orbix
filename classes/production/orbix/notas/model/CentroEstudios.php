<?php

namespace notas\model;

use core\ConfigDB;
use core\DBConnection;
use ubis\model\entity\Delegacion;

class CentroEstudios
{
    private object $oDbl;

    function __construct()
    {
        $oConfigDB = new ConfigDB('importar');
        // comun
        $configComunP = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($configComunP);
        $this->oDbl = $oConexion->getPDO();
    }

    public function getFromDl(array $aDl): string
    {
        $ce_lugar = '';

        foreach ($aDl as $id_dl) {
            //conseguir nombre del esquema de cada dl
            $Delegacion = new Delegacion($id_dl);
            $dl = $Delegacion->getDl();
            $region = $Delegacion->getRegion();

            $esquema = $region . '-' . $dl;


            $sQry = "SELECT valor
            FROM global.x_config_schema c JOIN public.db_idschema s ON (s.id = c.id_schema)
            WHERE s.schema = '$esquema' AND c.parametro = 'ce_lugar' ";

            if (($oDblSt = $this->oDbl->query($sQry)) === false) {
                $sClauError = 'CentroEstudios.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($this->oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $ce = $oDblSt->fetchColumn();
            if (!empty($ce)) {
                $ce_lugar .= empty($ce_lugar)? '' : ',';
                $ce_lugar .= $ce;
            }

        }

        return $ce_lugar;
    }
}