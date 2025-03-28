<?php

namespace devel\controller;
/**
 * Devuelve un array con los nombres de los campos que forman la clave primaria de la tabla
 *
 * Si no existe clave primaria, devuelve la primera clave única que encuentra. Si hay más de una, no
 * sé que puede pasar.
 *
 */
function primaryKey($oDB, $tabla)
{
    // si la tabla tiene el schema, hay que separalo:
    $schema = strtok($tabla, '.');
    if ($schema !== $tabla) {
        $tabla = strtok('.');
        $schema = "\"". $schema . "\"";
    }
    $schema_tabla = $schema.'.'.$tabla;
    $query_primaria = " SELECT a.attname
            FROM   pg_index i
            JOIN   pg_attribute a ON a.attrelid = i.indrelid
            AND a.attnum = ANY(i.indkey)
            WHERE  i.indrelid = '$schema_tabla'::regclass
            AND    i.indisprimary; ";

    $oDBSt_resultado = $oDB->query($query_primaria);
    $row = $oDBSt_resultado->fetch(\PDO::FETCH_ASSOC);
    if (empty($row)) exit ('Quizà falta definir la clave primaria');
    $campo[] = $row['attname'];

    return $campo;

    /*
    // si la tabla tiene el schema, hay que separalo:
    $schema_sql = '';
    $schema = strtok($tabla, '.');
    if ($schema !== $tabla) {
        $tabla = strtok('.');
        $schema_sql = "and n.nspname='$schema' ";
    }
    //miro si existe clave primaria, sino cojo la única
    $query_primaria = " SELECT a.attname
FROM   pg_index i
JOIN   pg_attribute a ON a.attrelid = i.indrelid
AND a.attnum = ANY(i.indkey)
WHERE  i.indrelid = '"H-dlb".i_colecciones_dl'::regclass
AND    i.indisprimary; ";

    $query_primaria = " select  i.indkey, c.oid
			from pg_catalog.pg_index i, pg_catalog.pg_class c, pg_catalog.pg_namespace n
			where i.indisprimary='t' and i.indisunique='t' and i.indrelid=c.oid and n.oid = c.relnamespace and c.relname='$tabla' $schema_sql";
    $oDBSt_resultado = $oDB->query($query_primaria);
    if (!$oDBSt_resultado->rowCount()) {
        $query_unica = " select  i.indkey, c.oid
				from pg_catalog.pg_index i, pg_catalog.pg_class c, pg_catalog.pg_namespace n
				where i.indisunique='t' and i.indrelid=c.oid and n.oid = c.relnamespace and c.relname='$tabla' $schema_sql";
        $oDBSt_resultado = $oDB->query($query_unica);
    }
    //buscar el nombre
    $row = $oDBSt_resultado->fetch(\PDO::FETCH_ASSOC);
    $claves = explode(" ", $row['indkey']);
    $oid_tabla = $row['oid'];
    if (empty($oid_tabla)) exit ('Quizà falta definir la clave primaria');
    foreach ($claves as $clave) {
        $query_nom = "select attname
					from pg_attribute
					where attrelid='$oid_tabla' and attnum='$clave'";
        $oDBSt_resultado = $oDB->query($query_nom);
        $row = $oDBSt_resultado->fetch(\PDO::FETCH_ASSOC);
        $campo[] = $row['attname'];
    }
    return $campo;
    */
}