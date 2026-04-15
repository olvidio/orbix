<?php

namespace src\ubis\application;

use src\shared\infrastructure\ProvidesRepositories;

final class TelecoResolver
{
    use ProvidesRepositories;

    public function getTelecoRepo(string $obj_pau): object
    {
        return $this->getTelecoRepository($obj_pau);
    }

    public function getUbiRepo(string $obj_pau): object
    {
        return $this->getRepository($obj_pau);
    }

    public function getTelecoRepoClass(string $obj_pau): string
    {
        return $this->getTelecoRepositoryClass($obj_pau);
    }
}
