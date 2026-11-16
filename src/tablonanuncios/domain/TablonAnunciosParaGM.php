<?php

namespace src\tablonanuncios\domain;

use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\security\HashB;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\criteria\AnuncioField;

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
        $cAnuncios = $this->anuncioRepository->matching(
            Criteria::create()
                ->where(Filter::matchesInsensitive(AnuncioField::Tablon, $tablon))
                ->andWhere(Filter::eq(AnuncioField::EsquemaDestino, ConfigGlobal::mi_region_dl()))
                ->andWhere(Filter::isNull(AnuncioField::TEliminado))
        );
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

            $a_valores[$i]['sel'] = HashB::sign('anuncio_delete', [
                'uuid_item' => $uuid_item->value(),
            ]);
            $a_valores[$i][1] = $esquema_emisor;
            $a_valores[$i][2] = $t_anotado instanceof DateTimeLocal ? $t_anotado->getFromLocal() : '';
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

    /**
     * Datos de la tabla del tablón; la monta `public/portada.php` con `ListaDesdeDatos`.
     *
     * @return array{id_tabla: string, cabeceras: list<mixed>, botones: list<array{txt: string, click: string}>, datos: array<int, array<int|string, mixed>>}
     */
    public function getTabla(string $tablon): array
    {
        return [
            'id_tabla' => 'tablon_anuncios',
            'cabeceras' => [
                _("origen"),
                ['name' => ucfirst(_("fecha")), 'class' => 'fecha'],
                _("aviso"),
            ],
            'botones' => [['txt' => _("borrar"), 'click' => 'fnjs_borrar("#seleccionados")']],
            'datos' => $this->getArray($tablon),
        ];
    }
}
