<?php

namespace src\encargossacd\application;

use src\encargossacd\db\DBPropuestas;

final class PropuestasCrearTabla
{
    /**
     * @param \Closure|null $crear creación de las tablas (por defecto, DBPropuestas::createAll()).
     *                              Inyectable para poder probar el caso de uso sin ejecutar DDL.
     */
    public function __construct(private ?\Closure $crear = null)
    {
    }

    /**
     * @return array{success: bool, mensaje?: string}
     */
    public function execute(): array
    {
        try {
            ($this->crear ?? static function (): void {
                (new DBPropuestas())->createAll();
            })();
        } catch (\RuntimeException) {
            return ['success' => false, 'mensaje' => _("No se puede crear la tabla")];
        }

        return ['success' => true];
    }
}
