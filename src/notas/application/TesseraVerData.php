<?php

declare(strict_types=1);

namespace src\notas\application;

use src\personas\domain\entity\Persona;
use src\ubis\domain\RegionStgrAviso;

/**
 * Dataset JSON para {@see frontend/notas/view/tesera_ver.phtml} (sin usar `Tesera` en frontend).
 */
final class TesseraVerData
{
    /**
     * @return array<string, mixed>
     */
    public static function execute(int $id_nom): array
    {
        if ($id_nom <= 0) {
            return self::respuestaConAviso(RegionStgrAviso::mensajePersonaNoValida());
        }

        $problemasRegionStgr = [];
        $oPersona = Persona::findPersonaEnGlobal($id_nom, $problemasRegionStgr);
        if ($oPersona === null) {
            return self::respuestaConAviso(sprintf(
                _('No encuentro persona con id_nom: %s'),
                (string)$id_nom
            ));
        }
        if ($oPersona->getId_schema() === 0) {
            RegionStgrAviso::registrarPersonaSinSchema(
                $problemasRegionStgr,
                $id_nom,
                (string)$oPersona->getPrefApellidosNombre(),
                (string)($oPersona->getDl() ?? ''),
            );

            return self::respuestaConAviso(RegionStgrAviso::formatear($problemasRegionStgr));
        }

        $tesera = new Tesera();

        return $tesera->datosParaVistaTesera($id_nom);
    }

    /**
     * @return array<string, mixed>
     */
    private static function respuestaConAviso(string $aviso): array
    {
        return [
            'aviso' => $aviso,
            'ap_nom' => '',
            'centro' => '',
            'tabla' => [],
            'numasig' => 0,
            'num_asig_total' => 0,
            'numasig_year' => 0,
            'numcred' => 0.0,
            'num_creditos_total' => 0.0,
            'curso_txt' => '',
            'numcred_year' => 0.0,
        ];
    }
}
