<?php

namespace Tests\factories\certificados;

use Faker\Factory;
use src\certificados\domain\entity\CertificadoRecibido;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

/**
 * Factory para crear instancias de CertificadoRecibido para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CertificadoRecibidoFactory
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
     * Crea una instancia simple de CertificadoRecibido con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CertificadoRecibido
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCertificadoRecibido = new CertificadoRecibido();
        $oCertificadoRecibido->setId_item($id);

        $oCertificadoRecibido->setF_certificado(new DateTimeLocal('2024-01-01'));
        $oCertificadoRecibido->setF_recibido(new DateTimeLocal('2024-01-01'));

        return $oCertificadoRecibido;
    }

    /**
     * Crea una instancia de CertificadoRecibido con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CertificadoRecibido
     */
    public function create(?int $id = null): CertificadoRecibido
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCertificadoRecibido = new CertificadoRecibido();
        $oCertificadoRecibido->setId_item($id);

        $oCertificadoRecibido->setId_nom($faker->numberBetween(10011, 100000));
        $oCertificadoRecibido->setNom($faker->name);
        $oCertificadoRecibido->setIdioma($faker->locale() . ".UTF-8");
        $oCertificadoRecibido->setDestino($faker->text);
        $oCertificadoRecibido->setCertificado($faker->text(50));
        $oCertificadoRecibido->setF_certificado(new DateTimeLocal($faker->date()));
        $oCertificadoRecibido->setEsquema_emisor($faker->text(20));
        $oCertificadoRecibido->setFirmado($faker->boolean);
        $oCertificadoRecibido->setDocumento($faker->word);
        $oCertificadoRecibido->setF_recibido(new DateTimeLocal($faker->date()));

        return $oCertificadoRecibido;
    }

    /**
     * Crea múltiples instancias de CertificadoRecibido
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
