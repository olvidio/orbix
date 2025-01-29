<?php

namespace Tests\factories\tablonanuncios;

use Faker\Factory;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use tablonanuncios\domain\AnuncioId;
use tablonanuncios\domain\entity\Anuncio;

class AnunciosFactory
{
    private int $count = 1;
    private string $esquema_emisor;

    public function __construct()
    {
    }

    public function create($esquema_emisor='')
    {
        $this->esquema_emisor = $esquema_emisor;
        return $this->crear_Anuncios();
    }

    public function crear_Anuncios(): array
    {

        $faker = Factory::create();


        $count = $this->getCount() ?? 10; // número de notas

        $cAnuncios = [];
        for($i=0;$i <= $count;$i++) {

            $uuid_item = AnuncioId::random();
            $usuario_creador = 'dani';
            $cEsquemas = $faker->randomElements($this->a_esquemas, 2);
            $esquema_emisor = $cEsquemas[0];
            $esquema_destino = $cEsquemas[1];
            $idioma = 'ca.ES-utf8';
            $tablon = 'est';

            $f_anotado = $faker->dateTimeBetween()->format('Y-m-dTH:i:s'); // a date between -30 years ago, and now
            $tanotado = new DateTimeLocal($f_anotado); // a date between -30 years ago, and now
            $eliminado = $faker->boolean();
            if ($eliminado) {
                $f_eliminado = $faker->dateTimeBetween()->format('Y-m-dTH:i:s'); // a date between -30 years ago, and now
                $teliminado = new DateTimeLocal($f_eliminado); // a date between -30 years ago, and now
            } else {
                $teliminado = new NullDateTimeLocal();
            }
            $texto_anuncio = $faker->sentence(3);
            $categoria = $faker->numberBetween(1, 5);

            $Anuncio = new Anuncio();
            $Anuncio->setUuid_item($uuid_item);
            $Anuncio->setUsuarioCreador($usuario_creador);
            $Anuncio->setEsquemaEmisor($esquema_emisor);
            $Anuncio->setEsquemaDestino($esquema_destino);
            $Anuncio->setTextoAnuncio($texto_anuncio);
            $Anuncio->setIdioma($idioma);
            $Anuncio->setTablon($tablon);
            $Anuncio->setTanotado($tanotado);
            $Anuncio->setTeliminado($teliminado);
            $Anuncio->setCategoria($categoria);

            $cAnuncios[] = $Anuncio;
        }

        return $cAnuncios;
    }


    private array $a_esquemas = [
        "H-H",
        "H-crHv",
        "GalBel-crGalBelv",
        "M-crMv",
        "H-dlalv",
        "H-dlbv",
        "H-dlmEv",
        "H-dlmOv",
        "H-dlpv",
    ];


    public function setCount(int $count)
    {
        $this->count = $count;
    }

    private function getCount()
    {
        return $this->count;
    }

}