<?php

namespace src\tablonanuncios\domain;

use frontend\shared\web\Lista;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;

class TablonAnunciosParaGM
{
    public function __construct(
        private AnuncioRepositoryInterface $anuncioRepository,
    ) {
    }

    /**
     * @return array<int, array<int|string, mixed>>
     */
    public function getArray(string $tablon): array
    {
        $aWhere = [
            'tablon' => $tablon,
            'esquema_destino' => ConfigGlobal::mi_region_dl(),
            't_eliminado' => 'x',
        ];
        $aOperador = [
            'tablon' => '~*',
            't_eliminado' => 'IS NULL',
        ];
        $cAnuncios = $this->anuncioRepository->getAnuncios($aWhere, $aOperador);
        $a_valores = [];
        /** @var list<string> $a_FechaIni */
        $a_FechaIni = [];
        $i = 0;
        foreach ($cAnuncios as $Anuncio) {
            $i++;
            $uuid_item = $Anuncio->getUuid_item();
            $esquema_emisor = $Anuncio->getEsquemaEmisorVo()->value();
            $texto_anuncio = $Anuncio->getTextoAnuncioVo()->value();
            $t_anotado = $Anuncio->getT_anotado();

            $a_valores[$i]['sel'] = $uuid_item;
            $a_valores[$i][1] = $esquema_emisor;
            $a_valores[$i][2] = $t_anotado->getFromLocal();
            $a_valores[$i][3] = $texto_anuncio;

            $a_FechaIni[$i] = $t_anotado instanceof DateTimeLocal ? $t_anotado->getIso() : '';
        }
        if ($a_valores !== []) {
            array_multisort(
                $a_FechaIni,
                SORT_STRING,
                $a_valores,
            );
        }

        return $a_valores;
    }

    public function getTabla(string $tablon): Lista
    {
        $a_botones[] = ['txt' => _('borrar'), 'click' => 'fnjs_borrar("#seleccionados")'];

        $a_cabeceras = [
            _('origen'),
            ['name' => ucfirst(_('fecha')), 'class' => 'fecha'],
            _('aviso'),
        ];
        $a_valores = $this->getArray($tablon);

        $oTabla = new Lista();
        $oTabla->setId_tabla('tablon_anuncios');
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setBotones($a_botones);
        $oTabla->setDatos($a_valores);

        return $oTabla;
    }
}
