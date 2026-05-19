<!--

/* Propiamente no es un página de estilo.

Debe estar con un include al principio de la página que llame a la función calendario.



Se definen unas variables en php:

	$colorColumnaUno
	$colorColumnaDos
	$table_border

Se define un estilo, para la tabla.
 th.cap		- la primera celda (cabecera)
 th.mes		- las celdas que pone el nombre del mes
 th.lletra	- las celdas que pone el nombre del día
 th.num		- las celdas que pone el número del día
 td.nom		- las celdas que pone el nombre de la persona o casa (columna izquierda)
 td.act		- las celdas que pone el nombre de la actividad
 td.nada	- las celdas que están vacías. (contienen un espacio)

*/
-->

<!-- PAGINA DE ESTILO: <?= __FILE__ ?> -->

<style>

table {
    border-collapse: collapse;
    border: 1px solid var(--planning-border);
}

.cap {
    font-size: 26pt;
    width: 10cm;
    background-color: var(--planning-nom-bg);
    color: var(--planning-cap-text);
    border: 1px solid var(--planning-nom-border);
}

.mes {
    font-size: 15pt;
    border: 1px solid var(--planning-nom-border);
    background-color: var(--planning-mes-bg);
    color: var(--planning-mes-text);
}

.diumenge, .diumengenum {
    background-color: var(--planning-diumenge-bg);
    color: var(--planning-diumenge-text);
    font-weight: bold;
}

.lletra {
    font-size: 9pt;
    border: 1px solid var(--planning-border-light);
    border-bottom-width: 0px;
}

.num {
    font-size: 9pt;
    border: 1px solid var(--planning-border-light);
    border-top-width: 0px;
}

.nom {
    font-size: 9pt;
    font-weight: bold;
    text-align: left;
    vertical-align: middle;
    padding: 2px 6px;
    border: 1px solid var(--planning-nom-border);
    background-color: var(--planning-nom-bg);
    color: var(--planning-nom-text);
}

.actsv {
    border-color: navy;
    border-width: 1px;
    background-color: #C0F0F0;
    font-size: 8pt;
}

.actsv_nomod {
    border-color: navy;
    border-width: 1px;
    background-color: #EEFFFF;
    font-size: 8pt;
}

.actpropio {
    border-color: green;
    border-width: 1px;
    background-color: #33F033;
    font-size: 8pt;
}

.actpropio_nomod {
    border-color: green;
    border-width: 1px;
    background-color: #33F033;
    font-size: 8pt;
}

.actpersonal {
    border-color: yellow;
    border-width: 1px;
    background-color: #FFFF00;
    font-size: 8pt;
}

.actsf {
    border-color: red;
    border-width: 1px;
    background-color: #FF9999;
    font-size: 8pt;
}

.actsf_nomod {
    border-color: red;
    border-width: 1px;
    background-color: #FFEEEE;
    font-size: 8pt;
}

.actotras {
    border-color: lime;
    border-width: 1px;
    background-color: #33FF99;
    font-size: 8pt;
}

.actotras_nomod {
    border-color: lime;
    border-width: 1px;
    background-color: #33FF99;
    font-size: 8pt;
}

.diumenge1,
.diumenge2 {
    background-color: var(--planning-diumenge-cell-bg);
}

.diumenge1 {
    border-style: solid;
    border-width: 0px 0px 1px 2px;
    border-bottom-color: var(--planning-border-light);
    border-left-color: var(--planning-diumenge-bg);
}

.diumenge2 {
    border-style: solid;
    border-width: 0px 0px 1px 0px;
    border-color: var(--planning-border-light);
}

.nada1 {
    border-style: solid;
    border-width: 0px 0px 1px 1px;
    border-color: var(--planning-border-light);
    font-size: 8pt;
}

.nada2 {
    border-style: solid;
    border-width: 0px 0px 1px 0px;
    border-color: var(--planning-border-light);
    font-size: 8pt;
}

tr.planning-fila-interna > td:not(.nom) {
    border-bottom-width: 1px;
    border-bottom-style: dotted;
    border-bottom-color: var(--planning-border-fila-interna);
}

tbody.planning-persona > tr:first-child > td.nom,
tr.planning-fila-persona-fin > td {
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: var(--planning-persona-separator);
}

#exportar table tbody.planning-persona > tr:first-child > td.nom {
    border-bottom: 1px solid var(--planning-persona-separator);
}

/* Anula estilos globales de la app (todo_en_uno) dentro del planning */
#exportar table th {
    background-color: var(--planning-th-bg);
    color: var(--planning-th-text);
}

#exportar table th.cap,
#exportar table td.nom {
    background-color: var(--planning-nom-bg);
    color: var(--planning-nom-text);
    border-color: var(--planning-nom-border);
}

#exportar table th.cap {
    color: var(--planning-cap-text);
}

#exportar table th.diumenge,
#exportar table th.diumengenum {
    background-color: var(--planning-diumenge-bg);
    color: var(--planning-diumenge-text);
}

#exportar table th.mes {
    background-color: var(--planning-mes-bg);
    color: var(--planning-mes-text);
}

A.link {
    color: navy;
    text-decoration: none;
}

/* link no visitado*/
A.visited {
    color: navy;
    text-decoration: none;
}

/* link visitado*/
A.hover {
    color: white;
    text-decoration: none;
}

.link_red {
    cursor: pointer;
    color: black;
    background-color: #FFFFAA;
    text-decoration: none;
}

</style>
