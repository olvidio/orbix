<?php

namespace src\tablonanuncios\infrastructure\persistence\postgresql;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\tablonanuncios\domain\criteria\AnuncioField;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\value_objects\AnuncioId;

class PgAnuncioRepository extends ClaseRepository implements AnuncioRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBPC'));
        $this->setoDbl_select(GlobalPdo::get('oDBPC_Select'));
        $this->setNomTabla('tablon_anuncios');
    }

    /**
     * Anuncios que cumplen el criterio.
     *
     * @return list<Anuncio>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(AnuncioField::class, self::COLUMNAS);
        $anuncios = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $aDatos['t_anotado'] = (new ConverterDate('timestamp', $aDatos['t_anotado'] ?? null))->fromPg();
            $aDatos['t_eliminado'] = (new ConverterDate('timestamp', $aDatos['t_eliminado'] ?? null))->fromPg();
            $anuncios[] = Anuncio::fromArray($aDatos);
        }
        return $anuncios;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Anuncio>
     */
    public function getAnuncios(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(AnuncioField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
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
