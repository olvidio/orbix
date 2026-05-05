<?php

namespace src\pasarela\domain;

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;
use stdClass;

/**
 * Configuración del parámetro `contribucion_reserva`.
 *
 * Encapsula default + excepciones por id_tipo_activ. Persistencia delegada en
 * {@see PasarelaConfigRepositoryInterface}. No genera HTML ni conoce la UI.
 */
class ContribucionReserva
{
    const PARAMETRO = 'contribucion_reserva';

    private $default;
    private array $a_excepciones = [];

    public function __construct()
    {
        $this->get();
    }

    public function delContribucionReserva($id_tipo_activ): void
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addContribucionReserva($id_tipo_activ, $contribucion_reserva): void
    {
        $this->a_excepciones[$id_tipo_activ] = $contribucion_reserva;
        $this->guardar();
    }

    public function setExcepciones(array $a_excepciones): void
    {
        $this->a_excepciones = $a_excepciones;
    }

    public function getExcepciones(): array
    {
        return $this->a_excepciones;
    }

    public function setDefault($default): void
    {
        $this->default = (int)$default;
        $this->guardar();
    }

    public function getDefault()
    {
        return $this->default;
    }

    private function get(): void
    {
        $PasarelaConfigRepository = $GLOBALS['container']->get(PasarelaConfigRepositoryInterface::class);
        $oPasarelaConfig = $PasarelaConfigRepository->findById(self::PARAMETRO);
        $json_contribucion_reserva = $oPasarelaConfig?->getJson_valor();
        if (empty((array)$json_contribucion_reserva)) {
            $this->default = 0;
            $this->a_excepciones = [111000 => 45, 111001 => 63];
        } else {
            $contribucion_reserva = is_string($json_contribucion_reserva) ? json_decode($json_contribucion_reserva) : $json_contribucion_reserva;
            $aaa = $contribucion_reserva->excepciones ?? [];
            $this->a_excepciones = (array)$aaa;
            $this->default = $contribucion_reserva->default ?? null;
        }
    }

    private function guardar(): void
    {
        $contribucion_reserva = new stdClass();
        $contribucion_reserva->default = $this->default;
        $contribucion_reserva->excepciones = $this->a_excepciones;

        $PasarelaConfigRepository = $GLOBALS['container']->get(PasarelaConfigRepositoryInterface::class);
        $oPasarelaConfig = $PasarelaConfigRepository->findById(self::PARAMETRO);
        if ($oPasarelaConfig === null) {
            $oPasarelaConfig = new PasarelaConfig();
            $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        }

        $oPasarelaConfig->setJson_valor($contribucion_reserva);
        $PasarelaConfigRepository->Guardar($oPasarelaConfig);
    }
}
