<?php

namespace Tests\factories\certificados;

use Faker\Factory;
use src\certificados\domain\entity\CertificadoEmitido;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

/**
 * Factory para crear instancias de CertificadoEmitido para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CertificadoEmitidoFactory
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
     * Crea una instancia simple de CertificadoEmitido con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CertificadoEmitido
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCertificadoEmitido = new CertificadoEmitido();
        $oCertificadoEmitido->setId_item($id);

        $oCertificadoEmitido->setF_certificado(new DateTimeLocal('2024-01-01'));
        $oCertificadoEmitido->setFirmado(false);
        $oCertificadoEmitido->setF_enviado(new DateTimeLocal('2024-01-01'));

        return $oCertificadoEmitido;
    }

    /**
     * Crea una instancia de CertificadoEmitido con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CertificadoEmitido
     */
    public function create(?int $id = null): CertificadoEmitido
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCertificadoEmitido = new CertificadoEmitido();
        $oCertificadoEmitido->setId_item($id);

        $oCertificadoEmitido->setId_nom($faker->numberBetween(10011, 100000));
        $oCertificadoEmitido->setNom($faker->name());
        $oCertificadoEmitido->setIdioma($faker->locale() . ".UTF-8");
        $oCertificadoEmitido->setDestino($faker->text);
        $oCertificadoEmitido->setCertificado($faker->text(50));
        $oCertificadoEmitido->setF_certificado(new DateTimeLocal($faker->date()));
        $oCertificadoEmitido->setEsquema_emisor($faker->text(50));
        $oCertificadoEmitido->setFirmado($faker->boolean);
        $oCertificadoEmitido->setDocumento($faker->word);
        $oCertificadoEmitido->setF_enviado(new DateTimeLocal($faker->date()));

        return $oCertificadoEmitido;
    }

    /**
     * Crea múltiples instancias de CertificadoEmitido
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
