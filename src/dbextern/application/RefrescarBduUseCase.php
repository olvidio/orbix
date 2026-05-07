<?php

namespace src\dbextern\application;

use src\dbextern\domain\CopiarBDU;

class RefrescarBduUseCase
{
    public function __invoke(): void
    {
        $oCopiarBDU = new CopiarBDU();
        $oCopiarBDU->crearTablaTmp();
    }
}
