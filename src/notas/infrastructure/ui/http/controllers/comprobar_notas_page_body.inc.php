<?php

declare(strict_types=1);



use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\application\AsignaturasMapData;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\ActaFinCicloInsert;
use src\notas\application\ComprobarNotasConstantsData;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\domain\value_objects\CursoStgr;
use src\notas\domain\value_objects\NotaSituacion;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;

/**
 * Cuerpo de la pantalla legacy “comprobar notas” (SQL + HTML).
 *
 * @var PDO $oDB Conexión de delegación (global tras `global_object.inc`).
 * @var string $tabla
 * @var string $tabla_txt
 */

$dbQuery = static function (PDO $db, string $sql): PDOStatement {
    $stmt = $db->query($sql);
    if ($stmt === false) {
        throw new RuntimeException('comprobar_notas: query failed');
    }

    return $stmt;
};

$constantsPayload = DependencyResolver::get(ComprobarNotasConstantsData::class)->execute();
$nivel_stgr_B = NivelStgrId::B;
$nivel_stgr_C1 = NivelStgrId::C1;
$nivel_stgr_C2 = NivelStgrId::C2;
$nivel_stgr_R = NivelStgrId::R;
$nivel_stgr_N = NivelStgrId::N;
$nota_situ_numerica = NotaSituacion::NUMERICA;
$nota_situ_cursada = NotaSituacion::CURSADA;

$requestInput = $_POST !== [] ? $_POST : $_GET;
$Qactualizar = \src\shared\domain\helpers\FuncTablasSupport::inputString($requestInput, 'actualizar');
$Qid_tabla = \src\shared\domain\helpers\FuncTablasSupport::inputString($requestInput, 'id_tabla');

[$tabla, $tabla_txt] = match ($Qid_tabla) {
    'n' => ['p_numerarios', 'Numerarios'],
    'a', 'agd' => ['p_agregados', 'Agregados'],
    default => throw new RuntimeException(
        'comprobar_notas: id_tabla no válido (' . ($Qid_tabla === '' ? 'vacío' : $Qid_tabla) . ')'
    ),
};

// Expediente agregado (todas las DL). Marcadores 9998/9999: acta=sigla DL local.
$tablaNotas = ConfigGlobal::mi_sfsv() == 2 ? 'publicf.e_notas' : 'publicv.e_notas';
$tablaNotasDl = 'e_notas_dl';
$actaFinCiclo = new ActaFinCicloInsert($oDB, $tablaNotas);

// Plan de estudios para bienio/cuadrienio terminado (defecto: vigente 2026).
$Qplan = \src\shared\domain\helpers\FuncTablasSupport::inputInt($requestInput, 'plan_estudios');
if (!in_array($Qplan, PlanEstudios::VALORES_POSIBLES, true)) {
    $Qplan = PlanEstudios::PLAN_2026;
}

/** @var AsignaturaRepositoryInterface $asignaturaRepo */
$asignaturaRepo = DependencyResolver::get(AsignaturaRepositoryInterface::class);

/**
 * Asignaturas activas en un rango de `id_nivel` para un plan.
 *
 * @return array{count: int, niveles: list<int>, in_niveles: string, in_asignaturas: string}
 */
$asignaturasDeRango = static function (int $desde, int $hasta, int $plan) use ($asignaturaRepo): array {
    [$aWhere, $aOperador] = PlanEstudiosFilter::apply($plan, [
        'active' => 't',
        'id_nivel' => "$desde,$hasta",
    ], ['id_nivel' => 'BETWEEN']);
    $asignaturas = $asignaturaRepo->getAsignaturas($aWhere, $aOperador);
    $niveles = [];
    $idsAsig = [];
    foreach ($asignaturas as $asig) {
        $niveles[] = (int) $asig->getId_nivel();
        $idsAsig[] = (int) $asig->getId_asignatura();
    }
    $niveles = array_values(array_unique($niveles));
    $idsAsig = array_values(array_unique($idsAsig));

    return [
        'count' => count($idsAsig),
        'niveles' => $niveles,
        'in_niveles' => $niveles === [] ? 'NULL' : implode(',', $niveles),
        'in_asignaturas' => $idsAsig === [] ? 'NULL' : implode(',', $idsAsig),
    ];
};

/**
 * Asignaturas activas del tramo {@see CursoStgr} para un plan.
 *
 * @return array{count: int, niveles: list<int>, in_niveles: string, in_asignaturas: string}
 */
$asignaturasDeTramo = static function (CursoStgr $curso, int $plan) use ($asignaturasDeRango): array {
    [$desde, $hasta] = $curso->rangoNiveles();

    return $asignaturasDeRango($desde, $hasta, $plan);
};

$tramoBienio = $asignaturasDeTramo(CursoStgr::BIENIO, $Qplan);
$tramoCuadrienio = $asignaturasDeTramo(CursoStgr::CUADRIENIO, $Qplan);

// c1/c2: plan 1997 = bloque año I (2100–2113) + opcional 2430;
// plan 2026 = bienio 1000–2000 + marca 9999 (sin 2430).
if ($Qplan === PlanEstudios::PLAN_2026) {
    $tramoC1C2 = $asignaturasDeRango(1000, 2000, $Qplan);
    $c1c2ConFinBienio = true;
} else {
    $tramoC1 = $asignaturasDeTramo(CursoStgr::C1, $Qplan);
    $opcional2430 = $asignaturasDeRango(2430, 2430, $Qplan);
    $niveles = array_values(array_unique(array_merge($tramoC1['niveles'], $opcional2430['niveles'])));
    $idsAsig = [];
    foreach ([$tramoC1['in_asignaturas'], $opcional2430['in_asignaturas']] as $inList) {
        if ($inList === 'NULL') {
            continue;
        }
        foreach (explode(',', $inList) as $id) {
            $idsAsig[] = (int) $id;
        }
    }
    $idsAsig = array_values(array_unique($idsAsig));
    $tramoC1C2 = [
        'count' => count($idsAsig),
        'niveles' => $niveles,
        'in_niveles' => $niveles === [] ? 'NULL' : implode(',', $niveles),
        'in_asignaturas' => $idsAsig === [] ? 'NULL' : implode(',', $idsAsig),
    ];
    $c1c2ConFinBienio = false;
}

$superada = "(n.id_situacion = " . $nota_situ_numerica . " OR n.id_situacion::text ~ '[1345]')";

$comprobarNotasUrl = static function (array $params) use ($Qid_tabla, $Qplan): string {
    $params += ['id_tabla' => $Qid_tabla, 'plan_estudios' => $Qplan];

    return HashFront::link(
        AppUrlConfig::getPublicAppBaseUrl()
        . '/frontend/notas/controller/comprobar_notas.php?'
        . http_build_query($params)
    );
};

/**
 * SQL de candidatos a c1 o c2 según el plan seleccionado.
 *
 * @param 'c1'|'c2' $destino
 */
$sqlCandidatosC1C2 = static function (
    string $destino,
    bool $soloIdNom,
) use (
    $tabla,
    $tablaNotas,
    $tramoC1C2,
    $c1c2ConFinBienio,
    $superada,
    $nivel_stgr_B,
    $nivel_stgr_C1,
    $nivel_stgr_C2,
    $nivel_stgr_R,
    $nivel_stgr_N,
): ?string {
    if ($tramoC1C2['count'] < 1 || $tramoC1C2['in_asignaturas'] === 'NULL') {
        return null;
    }
    if ($destino !== 'c1' && $destino !== 'c2') {
        throw new RuntimeException('comprobar_notas: destino c1/c2 inválido');
    }

    $excluir = $destino === 'c1' ? $nivel_stgr_C1 : $nivel_stgr_C2;
    $havingCmp = $destino === 'c1'
        ? "count(n.id_asignatura) < {$tramoC1C2['count']}"
        : "count(n.id_asignatura) >= {$tramoC1C2['count']}";
    $select = $soloIdNom
        ? 'p.id_nom'
        : 'p.nivel_stgr, p.nom, p.apellido1, p.apellido2, count(n.id_asignatura) AS NumAsig';
    $groupBy = $soloIdNom
        ? 'p.id_nom'
        : 'p.id_nom, p.nivel_stgr, p.nom, p.apellido1, p.apellido2';
    $orderBy = $soloIdNom ? '' : ' ORDER BY p.apellido1, p.apellido2, p.nom';
    $filtroPersona = "p.nivel_stgr != $nivel_stgr_B
              AND p.nivel_stgr != $nivel_stgr_R
              AND p.nivel_stgr != $excluir
              AND p.nivel_stgr != $nivel_stgr_N";

    if ($c1c2ConFinBienio) {
        // Plan 2026: marca 9999 + nº de aprobadas del bloque 1000–2000 vs catálogo.
        return "SELECT $select
            FROM $tabla p
            INNER JOIN {$tablaNotas} fin ON fin.id_nom = p.id_nom AND fin.id_asignatura = 9999
            LEFT JOIN {$tablaNotas} n ON n.id_nom = p.id_nom
                AND n.id_asignatura IN ({$tramoC1C2['in_asignaturas']})
                AND $superada
            WHERE $filtroPersona
            GROUP BY $groupBy
            HAVING $havingCmp
            $orderBy";
    }

    // Plan 1997: nº de notas del bloque año I (C1) vs catálogo.
    return "SELECT $select
        FROM $tabla p
        INNER JOIN {$tablaNotas} n ON n.id_nom = p.id_nom
            AND n.id_asignatura IN ({$tramoC1C2['in_asignaturas']})
        WHERE $filtroPersona
        GROUP BY $groupBy
        HAVING $havingCmp
        $orderBy";
};

if ($Qactualizar === 'c1') {
    $ssql = $sqlCandidatosC1C2('c1', true);
    if ($ssql !== null) {
        $oDBSt_sql = $dbQuery($oDB, $ssql);
        foreach ($oDBSt_sql->fetchAll() as $row) {
            $id_nom = $row['id_nom'];
            $dbQuery($oDB, "UPDATE $tabla SET nivel_stgr=" . $nivel_stgr_C1 . " WHERE id_nom=$id_nom");
        }
    }
}
if ($Qactualizar === 'c2') {
    $ssql = $sqlCandidatosC1C2('c2', true);
    if ($ssql !== null) {
        $oDBSt_sql = $dbQuery($oDB, $ssql);
        foreach ($oDBSt_sql->fetchAll() as $row) {
            $id_nom = $row['id_nom'];
            $dbQuery($oDB, "UPDATE $tabla SET nivel_stgr=" . $nivel_stgr_C2 . " WHERE id_nom=$id_nom");
        }
    }
}
if ($Qactualizar === 'r') {
    $ssql = "SELECT p.id_nom
		FROM $tabla p LEFT JOIN {$tablaNotas} n USING (id_nom)
		WHERE p.nivel_stgr != " . $nivel_stgr_R . " AND n.id_asignatura = 9998 
		";

    $oDBSt_sql = $dbQuery($oDB, $ssql);
    $nf = $oDBSt_sql->rowCount();

    $i = 0;
    foreach ($oDBSt_sql->fetchAll() as $row) {
        $i++;
        $id_nom = $row["id_nom"];
        $ssql_1 = "UPDATE $tabla SET nivel_stgr=" . $nivel_stgr_R . "
			WHERE id_nom=$id_nom
			";
        $dbQuery($oDB, $ssql_1);
    }
}
if ($Qactualizar === 'borrar_cursada') {

    $Qid_nom = (string)\src\shared\domain\helpers\FuncTablasSupport::inputInt($requestInput, 'id_nom');
    $Qid_asignatura = \src\shared\domain\helpers\FuncTablasSupport::inputString($requestInput, 'id_asignatura');

    $ssql = "DELETE FROM {$tablaNotas} n 
		WHERE n.id_situacion = " . $nota_situ_cursada . "
            AND id_nom = $Qid_nom
            AND id_asignatura = $Qid_asignatura
		";

    $dbQuery($oDB, $ssql);
}
if ($Qactualizar === 'caduca_cursada') {
    $oConfig = $_SESSION['oConfig'] ?? null;
    $caduca_cursada = $oConfig instanceof ConfigSnapshot ? (int) $oConfig->getCaducaCursada() : 0;
    $f_caduca_iso = (new DateTimeImmutable('today'))
        ->sub(new DateInterval('P' . $caduca_cursada . 'Y'))
        ->format('Y-m-d');

    $ssql = "SELECT p.id_nom, n.id_asignatura
		FROM $tabla p LEFT JOIN {$tablaNotas} n USING (id_nom)
		WHERE n.id_situacion = " . $nota_situ_cursada . "
            AND f_acta < '$f_caduca_iso'
		";

    $oDBSt_sql = $dbQuery($oDB, $ssql);
    $nf = $oDBSt_sql->rowCount();

    $i = 0;
    foreach ($oDBSt_sql->fetchAll() as $row) {
        $i++;
        $id_nom = $row["id_nom"];
        $id_asignatura = $row["id_asignatura"];
        $ssql_1 = "DELETE FROM {$tablaNotas}
			WHERE id_nom=$id_nom AND id_asignatura = $id_asignatura
			";
        $dbQuery($oDB, $ssql_1);
    }
}
if ($Qactualizar == "9999" && $tramoBienio['count'] > 0) {
    $ssql = "SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*),nivel_stgr
		FROM $tabla p,{$tablaNotas} n
		WHERE p.id_nom=n.id_nom AND $superada
			AND (n.id_nivel IN ({$tramoBienio['in_niveles']}) OR n.id_nivel=9999)
		GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,nivel_stgr
		HAVING count(*) >= {$tramoBienio['count']} AND Max(n.id_nivel)<>9999
		ORDER BY p.apellido1 ASC,p.apellido2 ";

    $oDBSt_sql = $dbQuery($oDB, $ssql);
    $nf = $oDBSt_sql->rowCount();

    $i = 0;
    foreach ($oDBSt_sql->fetchAll() as $row) {
        $i++;
        $id_nom = (int) $row["id_nom"];

        $ssql_2 = "UPDATE $tabla SET nivel_stgr=" . $nivel_stgr_C1 . "
			WHERE id_nom=$id_nom
			";
        $dbQuery($oDB, $ssql_2);

        $actaFinCiclo->insertIntoDl($id_nom, ActaFinCicloInsert::ID_FIN_BIENIO, $tablaNotasDl);
    }
}
if ($Qactualizar == "9998" && $tramoCuadrienio['count'] > 0) {
    $ssql = "SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*),nivel_stgr
		FROM $tabla p LEFT JOIN {$tablaNotas} n USING (id_nom)
		WHERE $superada
			AND (n.id_nivel IN ({$tramoCuadrienio['in_niveles']}) OR n.id_nivel=9998)
		GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,nivel_stgr
		HAVING count(*) >= {$tramoCuadrienio['count']} AND Max(n.id_nivel)<>9998
		ORDER BY p.apellido1,p.apellido2,nom ";

    $oDBSt_sql = $dbQuery($oDB, $ssql);
    $nf = $oDBSt_sql->rowCount();

    $i = 0;
    foreach ($oDBSt_sql->fetchAll() as $row) {
        $i++;
        $id_nom = (int) $row["id_nom"];

        $ssql_2 = "UPDATE $tabla SET nivel_stgr=" . $nivel_stgr_R . "
			WHERE id_nom=$id_nom
			";
        $dbQuery($oDB, $ssql_2);

        $actaFinCiclo->insertIntoDl($id_nom, ActaFinCicloInsert::ID_FIN_CUADRIENIO, $tablaNotasDl);
    }
}
?>
    <html>
    <head>
        <style type="text/css">
            p {
                background-color: darkgray;
            }

            p.action {
                background-color: lightgray;
            }
        </style>
    </head>
    <body topmargin="-0,5cm" background=/icons/fons.gif link=#0000ff vlink=#0000ff>
    <h2>
        <center><font color=red><?= _("Comprobación del Fichero de Notas") ?></font></center>
    </h2>
    <hr size="2" aling="center">
    </bODY>
    </html>

<?php

$planOtro = $Qplan === PlanEstudios::PLAN_2026 ? PlanEstudios::PLAN_1997 : PlanEstudios::PLAN_2026;
$goPlanOtro = $comprobarNotasUrl(['plan_estudios' => $planOtro]);
$pagPlanOtro = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$goPlanOtro');\">$planOtro</span>";
echo '<p>' . sprintf(
    _('Plan de estudios: %1$d (cambiar a %2$s). Umbral bienio: %3$d asignaturas; cuadrienio: %4$d.'),
    $Qplan,
    $pagPlanOtro,
    $tramoBienio['count'],
    $tramoCuadrienio['count']
) . '</p>';

/*1. Numerarios con el bienio terminado y sin poner que lo ha terminado */
$nf = 0;
$oDBSt_bienio = null;
if ($tramoBienio['count'] > 0) {
    $sql = "SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*) as num_asig,nivel_stgr
FROM $tabla p,{$tablaNotas} n
WHERE p.id_nom=n.id_nom AND $superada
	AND (n.id_nivel IN ({$tramoBienio['in_niveles']}) OR n.id_nivel=9999)
GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,nivel_stgr
HAVING count(*) >= {$tramoBienio['count']} AND Max(n.id_nivel)<>9999
ORDER BY p.apellido1 ASC,p.apellido2 ";

    $oDBSt_bienio = $dbQuery($oDB, $sql);
    $nf = $oDBSt_bienio->rowCount();
}
echo "<p>". sprintf(_("1. %s con el bienio terminado (plan %d) y sin poner que lo ha terminado: %d"), $tabla_txt, $Qplan, $nf) . "</p>";
echo "<p>"._("Es importante poner bien la fecha en que lo ha terminado")."</p>";
if (!empty($nf) && $oDBSt_bienio instanceof PDOStatement) {
    /* Para sacar una lista*/
    echo "<table>";
    foreach ($oDBSt_bienio->fetchAll() as $algo) {
        $nom = $algo['apellido1'] . " " . $algo['apellido2'] . ", " . $algo['nom'];
        $numasig = $algo['num_asig'];
        $nivel_stgr = $algo['nivel_stgr'];
        echo "<tr><td width=20></td>";
        echo "<td>$nom</td><td>$numasig</td><td>$nivel_stgr</td></tr>";
    }
    echo "<tr><td colspan=7><hr>";
    echo "</table>";
    /* end lista */
    $go = $comprobarNotasUrl(['actualizar' => 9999]);
    $pag = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">" . _("clic aquí") . "</span>";
    echo "<p class=action>";
    printf(_("para poner c1 y bienio finalizado a todos los de la lista, hacer %s. Esto pondrá la fecha de acta última."), $pag);
    echo "</p>";
}

/*2. Numerarios con el cuadrienio terminado y sin poner que lo ha terminado */
$nf = 0;
$oDBSt_cuadrienio = null;
if ($tramoCuadrienio['count'] > 0) {
    $sql = "SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*) as num_asig,nivel_stgr
		FROM $tabla p LEFT JOIN {$tablaNotas} n USING (id_nom)
		WHERE $superada
			AND (n.id_nivel IN ({$tramoCuadrienio['in_niveles']}) OR n.id_nivel=9998)
		GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,nivel_stgr
		HAVING count(*) >= {$tramoCuadrienio['count']} AND Max(n.id_nivel)<>9998
		ORDER BY p.apellido1,p.apellido2,nom ";

    $oDBSt_cuadrienio = $dbQuery($oDB, $sql);
    $nf = $oDBSt_cuadrienio->rowCount();
}
echo "<br><p>" . sprintf(_("2. %s con el cuadrienio terminado (plan %d) y sin poner que lo ha terminado: %d"), $tabla_txt, $Qplan, $nf) . "</p>";

if (!empty($nf) && $oDBSt_cuadrienio instanceof PDOStatement) {
    /* Para sacar una lista*/
    echo "<table>";
    foreach ($oDBSt_cuadrienio->fetchAll() as $algo) {
        $nom = $algo['apellido1'] . " " . $algo['apellido2'] . ", " . $algo['nom'];
        $numasig = $algo['num_asig'];
        $nivel_stgr = $algo['nivel_stgr'];
        echo "<tr><td width=20></td>";
        echo "<td>$nom</td><td>$numasig</td><td>$nivel_stgr</td></tr>";
    }
    echo "<tr><td colspan=7><hr>";
    echo "</table>";
    /* end lista */
    $go = $comprobarNotasUrl(['actualizar' => 9998]);
    $pag = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">" . _("clic aquí") . "</span>";
    echo "<p class=action>";
    printf(_("para poner r y cuadrienio finalizado a todos los de la lista, hacer %s. Esto pondrá la fecha de acta última."), $pag);
    echo "</p>";
}

/*3. Gente con opcionales genéricas sin fecha o ref. a la opcional que es (no cuento las de la Ratio 89)*/

/*4. Gente sin fecha en acta (no cuento las de la Ratio 89)*/

/** @var AsignaturasMapData $asignaturasMapData */
$asignaturasMapData = DependencyResolver::get(AsignaturasMapData::class);
$dAsigMap = $asignaturasMapData->execute();
$a_asignaturas_map = $dAsigMap['a_asignaturas'];

$sqlF = "SELECT  p.id_nom,p.nom, p.apellido1, p.apellido2, n.f_acta, n.id_asignatura
FROM $tabla p,{$tablaNotas} n
WHERE p.id_nom=n.id_nom AND (n.f_acta) IS NULL AND (n.id_situacion = " . $nota_situ_numerica . " OR n.id_situacion::text ~ '[34]')
ORDER BY p.apellido1,p.apellido2 ";

$oDBSt_sql = $dbQuery($oDB, $sqlF);
$nf = $oDBSt_sql->rowCount();
echo "<br><p>4. $tabla_txt con asignaturas sin fecha de acta: $nf</p>";

/* Para sacar una lista*/
echo "<table>";
foreach ($oDBSt_sql->fetchAll() as $algo) {
    $nom = $algo['apellido1'] . " " . $algo['apellido2'] . ", " . $algo['nom'];
    $fecha = $algo['f_acta'];
    $id_asignatura = $algo['id_asignatura'];
    if (empty($a_asignaturas_map[$id_asignatura])) {
        throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
    }
    $asig = $a_asignaturas_map[$id_asignatura];
    echo "<tr><td width=20></td>";
    echo "<td>$nom</td><td>$fecha</td><td>$asig</td></tr>";
}
echo "<tr><td colspan=7><hr>";
echo "</table>";
/* end lista */

// 5–6. c1 / c2 según plan (1997: bloque año I; 2026: bienio 1000–2000 + 9999).
$renderListaC1C2 = static function (
    string $destino,
    string $tituloFmt,
) use (
    $dbQuery,
    $oDB,
    $sqlCandidatosC1C2,
    $comprobarNotasUrl,
    $tabla_txt,
    $Qplan,
    $tramoC1C2,
): void {
    $ssql = $sqlCandidatosC1C2($destino, false);
    if ($ssql === null) {
        return;
    }
    $oDBSt_sql = $dbQuery($oDB, $ssql);
    $nf = $oDBSt_sql->rowCount();
    if (empty($nf)) {
        return;
    }
    echo '<br><p>' . sprintf($tituloFmt, $tabla_txt, $Qplan, $nf, $tramoC1C2['count']) . '</p>';
    echo '<table>';
    foreach ($oDBSt_sql->fetchAll() as $algo) {
        $nom = $algo['apellido1'] . ' ' . $algo['apellido2'] . ', ' . $algo['nom'];
        $nivel_stgr = $algo['nivel_stgr'];
        $asig = $algo['numasig'];
        echo '<tr><td width=20></td>';
        echo "<td>$nom</td><td>$nivel_stgr</td><td>$asig</td></tr>";
    }
    echo '<tr><td colspan=7><hr>';
    echo '</table>';
    $go = $comprobarNotasUrl(['actualizar' => $destino]);
    $pag = '<span class="link" onclick="fnjs_update_div(\'#main\',\'' . $go . '\');">' . _('clic aquí') . '</span>';
    echo '<p class=action>';
    printf($destino === 'c1'
        ? _('para poner c1 a todos los de la lista, hacer %s')
        : _('para poner c2 a todos los de la lista, hacer %s'), $pag);
    echo '</p>';
};

$renderListaC1C2(
    'c1',
    _('5. %1$s con "c1" mal puesto (plan %2$d, umbral %4$d): %3$d')
);
$renderListaC1C2(
    'c2',
    _('6. %1$s con "c2" mal puesto (plan %2$d, umbral %4$d): %3$d')
);

// 7. Comprobar que los han terminado tienen pueso r
$ssql = "SELECT p.nivel_stgr,p.nom, p.apellido1, p.apellido2
	FROM $tabla p LEFT JOIN {$tablaNotas} n USING (id_nom)
	WHERE p.nivel_stgr != " . $nivel_stgr_R . " AND n.id_asignatura = 9998
	ORDER BY apellido1,apellido2,nom";

$oDBSt_sql = $dbQuery($oDB, $ssql);
$nf = $oDBSt_sql->rowCount();
if (!empty($nf)) {
    echo "<br><p>7. $tabla_txt con \"r\" sin poner: $nf</p>";
    // Para sacar una lista
    // Para sacar una lista
    echo "<table>";
    foreach ($oDBSt_sql->fetchAll() as $algo) {
        $nom = $algo['apellido1'] . " " . $algo['apellido2'] . ", " . $algo['nom'];
        $nivel_stgr = $algo['nivel_stgr'];
        echo "<tr><td width=20></td>";
        echo "<td>$nom</td><td>$nivel_stgr</td></tr>";
    }
    echo "<tr><td colspan=7><hr>";
    echo "</table>";
    $go = $comprobarNotasUrl(['actualizar' => 'r']);
    $pag = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">" . _("clic aquí") . "</span>";

    echo "<p class=action>";
    printf(_("para poner r a todos los de la lista, hacer %s"), $pag);
    echo "</p>";
}


/*8. Gente con asignaturas cursadas sin aprobar*/
$sqlF = "SELECT  p.id_nom,p.nom, p.apellido1, p.apellido2, n.f_acta, n.id_asignatura
FROM $tabla p,{$tablaNotas} n
WHERE p.situacion != 'B' AND p.id_nom = n.id_nom AND n.id_situacion = " . $nota_situ_cursada . "
ORDER BY p.apellido1,p.apellido2 ";

$oDBSt_sql = $dbQuery($oDB, $sqlF);
$nf = $oDBSt_sql->rowCount();
echo "<br><p>8. $tabla_txt con asignaturas cursadas sin examinar: $nf</p>";

/* Para sacar una lista*/
$go = $comprobarNotasUrl(['actualizar' => 'caduca_cursada']);
$oConfig = $_SESSION['oConfig'] ?? null;
$caduca_cursada = $oConfig instanceof ConfigSnapshot ? $oConfig->getCaducaCursada() : '';

echo "<table>";
foreach ($oDBSt_sql->fetchAll() as $algo) {
    $nom = $algo['apellido1'] . " " . $algo['apellido2'] . ", " . $algo['nom'];
    $fecha = $algo['f_acta'];
    $id_asignatura = $algo['id_asignatura'];
    if (empty($a_asignaturas_map[$id_asignatura])) {
        throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
    }
    $asig = $a_asignaturas_map[$id_asignatura];
    $id_nom = $algo['id_nom'];

    $aParam = [
        'id_nom' => $id_nom,
        'id_asignatura' => $id_asignatura,
        'actualizar' => 'borrar_cursada',
    ];
    $go_borrar = $comprobarNotasUrl($aParam);
    $pag_borrar = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go_borrar');\">" . _("borrar") . "</span>";
    echo "<tr><td width=20></td>";
    echo "<td>$nom</td><td>$fecha</td><td>$asig</td><td>$pag_borrar</td></tr>";
}
echo "<tr><td colspan=7><hr>";
echo "</table>";
$pag = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">" . _("clic aquí") . "</span>";
echo "<p class=action>";
printf(_("para borrar las cursadas de más de %s años, hacer %s"), $caduca_cursada, $pag);
echo "</p>";
/* end lista */

echo "</body>";
