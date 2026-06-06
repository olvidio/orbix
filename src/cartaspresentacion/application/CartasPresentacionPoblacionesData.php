<?php

namespace src\cartaspresentacion\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;

/**
 * Data builder: opciones del desplegable de poblaciones del modulo
 * `cartaspresentacion`, en funcion del filtro elegido en la pantalla
 * principal.
 *
 * Sucesor de la rama `que_mod=poblaciones` del dispatcher
 * `apps/cartaspresentacion/controller/cartas_presentacion_ajax.php`.
 *
 * El payload devuelto sigue el contrato estandar
 * (ver `refactor.md > Desplegables devueltos por endpoints AJAX`), de
 * modo que el frontend lo transforma con `fnjs_construir_desplegable`.
 */
final class CartasPresentacionPoblacionesData
{
    public function __construct(
        private DireccionCentroRepositoryInterface $direccionCentroRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private DireccionCentroDlRepositoryInterface $direccionCentroDlRepository,
        private RelacionCentroDlDireccionRepositoryInterface $relacionCentroDlDireccionRepository,
    ) {
    }

    /**
     * @param array{filtro?: string} $input
     * @return array{
     *   id: string,
     *   opciones: array<string,string>,
     *   selected: string,
     *   blanco: bool,
     *   val_blanco: string,
     *   action: string
     * }
     */
    public function execute(array $input): array
    {
        $filtro = $input['filtro'] ?? '';

        $aOpciones = match ($filtro) {
            'get_H' => $this->poblacionesPaisEspaña(),
            'get_r' => $this->poblacionesPaisExtranjero(),
            'get_dl' => $this->poblacionesDelegacion(),
            default => [],
        };

        return [
            'id' => 'poblacion_sel',
            'opciones' => $aOpciones,
            'selected' => '',
            'blanco' => true,
            'val_blanco' => '',
            'action' => '',
        ];
    }

    /**
     * @return array<string,string>
     */
    private function poblacionesPaisEspaña(): array
    {
        return (array)$this->direccionCentroRepository->getArrayPoblaciones("WHERE pais ILIKE 'españa'");
    }

    /**
     * @return array<string,string>
     */
    private function poblacionesPaisExtranjero(): array
    {
        return (array)$this->direccionCentroRepository->getArrayPoblaciones("WHERE pais NOT ILIKE 'españa'");
    }

    /**
     * @return array<string,string>
     */
    private function poblacionesDelegacion(): array
    {
        $cCentros = $this->centroDlRepository->getCentros();
        $poblaciones = [];
        foreach ($cCentros as $oCentro) {
            $aDirecciones = $this->relacionCentroDlDireccionRepository->getDireccionesPorUbi($oCentro->getId_ubi());
            foreach ($aDirecciones as $aDireccion) {
                $oDir = $this->direccionCentroDlRepository->findById((int)$aDireccion['id_direccion']);
                if ($oDir === null) {
                    continue;
                }
                $pob = (string)$oDir->getPoblacion();
                if ($pob !== '' && !isset($poblaciones[$pob])) {
                    $poblaciones[$pob] = $pob;
                }
            }
        }
        uksort($poblaciones, 'src\shared\domain\helpers\strsinacentocmp');
        return $poblaciones;
    }
}
