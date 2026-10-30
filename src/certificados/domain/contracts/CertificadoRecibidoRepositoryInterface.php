<?php

namespace src\certificados\domain\contracts;

use src\certificados\domain\entity\CertificadoRecibido;
use src\shared\domain\criteria\Criteria;

interface CertificadoRecibidoRepositoryInterface
{
    /**
     * Certificados que cumplen el criterio.
     *
     * @return list<CertificadoRecibido>
     */
    public function matching(Criteria $criteria): array;

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
