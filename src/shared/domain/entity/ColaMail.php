<?php

namespace src\shared\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\ColaMailId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;


class ColaMail
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private ColaMailId $uuid_item;

    private ?string $mail_to = null;

    private ?string $message = null;

    private ?string $subject = null;

    private ?string $headers = null;

    private ?string $writed_by = null;

   private ?DateTimeLocal $sended = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getUuid_item(): ColaMailId
    {
        return $this->uuid_item;
    }


    public function setUuid_item(ColaMailId|string $uuid_item): void
    {
        $this->uuid_item = $uuid_item instanceof ColaMailId
            ? $uuid_item
            : ColaMailId::fromString($uuid_item);
    }


    public function getMail_to(): ?string
    {
        return $this->mail_to;
    }


    public function setMail_to(?string $mail_to = null): void
    {
        $this->mail_to = $mail_to;
    }


    public function getMessage(): ?string
    {
        return $this->message;
    }


    public function setMessage(?string $message = null): void
    {
        $this->message = $message;
    }


    public function getSubject(): ?string
    {
        return $this->subject;
    }


    public function setSubject(?string $subject = null): void
    {
        $this->subject = $subject;
    }


    public function getHeaders(): ?string
    {
        return $this->headers;
    }


    public function setHeaders(?string $headers = null): void
    {
        $this->headers = $headers;
    }


    public function getWrited_by(): ?string
    {
        return $this->writed_by;
    }


    public function setWrited_by(?string $writed_by = null): void
    {
        $this->writed_by = $writed_by;
    }


    public function getSended(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->dsended ?? new NullDateTimeLocal;
    }


    public function setSended(DateTimeLocal|null $sended = null): void
    {
        $this->dsended = $sended;
    }
}