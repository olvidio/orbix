<?php

namespace src\certificados\infrastructure\persistence\postgresql;

use PDO;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\HandlesPgBytea;

class PgCertificadoEmitidoRepository extends ClaseRepository implements CertificadoEmitidoRepositoryInterface
{
    use HandlesPdoErrors;
    use HandlesPgBytea;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDB'));
        $this->setNomTabla('e_certificados_rstgr');
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CertificadoEmitido>
     */
    public function getCertificados(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $CertificadoSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre' || $camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN' || $sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = ' WHERE ' . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && is_scalar($aWhere['_ordre']) && (string) $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . (string) $aWhere['_ordre'];
        }
        unset($aWhere['_ordre']);
        if (isset($aWhere['_limit']) && is_scalar($aWhere['_limit']) && (string) $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . (string) $aWhere['_limit'];
        }
        unset($aWhere['_limit']);

        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $aDatos['documento'] = $this->normalizeBytea($this->readByteaField($aDatos['documento'] ?? null));
            $aDatos['f_certificado'] = (new ConverterDate('date', $aDatos['f_certificado'] ?? null))->fromPg();
            $aDatos['f_enviado'] = (new ConverterDate('date', $aDatos['f_enviado'] ?? null))->fromPg();
            $CertificadoSet->add(CertificadoEmitido::fromArray($aDatos));
        }

        return array_values($CertificadoSet->getTot());
    }

    public function Eliminar(CertificadoEmitido $Certificado): bool
    {
        $id_item = $Certificado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(CertificadoEmitido $Certificado): bool
    {
        $id_item = $Certificado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $Certificado->toArrayForDatabase([
            'h_ini' => fn ($v) => (new ConverterDate('time', $v))->toPg(),
            'documento' => fn ($v) => ($v ? ('\\x' . bin2hex((string) $v)) : null),
            'f_certificado' => fn ($v) => (new ConverterDate('date', $v))->toPg(),
            'f_enviado' => fn ($v) => (new ConverterDate('date', $v))->toPg(),
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
                    f_enviado                = :f_enviado';
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_item,id_nom,nom,idioma,destino,certificado,f_certificado,esquema_emisor,firmado,documento,f_enviado)';
            $valores = '(:id_item,:id_nom,:nom,:idioma,:destino,:certificado,:f_certificado,:esquema_emisor,:firmado,:documento,:f_enviado)';
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
        $aDatos['f_enviado'] = (new ConverterDate('date', $aDatos['f_enviado'] ?? null))->fromPg();

        return $aDatos;
    }

    public function findById(int $id_item): ?CertificadoEmitido
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }

        return CertificadoEmitido::fromArray($aDatos);
    }

    public function getNewId_item(): int|string
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('e_certificados_rstgr_id_item_seq'::regclass)";
        $result = $oDbl->query($sQuery);
        if ($result === false) {
            return 0;
        }

        $value = $result->fetchColumn();

        return is_numeric($value) ? (int) $value : (string) $value;
    }
}
