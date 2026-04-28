<?php

namespace src\actividadessacd\application;

use src\usuarios\domain\contracts\LocalRepositoryInterface;

final class LocalesDesplegableData
{
    /**
     * @return array{a_locales: array}
     */
    public static function execute(): array
    {
        $LocaleRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);

        return ['a_locales' => $LocaleRepository->getArrayLocales()];
    }
}
