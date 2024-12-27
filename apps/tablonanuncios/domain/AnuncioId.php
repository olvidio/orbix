<?php

namespace tablonanuncios\domain;

use core\ValueObject\Uuid;

final class AnuncioId extends Uuid
{
    public static function random(): self
    {
        return new AnuncioId(parent::random());
    }
}