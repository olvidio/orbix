<?php

namespace src\actividadplazas\domain;

use src\actividadplazas\application\services\ResumenPlazasService;

/**
 * @deprecated Esta clase está deprecada. Use ResumenPlazasService en su lugar.
 *
 * Esta clase se mantiene temporalmente por compatibilidad con código legacy,
 * pero todos los métodos ahora delegan al servicio ResumenPlazasService que
 * cumple con los principios de DDD (capa de aplicación).
 *
 * RAZÓN DEL CAMBIO:
 * - ResumenPlazas no es una entidad de dominio
 * - Es un servicio de aplicación que orquesta múltiples repositorios
 * - Accede a configuración global y sesión
 * - Viola el principio de que domain/ no debe depender de infrastructure/
 *
 * @package orbix
 * @subpackage domain (DEPRECATED - mover a application)
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/11/2016
 * @deprecated 2026-01-02 Use ResumenPlazasService instead
 */
class ResumenPlazas
{
    private ResumenPlazasService $service;

    public function __construct()
    {
        $this->service = $GLOBALS['container']->get(ResumenPlazasService::class);
    }

    // Todos los métodos delegan al servicio para mantener compatibilidad
    public function setId_activ($iid_activ = '')
    {
        $this->service->setId_activ($iid_activ);
    }

    public function getPosiblesPropietarios($dl_de_paso = FALSE)
    {
        return $this->service->getPosiblesPropietarios($dl_de_paso);
    }

    public function getPlazasCalendario($dl)
    {
        return $this->service->getPlazasCalendario($dl);
    }

    public function getPlazasCedidas($dl)
    {
        return $this->service->getPlazasCedidas($dl);
    }

    public function getPlazasConseguidas($dl)
    {
        return $this->service->getPlazasConseguidas($dl);
    }

    public function getPlazasDisponibles($dl)
    {
        return $this->service->getPlazasDisponibles($dl);
    }

    public function getPlazasTotales()
    {
        return $this->service->getPlazasTotales();
    }

    public function getResumen()
    {
        return $this->service->getResumen();
    }

    public function getLibres(string $dl = '')
    {
        return $this->service->getLibres($dl);
    }

    public function getPropiedadPlazaLibre()
    {
        return $this->service->getPropiedadPlazaLibre();
    }
}
