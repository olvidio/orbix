<?php

namespace src\certificados\domain\contracts;

use src\certificados\domain\entity\CertificadoRecibido;

interface CertificadoRecibidoRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CertificadoRecibido>
     */
    public function getCertificados(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(CertificadoRecibido $Certificado): bool;

    public function Guardar(CertificadoRecibido $Certificado): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?CertificadoRecibido;

    public function getNewId_item(): int|string;
}
