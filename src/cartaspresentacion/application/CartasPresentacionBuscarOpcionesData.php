<?php

namespace src\cartaspresentacion\application;

use src\shared\config\ConfigGlobal;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\application\services\RegionDropdown;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use web\Hash;

/**
 * Data builder: opciones de formulario para la pantalla de busqueda de
 * cartas de presentacion (`frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php`).
 *
 * Sucesor de la parte de inicializacion de desplegables de
 * `apps/cartaspresentacion/controller/cartas_presentacion_buscar.php`.
 */
final class CartasPresentacionBuscarOpcionesData
{
    /**
     * @return array{
     *   opciones_region: array<string,string>,
     *   opciones_pais: array<string,string>,
     *   opciones_delegacion: array<string,string>,
     *   url_lista: string,
     *   hash_lista_html: string
     * }
     */
    public static function execute(): array
    {
        $repoDireccion = $GLOBALS['container']->get(DireccionCentroRepositoryInterface::class);

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $url_lista = $web . '/frontend/cartaspresentacion/controller/cartas_presentacion_lista.php';
        $oHash = new Hash();
        $oHash->setUrl($url_lista);
        $oHash->setArrayCamposHidden(['que' => 'get']);
        $oHash->setCamposForm('que!poblacion!region!pais!dl');
        $oHash->setCamposNo('scroll_id!sel');

        return [
            'opciones_region' => RegionDropdown::activasOrdenNombre(),
            'opciones_pais' => (array)$repoDireccion->getArrayPaises(),
            'opciones_delegacion' => DelegacionDropdown::byRegiones(['H']),
            'url_lista' => $url_lista,
            'hash_lista_html' => $oHash->getCamposHtml(),
        ];
    }
}
