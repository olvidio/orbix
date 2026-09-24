<?php

declare(strict_types=1);

namespace src\notas\application;

use src\notas\domain\DestinoNotaExterno;
use src\personas\domain\entity\Persona;
use src\ubis\domain\RegionStgrAviso;

/**
 * Datos imprimibles de tessera ya serializados (sin objetos dominio → JSON estable).
 */
final class TesseraImprimirData
{
    public function __construct(
        private readonly Tesera $tesera,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_nom): array
    {
        if ($id_nom === 0) {
            return ['aviso' => RegionStgrAviso::mensajePersonaNoValida()];
        }

        $problemasRegionStgr = [];
        $oPersona = Persona::findPersonaEnGlobal($id_nom, $problemasRegionStgr);
        if ($oPersona === null) {
            return ['aviso' => sprintf(_('No encuentro persona con id_nom: %s'), (string)$id_nom)];
        }
        if ($oPersona->getId_schema() === 0 && !DestinoNotaExterno::esExternoPorIdNom($id_nom)) {
            RegionStgrAviso::registrarPersonaSinSchema(
                $problemasRegionStgr,
                $id_nom,
                (string)$oPersona->getPrefApellidosNombre(),
                (string)($oPersona->getDl() ?? ''),
            );

            return ['aviso' => RegionStgrAviso::formatear($problemasRegionStgr)];
        }

        $plan = $this->tesera->getPlan($id_nom);
        $cAsignaturas = $this->tesera->getAsignaturasPosibles($plan);
        $aAprobadas = $this->tesera->getAsignaturasAprobadas($id_nom, $plan);

        return [
            'nom' => $oPersona->getNombreApellidos(),
            'plan' => $plan,
            'filas' => $this->tesera->filasParaImpresion($cAsignaturas, $aAprobadas),
        ];
    }
}
