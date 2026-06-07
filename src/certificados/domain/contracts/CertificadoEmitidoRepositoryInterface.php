<?php

namespace src\certificados\domain\contracts;

use src\certificados\domain\entity\CertificadoEmitido;

interface CertificadoEmitidoRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CertificadoEmitido>
     */
    public function getCertificados(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(CertificadoEmitido $Certificado): bool;

    public function Guardar(CertificadoEmitido $Certificado): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?CertificadoEmitido;

    public function getNewId_item(): int|string;
}
