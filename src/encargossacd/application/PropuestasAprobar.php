<?php

namespace src\encargossacd\application;

use src\encargossacd\application\services\PropuestasAprobarService;

final class PropuestasAprobar
{
    public function __construct(private PropuestasAprobarService $aprobarService)
    {
    }

    /**
     * @return array{text: string}
     */
    public function execute(): array
    {
        return ['text' => $this->aprobarService->execute()];
    }
}
