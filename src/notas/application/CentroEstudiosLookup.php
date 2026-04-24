<?php

namespace src\notas\application;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Resuelve la lista de `ce_lugar` asociadas a una coleccion de delegaciones
 * (informes STGR, asignaturas pendientes). Consulta la tabla de configuracion
 * por esquema y concatena los valores encontrados separados por coma.
 */
final class CentroEstudiosLookup
{
    private object $oDbl;

    public function __construct()
    {
        $oConfigDB = new ConfigDB('importar');
        $configComunP = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($configComunP);
        $this->oDbl = $oConexion->getPDO();
    }

    public function getFromDl(array $aDl): string
    {
        $ce_lugar = '';
        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        foreach ($aDl as $id_dl) {
            $Delegacion = $DelegacionRepository->findById($id_dl);
            $dl = $Delegacion->getDlVo()?->value() ?? '';
            $region = $Delegacion->getRegionVo()?->value() ?? '';
            $esquema = $region . '-' . $dl;

            $sQry = "SELECT valor
                FROM global.x_config_schema c JOIN public.db_idschema s ON (s.id = c.id_schema)
                WHERE s.schema = '$esquema' AND c.parametro = 'ce_lugar' ";

            if (($oDblSt = $this->oDbl->query($sQry)) === false) {
                $sClauError = 'CentroEstudiosLookup.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($this->oDbl, $sClauError, __LINE__, __FILE__);
                return '';
            }
            $ce = $oDblSt->fetchColumn();
            if (!empty($ce)) {
                $ce_lugar .= empty($ce_lugar) ? '' : ',';
                $ce_lugar .= $ce;
            }
        }

        return $ce_lugar;
    }
}
