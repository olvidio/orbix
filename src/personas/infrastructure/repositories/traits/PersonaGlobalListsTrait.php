<?php

namespace src\personas\infrastructure\repositories\traits;

/**
 * Trait con utilidades de listados para personas, extraído de GestorPersonaGlobal.
 * Debe ser usado por repositorios que heredan de core\ClaseRepository, ya que
 * requiere los métodos `getoDbl()` y `getNomTabla()`.
 */
trait PersonaGlobalListsTrait
{
    /**
     * Expresión SQL para apellidos y nombre formateado.
     *
     * Nota: se mantiene como en legacy para asegurar compatibilidad en listados.
     */
    protected string $sApeNom = "apellido1
            || case when nx2 = '' or nx2 isnull then ' ' else ' '||nx2||' ' end 
            || case when apellido2 = '' or apellido2 isnull then '' else ''||apellido2||'' end 
            || ', '|| case when trato isnull or trato = '' then '' else trato||' ' end 
            || COALESCE(apel_fam, nom)
            || case when nx1 = '' or nx1 isnull then '' else ' '||nx1||' ' end
          ";

    /**
     * Devuelve un array con los id de centros (id_ctr) de personas activas.
     *
     * @param string $sdonde condición extra SQL (debe empezar por AND)
     * @return array
     */
    public function getArrayIdCentros(string $sdonde = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_ctr FROM $nom_tabla WHERE situacion='A' $sdonde GROUP BY id_ctr";
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aLista = [];
        foreach ($stmt as $aDades) {
            $aLista[$aDades['id_ctr']] = $aDades['id_ctr'];
        }
        return $aLista;
    }

    /**
     * Lista de posibles SACD en array [id_nom => ape_nom].
     *
     * @param string $sdonde condición extra SQL (debe empezar por AND)
     * @return array|false
     */
    public function getArraySacd(string $sdonde = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_nom, " . $this->sApeNom . " as ape_nom
                        FROM $nom_tabla
                        WHERE situacion='A' AND sacd='t' $sdonde
                        ORDER by apellido1,apellido2,nom";
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aSacd = [];
        foreach ($stmt as $aDades) {
            $aSacd[$aDades['id_nom']] = $aDades['ape_nom'];
        }
        return $aSacd;
    }

    /**
     * Lista de personas activas en array [id_nom => ape_nom(centro)].
     *
     * @param string $id_tabla únicamente usado cuando la tabla es p_de_paso_ex
     * @return array|false
     */
    public function getArrayPersonas(string $id_tabla = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($nom_tabla === 'p_de_paso_ex') {
            $Qry_tabla = empty($id_tabla) ? '' : "AND id_tabla = '$id_tabla'";
            $sQuery = "SELECT id_nom, " . $this->sApeNom . " || ' (' || p.dl || ')' as ape_nom
                       FROM $nom_tabla p 
                       WHERE p.situacion='A' $Qry_tabla
                       ORDER by apellido1,apellido2,nom";
        } else {
            $sQuery = "SELECT id_nom, " . $this->sApeNom . " || ' (' ||   COALESCE(c.nombre_ubi, '-') || ')' as ape_nom
                       FROM $nom_tabla p LEFT JOIN u_centros_dl c ON (c.id_ubi=p.id_ctr)
                       WHERE p.situacion='A'
                       ORDER by apellido1,apellido2,nom";
        }
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aPersonas = [];
        foreach ($stmt as $aDades) {
            $aPersonas[$aDades['id_nom']] = $aDades['ape_nom'];
        }
        return $aPersonas;
    }

}
