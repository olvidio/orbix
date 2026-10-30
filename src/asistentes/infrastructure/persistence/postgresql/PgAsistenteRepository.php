<?php

namespace src\asistentes\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\asistentes\domain\criteria\AsistenteField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\criteria\NullsPosition;
use src\shared\domain\criteria\Order;
use src\shared\domain\criteria\OrderDirection;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use DateTimeInterface;
use InvalidArgumentException;
use PDO;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\asistentes\domain\value_objects\AsistentePk;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\traits\DispatchesDomainEvents;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla d_asistentes_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgAsistenteRepository extends ClaseRepository implements AsistenteRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;
    use DispatchesDomainEvents;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    protected UnitOfWorkInterface $unitOfWork;

    public function __construct(UnitOfWorkInterface $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
        $oDbl = GlobalPdo::get('oDBEP');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBEP_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_asistentes_all');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Asistentes que cumplen el criterio.
     *
     * @return list<Asistente>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(AsistenteField::class, self::COLUMNAS);
        return array_map(Asistente::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /**
     * Une los resultados de varios repositorios de asistentes (Dl, Out, Ex…) para un mismo criterio.
     * Cada repositorio se consulta solo con los filtros; el orden y la paginación (limit/offset)
     * se aplican después sobre el conjunto, en memoria, así que:
     *  - solo se admiten órdenes por campo de {@see AsistenteField} (otro tipo lanza InvalidArgumentException);
     *  - los números se comparan como números, los textos sin distinguir mayúsculas y los nulos
     *    como PostgreSQL (al final en ASC, al principio en DESC) salvo que el orden indique otra cosa;
     *  - en caso de empate manda el orden de `$repos`.
     *
     * @param list<class-string<AsistenteRepositoryInterface>> $repos
     * @return list<Asistente>
     * @throws InvalidArgumentException si un repositorio no es de asistentes o el orden no se puede aplicar
     */
    public function matchingConjunto(array $repos, Criteria $criteria): array
    {
        $orders = [];
        foreach ($criteria->orders() as $order) {
            if (!$order instanceof Order || !$order->field instanceof AsistenteField) {
                throw new InvalidArgumentException('matchingConjunto solo admite órdenes por campo de AsistenteField');
            }
            $orders[] = $order;
        }

        $soloFiltros = Criteria::create();
        foreach ($criteria->filters() as $filter) {
            $soloFiltros = $soloFiltros->andWhere($filter);
        }

        $todos = [];
        foreach ($repos as $repoInterface) {
            $repo = DependencyResolver::get($repoInterface);
            if (!$repo instanceof AsistenteRepositoryInterface) {
                throw new InvalidArgumentException($repoInterface . ' no es un AsistenteRepositoryInterface');
            }
            $todos = array_merge($todos, $repo->matching($soloFiltros));
        }

        if ($orders !== []) {
            usort($todos, static function (Asistente $a, Asistente $b) use ($orders): int {
                foreach ($orders as $order) {
                    $va = self::valorParaOrdenar($a, $order->field);
                    $vb = self::valorParaOrdenar($b, $order->field);
                    if ($va === null || $vb === null) {
                        if ($va === $vb) {
                            continue;
                        }
                        $nulosPrimero = $order->nulls !== null
                            ? $order->nulls === NullsPosition::First
                            : $order->direction === OrderDirection::Desc;
                        return ($va === null) === $nulosPrimero ? -1 : 1;
                    }
                    $cmp = (is_int($va) || is_float($va)) && (is_int($vb) || is_float($vb))
                        ? $va <=> $vb
                        : strcmp((string) $va, (string) $vb);
                    if ($cmp !== 0) {
                        return $order->direction === OrderDirection::Desc ? -$cmp : $cmp;
                    }
                }
                return 0;
            });
        }

        $offset = $criteria->getOffset() ?? 0;
        $limit = $criteria->getLimit();
        return $offset > 0 || $limit !== null ? array_values(array_slice($todos, $offset, $limit)) : $todos;
    }

    /** Valor del campo en la entidad (`getXxx()` o `isXxx()`), normalizado para compararlo. */
    private static function valorParaOrdenar(Asistente $a, AsistenteField $field): int|float|string|null
    {
        $base = ucfirst($field->value);
        if (method_exists($a, 'get' . $base)) {
            $v = $a->{'get' . $base}();
        } elseif (method_exists($a, 'is' . $base)) {
            $v = $a->{'is' . $base}();
        } else {
            throw new InvalidArgumentException('Asistente no tiene getter para el campo ' . $field->value);
        }
        if ($v instanceof DateTimeInterface) {
            return $v->getTimestamp();
        }
        if (is_object($v) && method_exists($v, 'value')) {
            $v = $v->value();
        }
        if (is_bool($v)) {
            return (int) $v;
        }
        return is_string($v) ? strtolower($v) : (is_int($v) || is_float($v) ? $v : ($v === null ? null : (string) $v));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Asistente $Asistente, bool $registrarCambios = true): bool
    {
        $id_activ = $Asistente->getId_activ();
        $id_nom = $Asistente->getId_nom();

        // Obtener datos actuales antes de eliminar (solo si hay que registrar cambios)
        $datosActuales = $registrarCambios ? $this->datosById($id_activ, $id_nom) : false;

        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ AND id_nom = $id_nom";
        $success = $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        if ($registrarCambios && $success && is_array($datosActuales)) {
            // Marcar como eliminada
            $this->markAsDeleted($Asistente, $datosActuales);
        }

        return $success;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Asistente $Asistente, bool $registrarCambios = true): bool
    {
        $id_activ = $Asistente->getId_activ();
        $id_nom = $Asistente->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ, $id_nom);

        // Obtener datos actuales si es UPDATE (solo si hay que registrar cambios)
        $datosActuales = [];
        if ($registrarCambios && !$bInsert) {
            $datosActuales = $this->datosById($id_activ, $id_nom) ?: [];
        }

        $aDatos = $Asistente->toArrayForDatabase();
        unset($aDatos['domainEvents']);

        $requiere_id_schema = ($nom_tabla === 'd_asistentes_de_paso' || $nom_tabla === 'd_asistentes_all');

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_activ']);
            unset($aDatos['id_nom']);
            $update = "
					propio                   = :propio,
					est_ok                   = :est_ok,
					cfi                      = :cfi,
					cfi_con                  = :cfi_con,
					falta                    = :falta,
					encargo                  = :encargo,
					dl_responsable           = :dl_responsable,
					observ                   = :observ,
					id_tabla                 = :id_tabla,
					plaza                    = :plaza,
					propietario              = :propietario,
					observ_est               = :observ_est,
					cama                     = :cama";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ AND id_nom = $id_nom";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            if ($requiere_id_schema) {
                $campos = "(id_schema,id_activ,id_nom,propio,est_ok,cfi,cfi_con,falta,encargo,dl_responsable,observ,id_tabla,plaza,propietario,observ_est,cama)";
                $valores = "(:id_schema,:id_activ,:id_nom,:propio,:est_ok,:cfi,:cfi_con,:falta,:encargo,:dl_responsable,:observ,:id_tabla,:plaza,:propietario,:observ_est,:cama)";
            } else {
                $campos = "(id_activ,id_nom,propio,est_ok,cfi,cfi_con,falta,encargo,dl_responsable,observ,id_tabla,plaza,propietario,observ_est,cama)";
                $valores = "(:id_activ,:id_nom,:propio,:est_ok,:cfi,:cfi_con,:falta,:encargo,:dl_responsable,:observ,:id_tabla,:plaza,:propietario,:observ_est,:cama)";
            }
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($stmt === false) {
            return false;
        }

        if ($bInsert && $requiere_id_schema) {
            $idSchema = ConfigGlobal::mi_id_schema();
            if ($idSchema < 1) {
                throw new \RuntimeException(_("Falta id_schema de sesión (mi_id_schema) para persistir el asistente."));
            }
            $aDatos['id_schema'] = $idSchema;
        }

        $success = $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);

        if ($registrarCambios && $success) {
            // Marcar evento de dominio
            if ($bInsert) {
                $this->markAsNew($Asistente, $datosActuales);
            } else {
                $this->markAsModified($Asistente, $datosActuales);
            }
        }

        return $success;
    }

    private function isNew(int $id_activ, int $id_nom): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ AND id_nom = $id_nom";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_activ
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_activ, int $id_nom): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ AND id_nom = $id_nom";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }

        $row = [];
        foreach ($aDatos as $key => $value) {
            $row[(string) $key] = $value;
        }

        return $row;
    }

    public function datosByPk(AsistentePk $pk): array|false
    {
        return $this->datosById($pk->IdActiv(), $pk->IdNom());
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_activ, int $id_nom): ?Asistente
    {
        $aDatos = $this->datosById($id_activ, $id_nom);
        if (!is_array($aDatos)) {
            return null;
        }
        return Asistente::fromArray($aDatos);
    }

    public function findByPk(AsistentePk $pk): ?Asistente
    {
        return $this->findById($pk->IdActiv(), $pk->IdNom());
    }
}
