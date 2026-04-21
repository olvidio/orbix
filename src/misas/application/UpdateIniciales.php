<?php

namespace src\misas\application;

use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\entity\InicialesSacd;

class UpdateIniciales
{
    /**
     * Inserta o actualiza la fila de iniciales/color para un sacerdote.
     *
     * Devuelve texto vacio si todo fue bien; en otro caso, el mensaje de
     * error del repositorio. El controlador HTTP es quien serializa la
     * respuesta con `ContestarJson::enviar(...)`.
     */
    public static function execute(int $id_sacd, string $iniciales, string $color): string
    {
        $InicialesSacdRepository = $GLOBALS['container']->get(InicialesSacdRepositoryInterface::class);

        $InicialesSacd = $InicialesSacdRepository->findById($id_sacd);
        if ($InicialesSacd === null) {
            $InicialesSacd = new InicialesSacd();
            $InicialesSacd->setId_nom($id_sacd);
        }
        $InicialesSacd->setIniciales($iniciales);
        $InicialesSacd->setColor(InicialesColorHex::normalizeForStorage($color));

        if ($InicialesSacdRepository->Guardar($InicialesSacd) === false) {
            return $InicialesSacdRepository->getErrorTxt();
        }
        return '';
    }
}
