<?php

namespace src\notas\application;

use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;

/**
 * Autocomplete de examinadores: delega al repositorio que ya devuelve
 * un array listo para jQuery-UI autocomplete (`[{label, value}, ...]`).
 */
final class ExaminadoresSearchData
{
    public static function execute(array $input): string
    {
        $search = (string)($input['search'] ?? '');
        $repo = $GLOBALS['container']->get(ActaTribunalDlRepositoryInterface::class);
        return (string)$repo->getJsonExaminadores($search);
    }
}
