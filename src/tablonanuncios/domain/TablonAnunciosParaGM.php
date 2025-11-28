<?php

namespace src\tablonanuncios\domain;

use core\ConfigGlobal;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use web\Lista;

class TablonAnunciosParaGM
{
    private string $tablon;

    public function __construct($tablon)
    {
        $this->tablon = $tablon;
    }

    public function getArray(): array
    {
        $AnuncioRepository = $GLOBALS['container']->get(AnuncioRepositoryInterface::class);
        $aWhere = [
            'tablon' => $this->tablon,
            'esquema_destino' => ConfigGlobal::mi_region_dl(),
            'teliminado' => 'x',
        ];
        $aOperador = [
            'tablon' => '~*',
            'teliminado' => 'IS NULL'
        ];
        $cAnuncios = $AnuncioRepository->getAnuncios($aWhere, $aOperador);
        $a_valores = [];
        $i = 0;
        foreach ($cAnuncios as $Anuncio) {
            $i++;
            $uuid_item = $Anuncio->getUuid_item();
            $usuario_creador = $Anuncio->getUsuarioCreador();
            $esquema_emisor = $Anuncio->getEsquemaEmisor();
            $esquema_destino = $Anuncio->getEsquemaDestino();
            $texto_anuncio = $Anuncio->getTextoAnuncio();
            $idioma = $Anuncio->getIdioma();
            $tablon = $Anuncio->getTablon();
            $tanotado = $Anuncio->getTanotado();
            $teliminado = $Anuncio->getTeliminado();
            $categoria = $Anuncio->getCategoria();

            // sólo puede ver que està ocupado
            $a_valores[$i]['sel'] = $uuid_item;
            $a_valores[$i][1] = $esquema_emisor;
            $a_valores[$i][2] = $tanotado->getFromLocal();
            $a_valores[$i][3] = $texto_anuncio;

            // para poder ordenar por fecha
            $a_FechaIni[$i] = $tanotado->getIso();
        }
        if (!empty($a_valores)) {
            array_multisort(
                $a_FechaIni, SORT_STRING,
                $a_valores);
        }

        return $a_valores;
    }

    public function getTabla()
    {

        $a_botones[] = ['txt' => _("borrar"), 'click' => "fnjs_borrar(\"#seleccionados\")"];

        $a_cabeceras = [
            _("origen"),
            ['name' => ucfirst(_("fecha")), 'class' => 'fecha'],
            _("aviso"),
        ];
        $a_valores = $this->getArray();

        $oTabla = new Lista();
        $oTabla->setId_tabla('tablon_anuncios');
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setBotones($a_botones);
        $oTabla->setDatos($a_valores);

        return $oTabla;
    }
}