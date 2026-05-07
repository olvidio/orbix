<?php

namespace src\pasarela\domain;

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;

/**
 * Configuración del parámetro `nombre`.
 *
 * Encapsula excepciones de nombre por id_tipo_activ. Persistencia delegada en
 * {@see PasarelaConfigRepositoryInterface}. No genera HTML ni conoce la UI.
 */
class Nombre
{
    const PARAMETRO = 'nombre';

    private array $a_excepciones = [];

    public function __construct()
    {
        $this->get();
    }

    public function delNombre($id_tipo_activ): void
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addNombre($id_tipo_activ, $nombre_actividad): void
    {
        $this->a_excepciones[$id_tipo_activ] = $nombre_actividad;
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

    private function get(): void
    {
        $PasarelaConfigRepository = $GLOBALS['container']->get(PasarelaConfigRepositoryInterface::class);
        $oPasarelaConfig = $PasarelaConfigRepository->findById(self::PARAMETRO);
        $json_nombres = $oPasarelaConfig?->getJson_valor();
        if (empty((array)$json_nombres)) {
            $this->a_excepciones = [111000 => 'prova1', 111001 => 'prova2'];
        } else {
            $nombres = is_string($json_nombres) ? json_decode($json_nombres) : $json_nombres;
            $aaa = $nombres->excepciones ?? [];
            $this->a_excepciones = (array)$aaa;
        }
    }

    private function guardar(): void
    {
        $a_nombres['excepciones'] = $this->a_excepciones;

        $PasarelaConfigRepository = $GLOBALS['container']->get(PasarelaConfigRepositoryInterface::class);
        $oPasarelaConfig = $PasarelaConfigRepository->findById(self::PARAMETRO);
        if ($oPasarelaConfig === null) {
            $oPasarelaConfig = new PasarelaConfig();
            $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        }

        $oPasarelaConfig->setJson_valor($a_nombres);
        $PasarelaConfigRepository->Guardar($oPasarelaConfig);
    }
}
