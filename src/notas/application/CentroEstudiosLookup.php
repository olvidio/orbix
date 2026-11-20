<?php

declare(strict_types=1);

namespace src\notas\application;

use PDO;
use src\shared\infrastructure\GlobalPdo;
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
        $this->oDbl = GlobalPdo::get('oDBPC');
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

            // Conexión de solo consulta (`public` de comun) y esquema enlazado: antes se interpolaba la
            // sigla en la consulta y se lanzaba con la conexión de mantenimiento.
            $oDblSt = $this->oDbl->prepare(
                "SELECT valor
                FROM global.x_config_schema c JOIN public.db_idschema s ON (s.id = c.id_schema)
                WHERE s.schema = :esquema AND c.parametro = 'ce_lugar'"
            );
            $oDblSt->execute(['esquema' => $esquema]);
            $ce = $oDblSt->fetchColumn();
            if (is_string($ce) && $ce !== '') {
                $ce_lugar .= $ce_lugar === '' ? '' : ',';
                $ce_lugar .= $ce;
            }
        }

        return $ce_lugar;
    }
}
