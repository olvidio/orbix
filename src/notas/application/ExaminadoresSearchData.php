<?php

namespace src\notas\application;

use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;

/**
 * Autocomplete de examinadores: delega al repositorio que ya devuelve
 * un array listo para jQuery-UI autocomplete (`[{label, value}, ...]`).
 */
final class ExaminadoresSearchData
{

    public function __construct(
        private readonly ActaTribunalDlRepositoryInterface $actaTribunalDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $search = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'search');
        $repo = $this->actaTribunalDlRepository;
        return (string)$repo->getJsonExaminadores($search);
    }
}
