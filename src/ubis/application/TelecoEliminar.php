<?php

namespace src\ubis\application;

use src\shared\infrastructure\ProvidesRepositories;

final class TelecoEliminar
{
    use ProvidesRepositories;

    public static function execute(string $obj_pau, array $a_pkey): array
    {
        return (new self())->run($obj_pau, $a_pkey);
    }

    private function run(string $obj_pau, array $a_pkey): array
    {
        $Repository = $this->getTelecoRepository($obj_pau);

        foreach ($a_pkey as $pkey) {
            $TelecoUbi = $Repository->findById($pkey);
            $Repository->Eliminar($TelecoUbi);
        }
        return ['ok' => true];
    }
}
