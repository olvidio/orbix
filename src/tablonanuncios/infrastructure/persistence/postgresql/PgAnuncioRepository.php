<?php

namespace src\tablonanuncios\infrastructure\persistence\postgresql;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\value_objects\AnuncioId;

class PgAnuncioRepository extends ClaseRepository implements AnuncioRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBPC'));
        $this->setNomTabla('tablon_anuncios');
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Anuncio>
     */
    public function getAnuncios(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $AnuncioSet = new Set();
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
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = ' WHERE ' . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        /** @var list<Anuncio> $anuncios */
        $anuncios = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $aDatos['t_anotado'] = (new ConverterDate('timestamp', $aDatos['t_anotado'] ?? null))->fromPg();
            $aDatos['t_eliminado'] = (new ConverterDate('timestamp', $aDatos['t_eliminado'] ?? null))->fromPg();
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $anuncios[] = Anuncio::fromArray($normalized);
        }

        return $anuncios;
    }

    public function Eliminar(Anuncio $Anuncio): bool
    {
        $uuid_item = $Anuncio->getUuid_item()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(Anuncio $Anuncio): bool
    {
        $uuid_item = $Anuncio->getUuid_item()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($Anuncio->getUuid_item());

        $aDatos = $Anuncio->toArrayForDatabase([
            't_anotado' => fn($v) => (new ConverterDate('timestamp', $v))->toPg(),
            't_eliminado' => fn($v) => (new ConverterDate('timestamp', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            unset($aDatos['uuid_item']);
            $update = " usuario_creador  = :usuario_creador,
                    esquema_emisor   = :esquema_emisor,
                    esquema_destino  = :esquema_destino,
                    texto_anuncio    = :texto_anuncio,
                    idioma           = :idioma,
                    tablon           = :tablon,
                    t_anotado        = :t_anotado,
                    t_eliminado      = :t_eliminado,
                    categoria        = :categoria";
            $sql = "UPDATE $nom_tabla SET $update WHERE uuid_item = '$uuid_item'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(uuid_item,usuario_creador,esquema_emisor,esquema_destino,texto_anuncio,idioma,tablon,t_anotado,t_eliminado,categoria)';
            $valores = '(:uuid_item,:usuario_creador,:esquema_emisor,:esquema_destino,:texto_anuncio,:idioma,:tablon,:t_anotado,:t_eliminado,:categoria)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(AnuncioId $vo): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $uuid_item = $vo->value();
        $sql = "SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return true;
        }
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(AnuncioId $vo): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $uuid_item = $vo->value();
        $sql = "SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $aDatos['t_anotado'] = (new ConverterDate('timestamp', $aDatos['t_anotado'] ?? null))->fromPg();
        $aDatos['t_eliminado'] = (new ConverterDate('timestamp', $aDatos['t_eliminado'] ?? null))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    public function findById(AnuncioId $vo): ?Anuncio
    {
        $aDatos = $this->datosById($vo);
        if ($aDatos === false) {
            return null;
        }
        return Anuncio::fromArray($aDatos);
    }
}
