<?php

namespace src\cartaspresentacion\application;

use src\shared\config\ConfigGlobal;
use web\Hash;

/**
 * URLs y fragmentos Hash para la shell `cartas_presentacion.php`.
 */
final class CartasPresentacionShellData
{
    /**
     * @return array<string, string>
     */
    public static function build(): array
    {
        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $mi_dele = ConfigGlobal::mi_delef();

        $url_ctr = $web . '/frontend/ubis/controller/home_ubis.php';
        $oHashCtr = new Hash();
        $oHashCtr->setUrl($url_ctr);
        $oHashCtr->setCamposForm('bloque!pau!id_ubi');
        $h_ctr = $oHashCtr->linkSinValParams();

        $url_lista = $web . '/frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php';
        $oHashLista = new Hash();
        $oHashLista->setUrl($url_lista);
        $oHashLista->setCamposForm('tipo_lista');
        $oHashLista->setCamposNo('scroll_id!sel!poblacion_sel');
        $hash_lista_html = $oHashLista->getCamposHtml();

        $url_form = $web . '/frontend/cartaspresentacion/controller/cartas_presentacion_form.php';
        $oHashForm = new Hash();
        $oHashForm->setUrl($url_form);
        $oHashForm->setCamposForm('id_direccion!id_ubi');
        $h_form = $oHashForm->linkSinVal();

        $url_poblaciones = $web . '/src/cartaspresentacion/poblaciones_data';
        $oHashPob = new Hash();
        $oHashPob->setUrl($url_poblaciones);
        $oHashPob->setCamposForm('filtro');
        $h_poblaciones = $oHashPob->linkSinValParams();

        $oHashEliminar = new Hash();
        $oHashEliminar->setUrl($web . '/src/cartaspresentacion/carta_presentacion_eliminar');
        $oHashEliminar->setCamposForm('id_ubi!id_direccion');
        $h_eliminar = $oHashEliminar->linkSinValParams();

        return [
            'mi_dele' => $mi_dele,
            'url_ctr' => $url_ctr,
            'h_ctr' => $h_ctr,
            'url_lista' => $url_lista,
            'hash_lista_html' => $hash_lista_html,
            'url_form' => $url_form,
            'h_form' => $h_form,
            'url_poblaciones' => $url_poblaciones,
            'h_poblaciones' => $h_poblaciones,
            'url_update' => $web . '/src/cartaspresentacion/carta_presentacion_update',
            'url_eliminar' => $web . '/src/cartaspresentacion/carta_presentacion_eliminar',
            'h_eliminar' => $h_eliminar,
        ];
    }
}
