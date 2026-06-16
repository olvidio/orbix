<?php

namespace src\actividadplazas\infrastructure\persistence\postgresql;

use JsonException;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ConverterJson;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla da_plazas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgActividadPlazasDlRepository extends PgActividadPlazasRepository implements ActividadPlazasDlRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setNomTabla('da_plazas_dl');
    }

    /**
     * Sobreescribe el Guardar del padre para usar la clave compuesta
     * (id_activ, id_dl, dl_tabla) en lugar de solo id_activ.
     * da_plazas_dl tiene una fila por DL y actividad, no una por actividad.
     *
     * @throws JsonException
     */
    public function Guardar(ActividadPlazas $ActividadPlazas): bool
    {
        $id_activ  = $ActividadPlazas->getId_activ();
        $id_dl     = $ActividadPlazas->getId_dl();
        $dl_tabla  = $ActividadPlazas->getDlTablaVo()->value();
        $oDbl      = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert   = $this->isNewDl($id_activ, $id_dl, $dl_tabla);

        $aDatos = $ActividadPlazas->toArrayForDatabase([
            'cedidas' => fn($v) => (new ConverterJson($ActividadPlazas->getArrayCedidas(), false))->toPg(false),
        ]);
        $aDatos['id_dl'] = $id_dl;

        if ($bInsert === false) {
            unset($aDatos['id_activ']);
            $update = "
                id_dl    = :id_dl,
                plazas   = :plazas,
                cl       = :cl,
                dl_tabla = :dl_tabla,
                cedidas  = :cedidas";
            $aDatos['dl_tabla_where'] = $dl_tabla;
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ AND id_dl = $id_dl AND dl_tabla = :dl_tabla_where";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos  = "(id_activ,id_dl,plazas,cl,dl_tabla,cedidas)";
            $valores = "(:id_activ,:id_dl,:plazas,:cl,:dl_tabla,:cedidas)";
            $sql     = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt    = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNewDl(int $id_activ, int $id_dl, string $dl_tabla): bool
    {
        $oDbl      = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql       = "SELECT 1 FROM $nom_tabla WHERE id_activ = :id_activ AND id_dl = :id_dl AND dl_tabla = :dl_tabla";
        $stmt      = $this->prepareAndExecute($oDbl, $sql, ['id_activ' => $id_activ, 'id_dl' => $id_dl, 'dl_tabla' => $dl_tabla], __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        return $stmt->rowCount() === 0;
    }

}