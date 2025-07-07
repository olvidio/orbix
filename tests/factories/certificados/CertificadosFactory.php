<?php

namespace Tests\factories\certificados;

use Faker\Factory;
use src\certificados\domain\entity\Certificado;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class CertificadosFactory
{
    private int $count = 1;
    private string $region;
    private $esquemas_posibles = [
        "H-Hv",
        "GalBel-crGalBelv",
    ];

    public function __construct()
    {
    }

    public function create($id_nom, $region)
    {
        $this->region = $region;
        return $this->crear_Certificado($id_nom);
    }

    public function crear_Certificado($id_nom): array
    {
        $faker = Factory::create();
        $count = $this->getCount() ?? 10; // número de certificados

        if (!empty($id_nom)) {
            $nom = $faker->firstName . ' ' . $faker->name();
        }

        $cCertificados = [];
        for ($c = 0; $c < $count; $c++) {
            $id_item = $faker->numberBetween(100, 900);
            if (empty($id_nom)) {
                $id_nom_c = $faker->numberBetween(1001245, 1001987);
                $nom_c = $faker->firstName . ' ' . $faker->name();
            } else {
                $id_nom_c = $id_nom;
                $nom_c = $nom;
            }
            $idioma = $faker->languageCode();
            $destino = $faker->colorName;
            $f_cert_iso = $faker->dateTimeBetween()->format('Y-m-d'); // a date between -30 years ago, and now
            $oFCertificado = new DateTimeLocal($f_cert_iso); // a date between -30 years ago, and now
            $f_certificado = $oFCertificado;

            $year = $oFCertificado->format('y');
            $num_cert = $faker->numberBetween(1, 150);
            $certificado = $this->region . ' ' . "$num_cert/$year";

            $esquema_emisor = $this->region . '-cr' . $this->region . 'v';
            $firmado = $faker->boolean;
            $documento = $faker->sentence(150);
            $enviado = $faker->boolean();
            if ($enviado) {
                $f_env_iso = $faker->dateTimeBetween()->format('Y-m-d'); // a date between -30 years ago, and now
                $oFEnviado = new DateTimeLocal($f_env_iso); // a date between -30 years ago, and now
                $f_enviado = $oFEnviado;
            } else {
                $f_enviado = new NullDateTimeLocal();
            }

            $Certificado = new Certificado();
            $Certificado->setId_item($id_item);
            $Certificado->setId_nom($id_nom_c);
            $Certificado->setNom($nom_c);
            $Certificado->setIdioma($idioma);
            $Certificado->setDestino($destino);
            $Certificado->setCertificado($certificado);
            $Certificado->setF_certificado($f_certificado);
            $Certificado->setEsquema_emisor($esquema_emisor);
            $Certificado->setFirmado($firmado);
            $Certificado->setDocumento($documento);
            $Certificado->setF_enviado($f_enviado);

            $cCertificados[] = $Certificado;
        }

        return $cCertificados;
    }


    public function setCount(int $count)
    {
        $this->count = $count;
    }

    private function getCount()
    {
        return $this->count;
    }

}