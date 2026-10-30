<?php

namespace src\certificados\domain\contracts;

use src\certificados\domain\entity\CertificadoEmitido;
use src\shared\domain\criteria\Criteria;

interface CertificadoEmitidoRepositoryInterface
{
    /**
     * Certificados que cumplen el criterio.
     *
     * @return list<CertificadoEmitido>
     */
    public function matching(Criteria $criteria): array;

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
