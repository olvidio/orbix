<?php

namespace src\certificados\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;


class CertificadoRecibido
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private ?int $id_nom = null;

    private ?string $nom = null;

    private ?string $idioma = null;

    private ?string $destino = null;

    private ?string $certificado = null;

    private DateTimeLocal|NullDateTimeLocal|null $f_certificado;

    private ?string $esquema_emisor = null;

    private ?bool $firmado = null;

    private ?string $documento = null;

    private DateTimeLocal|NullDateTimeLocal|null $f_recibido;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_nom(): ?int
    {
        return $this->id_nom;
    }


    public function setId_nom(?int $id_nom = null): void
    {
        $this->id_nom = $id_nom;
    }


    public function getNom(): ?string
    {
        return $this->nom;
    }


    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }


    public function getIdioma(): ?string
    {
        return $this->idioma;
    }


    public function setIdioma(?string $idioma): void
    {
        $this->idioma = $idioma;
    }


    public function getDestino(): ?string
    {
        return $this->destino;
    }


    public function setDestino(?string $destino): void
    {
        $this->destino = $destino;
    }


    public function getCertificado(): ?string
    {
        return $this->certificado;
    }


    public function setCertificado(?string $certificado = null): void
    {
        $this->certificado = $certificado;
    }

    public function getF_certificado(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->f_certificado ?? new NullDateTimeLocal;
    }

    public function setF_certificado(DateTimeLocal|NullDateTimeLocal|null $f_certificado): void
    {
        $this->f_certificado = $f_certificado;
    }


    public function getEsquema_emisor(): ?string
    {
        return $this->esquema_emisor;
    }


    public function setEsquema_emisor(?string $esquema_emisor = null): void
    {
        $this->esquema_emisor = $esquema_emisor;
    }


    public function isFirmado(): ?bool
    {
        return $this->firmado;
    }


    public function setFirmado(?bool $bfirmado = null): void
    {
        $this->firmado = $bfirmado;
    }


    public function getDocumento(): ?string
    {
        return $this->documento;
    }


    public function setDocumento(?string $documento = null): void
    {
        $this->documento = $documento;
    }

    public function getF_recibido(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->f_recibido ?? new NullDateTimeLocal;
    }

    public function setF_recibido(DateTimeLocal|NullDateTimeLocal|null $f_recibido): void
    {
        $this->f_recibido = $f_recibido;
    }
}