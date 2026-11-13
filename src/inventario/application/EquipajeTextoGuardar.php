<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\criteria\EgmField as EgmF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Guardar un texto del listado de un equipaje (`equipajes_texto_listado_guardar`): la cabecera, la
 * cabecera B, el pie o el texto de una maleta (`docs_grupo_<id_grupo>`).
 *
 * Sucesor de la lógica del controlador (fase 5, paso 5.2). Corrige: el mensaje de «texto demasiado
 * largo» daba los límites antiguos (los de la cabecera B y el pie se subieron a 400 y 2000) y, si el
 * grupo no existía, se llamaba a un método sobre null.
 */
final class EquipajeTextoGuardar
{
    /** Límite de caracteres de cada texto (los de los value objects). */
    private const LIMITES = ['cabecera' => 400, 'cabeceraB' => 400, 'pie' => 2000];
    private const LIMITE_TEXTO_MALETA = 255;

    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
        private EgmRepositoryInterface $egmRepository,
    ) {
    }

    /** @return string texto de error ('' si todo fue bien) */
    public function execute(int $idEquipaje, string $loc, string $texto): string
    {
        try {
            if (isset(self::LIMITES[$loc])) {
                $oEquipaje = $this->equipajeRepository->findById($idEquipaje);
                if ($oEquipaje === null) {
                    return '';
                }
                match ($loc) {
                    'cabecera' => $oEquipaje->setCabecera($texto),
                    'cabeceraB' => $oEquipaje->setCabecerab($texto),
                    'pie' => $oEquipaje->setPie($texto),
                };
                if ($this->equipajeRepository->Guardar($oEquipaje) === false) {
                    return _("hay un error, no se ha guardado") . "\n" . $this->equipajeRepository->getErrorTxt();
                }

                return '';
            }
            if (preg_match('/^docs_grupo_(\d+)$/', $loc, $m) !== 1) {
                return '';
            }
            $cEgm = $this->egmRepository->matching(Criteria::create()
                ->where(Filter::eq(EgmF::IdEquipaje, $idEquipaje))
                ->andWhere(Filter::eq(EgmF::IdGrupo, (int) $m[1])));
            if ($cEgm === []) {
                return _("hay un error, no se ha guardado");
            }
            $cEgm[0]->setTextoVo($texto);
            if ($this->egmRepository->Guardar($cEgm[0]) === false) {
                return _("hay un error, no se ha guardado") . "\n" . $this->egmRepository->getErrorTxt();
            }

            return '';
        } catch (\InvalidArgumentException $e) {
            if (str_contains($e->getMessage(), 'must be at most')) {
                return sprintf(_("El texto no puede superar %d caracteres"), self::LIMITES[$loc] ?? self::LIMITE_TEXTO_MALETA);
            }

            return _("hay un error, no se ha guardado") . "\n" . $e->getMessage();
        }
    }
}
