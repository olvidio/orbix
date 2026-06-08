<?php

namespace src\certificados\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\LocaleCode;

class CertificadoEmitido
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private ?int $id_nom = null;

    private ?string $nom = null;

    private ?LocaleCode $idioma = null;

    private ?string $destino = null;

    private ?string $certificado = null;

    private DateTimeLocal|null $f_certificado;

    private ?string $esquema_emisor = null;

    private bool $firmado = false;

    private ?string $documento = null;

    private DateTimeLocal|null $f_enviado;

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


    public function getIdiomaVo(): ?LocaleCode
    {
        return $this->idioma;
    }


    public function setIdiomaVo(LocaleCode|string|null $idioma): void
    {
        $this->idioma = $idioma instanceof LocaleCode
            ? $idioma
            : LocaleCode::fromNullableString($idioma);
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

    public function getF_certificado(): DateTimeLocal|null
    {
        return $this->f_certificado;
    }

    public function setF_certificado(DateTimeLocal|null $f_certificado): void
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


    public function isFirmado(): bool
    {
        return $this->firmado;
    }


    public function setFirmado(bool $firmado): void
    {
        $this->firmado = $firmado;
    }


    public function getDocumento(): ?string
    {
        return $this->documento;
    }


    public function setDocumento(?string $documento = null): void
    {
        $this->documento = $documento;
    }

    public function getF_enviado(): DateTimeLocal|null
    {
        return $this->f_enviado;
    }

    public function setF_enviado(DateTimeLocal|null $f_enviado): void
    {
        $this->f_enviado = $f_enviado;
    }
}