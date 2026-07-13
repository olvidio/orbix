---
id: "asignaturas.asignaturas_map_data"
tipo: "endpoint"
modulo: "asignaturas"
url: "/src/asignaturas/asignaturas_map_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/asignaturas/infrastructure/ui/http/controllers/asignaturas_map_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asignaturas_AsignaturasMapDataData"
respuesta_data: ["a_asignaturas:array"]
requiere_hashb: false
errores: []
frontend_referencias: []
casos_uso: ["src\\asignaturas\\application\\AsignaturasMapData"]
tags: ["asignaturas", "map", "data"]
estado_revision: "revisado"
---

# Asignaturas Map Data

Devuelve el mapa `id_asignatura => nombre_corto` de todas las asignaturas, pensado para pantallas
que necesitan la correspondencia id→nombre sin montar el contenedor de asignaturas en frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Expone un único listado plano:

- Selecciona `id_asignatura` y `nombre_corto` de todas las filas de la tabla de asignaturas,
  ordenadas por `id_asignatura` (sin filtro `active`, a diferencia de
  `asignaturas_con_separador_data`).
- Devuelve el resultado como mapa `id_asignatura => nombre_corto` (`nombre_corto` puede ser `null`
  si la fila no lo tiene).
- No recibe parámetros: es un listado completo sin filtros.

## Endpoint

- URL: `/src/asignaturas/asignaturas_map_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/asignaturas/infrastructure/ui/http/controllers/asignaturas_map_data.php`

## Entrada

Sin parámetros. El controller invoca directamente `execute()` sin leer `$_POST`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace un segundo
  `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es el payload (schema `asignaturas_AsignaturasMapDataData`):
  - `a_asignaturas` (`array`): mapa `id_asignatura (int) => nombre_corto (string|null)`, ordenado
    por `id_asignatura`.
- En error (excepción durante la construcción): `success: false`, `mensaje` con el texto de la
  excepción, `data: "none"`.

## Errores conocidos

- El caso de uso no lanza errores de negocio con `_( ... )`. El único camino de error es una
  excepción inesperada (p. ej. fallo de acceso a datos), que el controller captura y propaga como
  `success: false` con el mensaje de la excepción.

## Permisos

- El caso de uso no aplica ningún control de permisos propio: la autorización se resuelve en el
  frontend y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\asignaturas\application\AsignaturasMapData`

## Frontend Relacionado

- No se han encontrado referencias literales al endpoint en `frontend/`. Está pensado como fuente
  de datos (`id_asignatura => nombre_corto`) para pantallas que consumen el mapa sin usar el
  contenedor de asignaturas.
