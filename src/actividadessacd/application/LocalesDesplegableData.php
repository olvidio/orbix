<?php

namespace src\actividadessacd\application;

use src\usuarios\domain\contracts\LocalRepositoryInterface;

final class LocalesDesplegableData
{
    public function __construct(
        private LocalRepositoryInterface $localRepository,
    ) {
    }

    /**
     * @return array{a_locales: array<int|string, mixed>}
     */
    public function execute(): array
    {
        return ['a_locales' => $this->localRepository->getArrayLocales()];
    }
}
