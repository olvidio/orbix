<?php

namespace src\certificados\application;

use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

final class CertificadoRecibidoModificarFormData
{
    public function __construct(
        private readonly CertificadoRecibidoRepositoryInterface $certificadoRecibidoRepository,
        private readonly LocalRepositoryInterface $localRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_item): array
    {
        $oCertificadoRecibido = $this->certificadoRecibidoRepository->findById($id_item);
        if ($oCertificadoRecibido === null) {
            throw new \RuntimeException(_('No se encuentra el certificado'));
        }

        $id_nom = (int) ($oCertificadoRecibido->getId_nom() ?? 0);
        $nom = (string) ($oCertificadoRecibido->getNom() ?? '');
        $idioma = (string) ($oCertificadoRecibido->getIdiomaVo()?->value() ?? '');
        $destino = (string) ($oCertificadoRecibido->getDestino() ?? '');
        $certificado = (string) ($oCertificadoRecibido->getCertificado() ?? '');
        $f_certificado = $oCertificadoRecibido->getF_certificado();
        $f_certificado_txt = $f_certificado instanceof DateTimeLocal ? $f_certificado->getFromLocal() : '';
        $f_recibido = $oCertificadoRecibido->getF_recibido();
        $f_recibido_txt = $f_recibido instanceof DateTimeLocal ? $f_recibido->getFromLocal() : '';
        if ($f_recibido_txt === '') {
            $f_recibido_txt = (new DateTimeLocal())->getFromLocal();
        }
        $firmado = (bool) ($oCertificadoRecibido->isFirmado() ?? false);

        return [
            'id_nom' => $id_nom,
            'nom' => $nom,
            'idioma' => $idioma,
            'destino' => $destino,
            'certificado' => $certificado,
            'f_certificado' => $f_certificado_txt,
            'f_recibido' => $f_recibido_txt,
            'firmado' => $firmado,
            'chk_firmado' => $firmado ? 'checked' : '',
            'a_locales' => $this->localRepository->getArrayLocales(),
        ];
    }
}
