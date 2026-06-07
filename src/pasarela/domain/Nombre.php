<?php

namespace src\pasarela\domain;

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;

/**
 * Configuración del parámetro `nombre`.
 */
class Nombre
{
    const PARAMETRO = 'nombre';

    /** @var array<int|string, string> */
    private array $a_excepciones = [];

    public function __construct(
        private readonly PasarelaConfigRepositoryInterface $pasarelaConfigRepository,
    ) {
        $this->get();
    }

    public function delNombre(int|string $id_tipo_activ): void
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addNombre(int|string $id_tipo_activ, string $nombre_actividad): void
    {
        $this->a_excepciones[$id_tipo_activ] = $nombre_actividad;
        $this->guardar();
    }

    /** @param array<int|string, string> $a_excepciones */
    public function setExcepciones(array $a_excepciones): void
    {
        $this->a_excepciones = $a_excepciones;
    }

    /** @return array<int|string, string> */
    public function getExcepciones(): array
    {
        return $this->a_excepciones;
    }

    private function get(): void
    {
        $oPasarelaConfig = $this->pasarelaConfigRepository->findById(self::PARAMETRO);
        $jsonData = $oPasarelaConfig?->getJson_valor(returnArray: true);
        if (!is_array($jsonData) || $jsonData === []) {
            $this->a_excepciones = [111000 => 'prova1', 111001 => 'prova2'];
        } else {
            $raw = $jsonData['excepciones'] ?? [];
            $this->a_excepciones = [];
            if (is_array($raw)) {
                foreach ($raw as $key => $val) {
                    if (is_scalar($val)) {
                        $this->a_excepciones[$key] = (string)$val;
                    }
                }
            }
        }
    }

    private function guardar(): void
    {
        $a_nombres['excepciones'] = $this->a_excepciones;

        $oPasarelaConfig = $this->pasarelaConfigRepository->findById(self::PARAMETRO);
        if ($oPasarelaConfig === null) {
            $oPasarelaConfig = new PasarelaConfig();
            $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        }

        $oPasarelaConfig->setJson_valor($a_nombres);
        $this->pasarelaConfigRepository->Guardar($oPasarelaConfig);
    }
}
