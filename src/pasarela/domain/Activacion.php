<?php

namespace src\pasarela\domain;

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;

/**
 * Configuración del parámetro `fecha_activacion`.
 *
 * Encapsula default + excepciones por id_tipo_activ. Persistencia delegada en
 * {@see PasarelaConfigRepositoryInterface}. No genera HTML ni conoce la UI;
 * cualquier presentación se construye en `application/` o en `frontend/`.
 */
class Activacion
{
    const PARAMETRO = 'fecha_activacion';

    private int|string|null $default = null;
    /** @var array<int|string, int|string> */
    private array $a_excepciones = [];

    public function __construct(
        private readonly PasarelaConfigRepositoryInterface $pasarelaConfigRepository,
    ) {
        $this->get();
    }

    public function delActivacion(int|string $id_tipo_activ): void
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addActivacion(int|string $id_tipo_activ, int|string $activacion): void
    {
        $this->a_excepciones[$id_tipo_activ] = $activacion;
        $this->guardar();
    }

    /** @param array<int|string, int|string> $a_excepciones */
    public function setExcepciones(array $a_excepciones): void
    {
        $this->a_excepciones = $a_excepciones;
    }

    /** @return array<int|string, int|string> */
    public function getExcepciones(): array
    {
        return $this->a_excepciones;
    }

    public function setDefault(int|string|null $default): void
    {
        $this->default = $default;
        $this->guardar();
    }

    public function getDefault(): int|string|null
    {
        return $this->default;
    }

    private function get(): void
    {
        
        $oPasarelaConfig = $this->pasarelaConfigRepository->findById(self::PARAMETRO);
        $jsonData = $oPasarelaConfig?->getJson_valor(returnArray: true);
        if (!is_array($jsonData) || $jsonData === []) {
            $this->default = '3 días';
            $this->a_excepciones = [111000 => 'upload', 111001 => '5 días'];
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
            $default = $jsonData['default'] ?? null;
            $this->default = is_scalar($default) ? (string)$default : null;
        }
    }

    private function guardar(): void
    {
        $a_activacion['default'] = $this->default;
        $a_activacion['excepciones'] = $this->a_excepciones;

        
        $oPasarelaConfig = $this->pasarelaConfigRepository->findById(self::PARAMETRO);
        if ($oPasarelaConfig === null) {
            $oPasarelaConfig = new PasarelaConfig();
            $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        }

        $oPasarelaConfig->setJson_valor($a_activacion);
        $this->pasarelaConfigRepository->Guardar($oPasarelaConfig);
    }
}