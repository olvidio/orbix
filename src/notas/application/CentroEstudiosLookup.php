<?php

declare(strict_types=1);

namespace src\notas\application;

use PDO;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;

/**
 * Resuelve la lista de `ce_lugar` asociadas a una coleccion de delegaciones
 * (informes STGR, asignaturas pendientes). Consulta la tabla de configuracion
 * por esquema y concatena los valores encontrados separados por coma.
 */
final class CentroEstudiosLookup
{
    private PDO $oDbl;

    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
    ) {
        $oConfigDB = new ConfigDB('importar');
        $configComunP = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($configComunP);
        $this->oDbl = $oConexion->getPDO();
    }

    /**
     * @param array<int|string> $aDl
     */
    public function getFromDl(array $aDl): string
    {
        $ce_lugar = '';
        foreach ($aDl as $id_dl) {
            if (!is_numeric($id_dl)) {
                continue;
            }
            $Delegacion = $this->delegacionRepository->findById((int) $id_dl);
            if (!$Delegacion instanceof Delegacion) {
                continue;
            }
            $dl = $Delegacion->getDlVo()->value();
            $region = $Delegacion->getRegionVo()->value();
            $esquema = $region . '-' . $dl;

            $sQry = "SELECT valor
                FROM global.x_config_schema c JOIN public.db_idschema s ON (s.id = c.id_schema)
                WHERE s.schema = '$esquema' AND c.parametro = 'ce_lugar' ";

            $oDblSt = $this->oDbl->query($sQry);
            if ($oDblSt === false) {
                $gestor = $_SESSION['oGestorErrores'] ?? null;
                if (is_object($gestor) && method_exists($gestor, 'addErrorAppLastError')) {
                    $gestor->addErrorAppLastError($this->oDbl, 'CentroEstudiosLookup.query', __LINE__, __FILE__);
                }
                return '';
            }
            $ce = $oDblSt->fetchColumn();
            if (is_string($ce) && $ce !== '') {
                $ce_lugar .= $ce_lugar === '' ? '' : ',';
                $ce_lugar .= $ce;
            }
        }

        return $ce_lugar;
    }
}
