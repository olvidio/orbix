<?php

namespace Tests\factories\tablonanuncios;

use Faker\Factory;
use src\tablonanuncios\domain\entity\Anuncio;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\tablonanuncios\domain\value_objects\AnuncioId;
use src\tablonanuncios\domain\value_objects\Categoria;
use src\tablonanuncios\domain\value_objects\EsquemaDestino;
use src\tablonanuncios\domain\value_objects\EsquemaEmisor;
use src\tablonanuncios\domain\value_objects\Idioma;
use src\tablonanuncios\domain\value_objects\Tablon;
use src\tablonanuncios\domain\value_objects\TextoAnuncio;
use src\tablonanuncios\domain\value_objects\UsuarioCreador;

/**
 * Factory para crear instancias de Anuncio para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class AnuncioFactory
{
    private int $count = 1;

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Crea una instancia simple de Anuncio con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?string $id = null): Anuncio
    {
        $uuid_itemVo = $id ?? AnuncioId::random();
        $oAnuncio = new Anuncio();
        $oAnuncio->setUuid_item($uuid_itemVo);

        $oAnuncio->setUsuarioCreadorVo('testDani');
        $oAnuncio->setEsquemaEmisorVo(new EsquemaEmisor('test_esquema_emisor'));
        $oAnuncio->setEsquemaDestinoVo(new EsquemaDestino('test_esquema_destino'));
        $oAnuncio->setTextoAnuncioVo(new TextoAnuncio('test_texto_anuncio_vo'));
        $oAnuncio->setIdiomaVo(new Idioma('ca_ES-utf8'));
        $oAnuncio->setTablonVo(new Tablon('est'));
        $oAnuncio->setT_anotado(new DateTimeLocal('2025-10-09 10:34:45'));
        $oAnuncio->setCategoriaVo(new Categoria(1));

        return $oAnuncio;
    }

    /**
     * Crea una instancia de Anuncio con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Anuncio
     */
    public function create(?string $id = null): Anuncio
    {
        $faker = Factory::create('es_ES');
        $uuid_itemVo = $id ?? AnuncioId::random();
        $idiomaTest = $faker->locale() . '.UTF-8';

        $oAnuncio = new Anuncio();
        $oAnuncio->setUuid_item($uuid_itemVo);

        $oAnuncio->setUsuarioCreadorVo(substr($faker->name, 0, 20));
        $oAnuncio->setEsquemaEmisorVo(new EsquemaEmisor(substr($faker->word, 0, 20)));
        $oAnuncio->setEsquemaDestinoVo(new EsquemaDestino(substr($faker->word, 0, 20)));
        $oAnuncio->setTextoAnuncioVo(new TextoAnuncio($faker->word));
        $oAnuncio->setIdiomaVo(new Idioma($idiomaTest));
        $oAnuncio->setTablonVo(new Tablon($faker->word));
        $oAnuncio->setT_anotado(new DateTimeLocal($faker->date()));
        $oAnuncio->setT_eliminado(new DateTimeLocal($faker->date()));
        $oAnuncio->setCategoriaVo(new Categoria($faker->numberBetween(1, 2)));

        return $oAnuncio;
    }

    /**
     * Crea múltiples instancias de Anuncio
     * @param int $count Número de instancias a crear
     * @param int|null $startId ID inicial (se incrementará)
     * @return array
     */
    public function createMany(int $count, ?int $startId = null): array
    {
        $startId = $startId ?? (9900000 + random_int(1000, 9999));
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create($startId + $i);
        }

        return $instances;
    }
}
