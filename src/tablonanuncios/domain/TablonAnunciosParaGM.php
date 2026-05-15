<?php

namespace src\tablonanuncios\domain;

use src\shared\config\ConfigGlobal;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use frontend\shared\web\Lista;

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
            't_eliminado' => 'x',
        ];
        $aOperador = [
            'tablon' => '~*',
            't_eliminado' => 'IS NULL'
        ];
        $cAnuncios = $AnuncioRepository->getAnuncios($aWhere, $aOperador);
        $a_valores = [];
        $i = 0;
        foreach ($cAnuncios as $Anuncio) {
            $i++;
            $uuid_item = $Anuncio->getUuid_item();
            $esquema_emisor = $Anuncio->getEsquemaEmisorVo()->value();
            $texto_anuncio = $Anuncio->getTextoAnuncioVo()->value();
            $t_anotado = $Anuncio->getT_anotado();

            // sólo puede ver que està ocupado
            $a_valores[$i]['sel'] = $uuid_item;
            $a_valores[$i][1] = $esquema_emisor;
            $a_valores[$i][2] = $t_anotado->getFromLocal();
            $a_valores[$i][3] = $texto_anuncio;

            // para poder ordenar por fecha
            $a_FechaIni[$i] = $t_anotado->getIso();
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