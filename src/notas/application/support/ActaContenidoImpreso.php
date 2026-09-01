<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\notas\application\DatosActa;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\entity\Acta;

/**
 * Huella del contenido que sale en el acta impresa (cabecera, tribunal y notas de tessera).
 * Sirve para exigir reimpresión después de un cambio antes de subir el PDF firmado.
 */
final class ActaContenidoImpreso
{
    public const VERSION = 1;

    public function __construct(
        private readonly ActaTribunalRepositoryInterface $actaTribunalRepository,
        private readonly DatosActa $datosActa,
    ) {
    }

    public function hashDeActa(Acta $oActa): string
    {
        $actaNum = $oActa->getActa();
        $cTribunal = $this->actaTribunalRepository->getActasTribunales([
            'acta' => $actaNum,
            '_ordre' => 'orden',
        ]);
        $examinadores = [];
        foreach ($cTribunal as $oTribunal) {
            $examinadores[] = (string) $oTribunal->getExaminador();
        }

        $notas = [];
        foreach ($this->datosActa->getNotasActa($actaNum) as $oPersonaNota) {
            $notas[] = [
                'id_nom' => $oPersonaNota->getId_nom(),
                'nota' => $oPersonaNota->getNota_txt(),
            ];
        }
        usort(
            $notas,
            static fn (array $a, array $b): int => $a['id_nom'] <=> $b['id_nom'],
        );

        $payload = [
            'v' => self::VERSION,
            'acta' => $actaNum,
            'id_asignatura' => $oActa->getId_asignatura(),
            'f_acta' => $oActa->getF_acta()?->getIso() ?? '',
            'libro' => $oActa->getLibro(),
            'pagina' => $oActa->getPagina(),
            'linea' => $oActa->getLinea(),
            'lugar' => $oActa->getLugar() ?? '',
            'observ' => $oActa->getObserv() ?? '',
            'examinadores' => $examinadores,
            'notas' => $notas,
        ];

        return hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    public function coincideConImpreso(Acta $oActa): bool
    {
        $stored = $oActa->getHash_impreso();
        if ($stored === null || $stored === '') {
            return false;
        }

        return hash_equals($stored, $this->hashDeActa($oActa));
    }
}
