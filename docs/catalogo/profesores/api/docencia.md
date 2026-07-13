---
id: "profesores.docencia"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/docencia"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/profesores/infrastructure/ui/http/controllers/docencia.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "profesores_DocenciaListaData"
respuesta_data: ["id_tabla:string", "a_cabeceras:array", "a_valores:array"]
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/docencia.php"]
casos_uso: ["src\\profesores\\application\\DocenciaLista"]
tags: ["profesores", "docencia"]
estado_revision: "revisado"
---

# Docencia

Listado global de docencia STGR registrada: por cada profesor activo muestra delegación (en RSTGR),
nombre, curso de inicio, asignatura, modo de impartición y acta.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/docencia`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/profesores/infrastructure/ui/http/controllers/docencia.php`

## Entrada

Sin parámetros POST. Lee todos los profesores con docencia en `d_docencia_stgr`.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `success: true`, `data` con tabla lista.
- `id_tabla`: `tabla_docencia`.
- `a_cabeceras`: columnas 1–6 (`dl` solo en RSTGR; apellidos/nombre, inicio curso, asignatura,
  modo, acta).
- `a_valores`: filas indexadas; cada fila mapea columnas numéricas a valores de texto.

## Objetivo funcional

Consulta de solo lectura del registro histórico de docencia. Los datos se alimentan con
**actualizar docencia** (`actividadestudios/docencia_actualizar`) al cerrar cursos.

## Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']` (menú `stgr2`).

## Casos De Uso

- `src\profesores\application\DocenciaLista`

## Frontend Relacionado

- `frontend/profesores/controller/docencia.php` — renderiza `Lista` con `docencia.phtml`.
- Linaje: `apps/profesores/controller/docencia.php`.
