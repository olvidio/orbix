<?php

namespace src\pasarela\domain;

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;
use stdClass;

/**
 * Configuración del parámetro `contribucion_no_duerme`.
 *
 * Encapsula default + excepciones por id_tipo_activ. Persistencia delegada en
 * {@see PasarelaConfigRepositoryInterface}. No genera HTML ni conoce la UI.
 */
class ContribucionNoDuerme
{
    const PARAMETRO = 'contribucion_no_duerme';

    private $default;
    private array $a_excepciones = [];

    public function __construct()
    {
        $this->get();
    }

    public function delContribucionNoDuerme($id_tipo_activ): void
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addContribucionNoDuerme($id_tipo_activ, $contribucion_no_duerme): void
    {
        $this->a_excepciones[$id_tipo_activ] = $contribucion_no_duerme;
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
        $json_contribucion_no_duerme = $oPasarelaConfig?->getJson_valor();
        if (empty((array)$json_contribucion_no_duerme)) {
            $this->default = 85;
            $this->a_excepciones = [111000 => 45, 111001 => 63];
        } else {
            $contribucion_no_duerme = is_string($json_contribucion_no_duerme) ? json_decode($json_contribucion_no_duerme) : $json_contribucion_no_duerme;
            $aaa = $contribucion_no_duerme->excepciones ?? [];
            $this->a_excepciones = (array)$aaa;
            $this->default = $contribucion_no_duerme->default ?? null;
        }
    }

    private function guardar(): void
    {
        $contribucion_no_duerme['default'] = $this->default;
        $contribucion_no_duerme['excepciones'] = $this->a_excepciones;

        $PasarelaConfigRepository = $GLOBALS['container']->get(PasarelaConfigRepositoryInterface::class);
        $oPasarelaConfig = $PasarelaConfigRepository->findById(self::PARAMETRO);
        if ($oPasarelaConfig === null) {
            $oPasarelaConfig = new PasarelaConfig();
            $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        }

        $oPasarelaConfig->setJson_valor($contribucion_no_duerme);
        $PasarelaConfigRepository->Guardar($oPasarelaConfig);
    }
}
