<?php

namespace src\pasarela\domain;

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;

/**
 * Configuración del parámetro `contribucion_reserva`.
 */
class ContribucionReserva
{
    const PARAMETRO = 'contribucion_reserva';

    private ?int $default = null;
    /** @var array<int|string, int> */
    private array $a_excepciones = [];

    public function __construct(
        private readonly PasarelaConfigRepositoryInterface $pasarelaConfigRepository,
    ) {
        $this->get();
    }

    public function delContribucionReserva(int|string $id_tipo_activ): void
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addContribucionReserva(int|string $id_tipo_activ, int $contribucion_reserva): void
    {
        $this->a_excepciones[$id_tipo_activ] = $contribucion_reserva;
        $this->guardar();
    }

    /** @param array<int|string, int> $a_excepciones */
    public function setExcepciones(array $a_excepciones): void
    {
        $this->a_excepciones = $a_excepciones;
    }

    /** @return array<int|string, int> */
    public function getExcepciones(): array
    {
        return $this->a_excepciones;
    }

    public function setDefault(int $default): void
    {
        $this->default = $default;
        $this->guardar();
    }

    public function getDefault(): ?int
    {
        return $this->default;
    }

    private function get(): void
    {
        $oPasarelaConfig = $this->pasarelaConfigRepository->findById(self::PARAMETRO);
        $jsonData = $oPasarelaConfig?->getJson_valor(returnArray: true);
        if (!is_array($jsonData) || $jsonData === []) {
            $this->default = 0;
            $this->a_excepciones = [111000 => 45, 111001 => 63];
        } else {
            $raw = $jsonData['excepciones'] ?? [];
            $this->a_excepciones = [];
            if (is_array($raw)) {
                foreach ($raw as $key => $val) {
                    if (is_numeric($val)) {
                        $this->a_excepciones[$key] = (int)$val;
                    }
                }
            }
            $defaultRaw = $jsonData['default'] ?? null;
            $this->default = is_numeric($defaultRaw) ? (int)$defaultRaw : null;
        }
    }

    private function guardar(): void
    {
        $contribucion_reserva['default'] = $this->default;
        $contribucion_reserva['excepciones'] = $this->a_excepciones;

        $oPasarelaConfig = $this->pasarelaConfigRepository->findById(self::PARAMETRO);
        if ($oPasarelaConfig === null) {
            $oPasarelaConfig = new PasarelaConfig();
            $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        }

        $oPasarelaConfig->setJson_valor($contribucion_reserva);
        $this->pasarelaConfigRepository->Guardar($oPasarelaConfig);
    }
}
