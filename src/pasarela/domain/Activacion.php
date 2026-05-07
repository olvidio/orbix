<?php

namespace src\pasarela\domain;

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;
use stdClass;

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

    private $default;
    private array $a_excepciones = [];

    public function __construct()
    {
        $this->get();
    }

    public function delActivacion($id_tipo_activ): void
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addActivacion($id_tipo_activ, $activacion): void
    {
        $this->a_excepciones[$id_tipo_activ] = $activacion;
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
        $this->default = $default;
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
        $json_activacion = $oPasarelaConfig?->getJson_valor();
        if (empty((array)$json_activacion)) {
            $this->default = '3 días';
            $this->a_excepciones = [111000 => 'upload', 111001 => '5 días'];
        } else {
            $activacion = is_string($json_activacion) ? json_decode($json_activacion) : $json_activacion;
            $aaa = $activacion->excepciones ?? [];
            $this->a_excepciones = (array)$aaa;
            $this->default = $activacion->default ?? null;
        }
    }

    private function guardar(): void
    {
        $a_activacion['default'] = $this->default;
        $a_activacion['excepciones'] = $this->a_excepciones;

        $PasarelaConfigRepository = $GLOBALS['container']->get(PasarelaConfigRepositoryInterface::class);
        $oPasarelaConfig = $PasarelaConfigRepository->findById(self::PARAMETRO);
        if ($oPasarelaConfig === null) {
            $oPasarelaConfig = new PasarelaConfig();
            $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        }

        $oPasarelaConfig->setJson_valor($a_activacion);
        $PasarelaConfigRepository->Guardar($oPasarelaConfig);
    }
}