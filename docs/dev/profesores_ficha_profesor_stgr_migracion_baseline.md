# Baseline migracion `ficha_profesor_stgr`

## Pantalla

- URL legacy actual: `apps/profesores/controller/ficha_profesor_stgr.php`.
- Objetivo canonico: `frontend/profesores/controller/ficha_profesor_stgr.php`.

## Parametros de entrada

- `POST sel[]`: prioridad alta; toma `id_nom` e `id_tabla` del primer checkbox (`id_nom#id_tabla`).
- `POST id_pau`, `POST id_nom`: fallback para identificar el profesor.
- `POST id_tabla`: tipo de tabla origen (`n`, `a`, `pn`, `pa`).
- `POST stack`: recupera `id_sel` y `scroll_id` desde `Posicion`.
- `POST permiso`, `POST depende`, `POST obj_pau`: usados para enlaces de modificacion (`tablaDB_lista_ver.php`).
- `POST print`: fuerza version imprimible.

## Reglas funcionales relevantes

- Si el usuario tiene permiso de oficina `est`, se fuerza `permiso=3`.
- En ambito `rstgr`, se fuerza modo impresion (`print=1`).
- En modo normal muestra todos los bloques (segun permiso dossier).
- En modo print reduce bloques (sin director, juramento ni publicaciones) y muestra vista `*.print.phtml`.

## Salida

- HTML con bloques:
  - cabecera profesor (`nombre`, `ctr/dl`, `departamento`, flags `n/agd/sacd`)
  - curriculum
  - nombramientos
  - director (solo modo normal)
  - ampliacion
  - latin
  - juramento (solo modo normal)
  - congresos
  - publicaciones (solo modo normal)
  - docencia
- Links de modificacion via `frontend/shared/controller/tablaDB_lista_ver.php`.

## Riesgos / casos especiales

- Dependencia de permisos por dossier (`1012`, `1017`, `1018`, `1019`, `1020`, `1021`, `1022`, `1024`, `1025`).
- Dependencia de ambito (`rstgr`) para centro (`Centro` vs `CentroDl`) y modo print.
- Compatibilidad con navegacion historica via `Posicion` (`stack`).
