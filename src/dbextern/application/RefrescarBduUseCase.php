<?php

namespace src\dbextern\application;

use src\dbextern\domain\CopiarBDU;

class RefrescarBduUseCase
{
    public function __construct(
        private CopiarBDU $copiarBDU,
    ) {
    }

    public function __invoke(): void
    {
        $this->copiarBDU->crearTablaTmp();
    }
}
