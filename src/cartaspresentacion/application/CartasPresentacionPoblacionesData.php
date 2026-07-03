<?php

namespace src\cartaspresentacion\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\shared\domain\helpers\OpcionesDesplegable;

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
     *   opciones: list<array{0: string, 1: string}>,
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
            'opciones' => OpcionesDesplegable::enOrden($aOpciones),
            'selected' => '',
            'blanco' => true,
            'val_blanco' => '',
            'action' => '',
            'clase' => 'contenido',
        ];
    }

    /**
     * @return array<string,string>
     */
    private function poblacionesPaisEspaña(): array
    {
        return (array) $this->stringKeyOptions((array) $this->direccionCentroRepository->getArrayPoblaciones("WHERE pais ILIKE 'españa'"));
    }

    /**
     * @return array<string,string>
     */
    private function poblacionesPaisExtranjero(): array
    {
        return (array) $this->stringKeyOptions((array) $this->direccionCentroRepository->getArrayPoblaciones("WHERE pais NOT ILIKE 'españa'"));
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
            if ($aDirecciones === false) {
                continue;
            }
            foreach ($aDirecciones as $aDireccion) {
                $idDirRaw = $aDireccion['id_direccion'] ?? null;
                if (!is_numeric($idDirRaw)) {
                    continue;
                }
                $oDir = $this->direccionCentroDlRepository->findById((int) $idDirRaw);
                if ($oDir === null) {
                    continue;
                }
                $pob = (string)$oDir->getPoblacion();
                if ($pob !== '' && !isset($poblaciones[$pob])) {
                    $poblaciones[$pob] = $pob;
                }
            }
        }
        uksort($poblaciones, [\src\shared\domain\helpers\FuncTablasSupport::class, 'strsinacentocmp']);
        return $poblaciones;
    }

    /**
     * @param array<int|string, string> $options
     * @return array<string, string>
     */
    private function stringKeyOptions(array $options): array
    {
        $result = [];
        foreach ($options as $key => $value) {
            $result[(string) $key] = (string) $value;
        }

        return $result;
    }
}
