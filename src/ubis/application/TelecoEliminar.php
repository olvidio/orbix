<?php

namespace src\ubis\application;

final class TelecoEliminar
{
    public static function execute(string $obj_pau, array $pkey): array
    {
        $resolver = new TelecoResolver();
        $Repository = $resolver->getTelecoRepo($obj_pau);
        $TelecoUbi = $Repository->findById($pkey);
        $Repository->Eliminar($TelecoUbi);
        return ['ok' => true];
    }
}
