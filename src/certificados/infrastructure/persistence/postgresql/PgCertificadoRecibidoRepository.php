<?php

namespace src\certificados\infrastructure\persistence\postgresql;

use PDO;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\certificados\domain\criteria\CertificadoRecibidoField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\HandlesPgBytea;

class PgCertificadoRecibidoRepository extends ClaseRepository implements CertificadoRecibidoRepositoryInterface
{
    use HandlesPdoErrors;
    use HandlesPgBytea;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDB'));
        $this->setoDbl_select(GlobalPdo::get('oDB'));
        $this->setNomTabla('e_certificados_dl');
    }

    /**
     * Certificados que cumplen el criterio.
     *
     * @return list<CertificadoRecibido>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(CertificadoRecibidoField::class, self::COLUMNAS);
        /** @var list<CertificadoRecibido> $certificados */
        $certificados = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $aDatos['documento'] = $this->normalizeBytea($this->readByteaField($aDatos['documento'] ?? null));
            $aDatos['f_certificado'] = (new ConverterDate('date', $aDatos['f_certificado'] ?? null))->fromPg();
            $aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido'] ?? null))->fromPg();
            $certificados[] = CertificadoRecibido::fromArray($aDatos);
        }

        return $certificados;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CertificadoRecibido>
     */
    public function getCertificados(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(CertificadoRecibidoField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    public function Eliminar(CertificadoRecibido $Certificado): bool
    {
        $id_item = $Certificado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(CertificadoRecibido $Certificado): bool
    {
        $id_item = $Certificado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $Certificado->toArrayForDatabase([
            'documento' => fn ($v) => ($v ? ('\\x' . bin2hex((string) $v)) : null),
            'f_certificado' => fn ($v) => (new ConverterDate('date', $v))->toPg(),
            'f_recibido' => fn ($v) => (new ConverterDate('date', $v))->toPg(),
        ]);
        if ($bInsert === false) {
            unset($aDatos['id_item']);
            $update = '
					id_nom                   = :id_nom,
					nom                      = :nom,
					idioma                   = :idioma,
					destino                  = :destino,
					certificado              = :certificado,
					f_certificado            = :f_certificado,
					esquema_emisor           = :esquema_emisor,
					firmado                  = :firmado,
					documento                = :documento,
                    f_recibido                = :f_recibido';
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_item,id_nom,nom,idioma,destino,certificado,f_certificado,esquema_emisor,firmado,documento,f_recibido)';
            $valores = '(:id_item,:id_nom,:nom,:idioma,:destino,:certificado,:f_certificado,:esquema_emisor,:firmado,:documento,:f_recibido)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($stmt === false) {
            return false;
        }

        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }

        return $stmt->rowCount() === 0;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }

        $aDatos['documento'] = $this->normalizeBytea($this->readByteaField($aDatos['documento'] ?? null));

        $aDatos['f_certificado'] = (new ConverterDate('date', $aDatos['f_certificado'] ?? null))->fromPg();
        $aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido'] ?? null))->fromPg();

        return $aDatos;
    }

    public function findById(int $id_item): ?CertificadoRecibido
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }

        return CertificadoRecibido::fromArray($aDatos);
    }

    public function getNewId_item(): int|string
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('e_certificados_dl_id_item_seq'::regclass)";
        $result = $oDbl->query($sQuery);
        if ($result === false) {
            return 0;
        }

        $value = $result->fetchColumn();

        return is_numeric($value) ? (int) $value : (string) $value;
    }
}
