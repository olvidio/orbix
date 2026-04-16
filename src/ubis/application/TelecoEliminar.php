<?php

namespace src\ubis\application;

final class TelecoEliminar
{
    public static function execute(string $obj_pau, array $a_pkey): array
    {
        $resolver = new TelecoResolver();
        $Repository = $resolver->getTelecoRepo($obj_pau);

        foreach ($a_pkey as $pkey) {
            $TelecoUbi = $Repository->findById($pkey);
            $Repository->Eliminar($TelecoUbi);
        }
        return ['ok' => true];
    }
}
