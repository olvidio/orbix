---
id: "asignaturas.asignaturas_con_separador_data"
tipo: "endpoint"
modulo: "asignaturas"
url: "/src/asignaturas/asignaturas_con_separador_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/asignaturas/infrastructure/ui/http/controllers/asignaturas_con_separador_data.php"
entrada: ["post.op_genericas:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asignaturas_AsignaturasConSeparadorOpcionesDataData"
respuesta_data: ["a_opciones:array"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/notas/controller/asig_faltan_que.php"]
casos_uso: ["src\\asignaturas\\application\\AsignaturasConSeparadorOpcionesData"]
tags: ["asignaturas", "con", "separador", "data"]
estado_revision: "revisado"
---

# Asignaturas Con Separador Data

Devuelve el mapa `id_asignatura => nombre_asignatura` de las asignaturas activas, ordenado por
grupo (`op`) y nombre, para poblar un desplegable de selección de asignatura.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye las opciones del desplegable de asignaturas insertando un **separador visual** entre
bloques:

- Selecciona `id_asignatura`, `nombre_asignatura` y un grupo calculado `op`
  (`id_nivel` si `id_nivel < 3000`, en caso contrario `3001`) de las filas con `active = 't'`.
- Ordena por `op` y `nombre_asignatura`.
- Antes de la primera asignatura con grupo `> 3000` inserta una entrada sintética
  `3000 => '----------'` que actúa como separador entre las asignaturas "normales"
  (`id_nivel < 3000`) y el resto.
- El parámetro `op_genericas` decide si se incluyen las asignaturas de niveles genéricos: por
  defecto (`'1'`/ausente → `true`) se incluyen; con `'0'` (→ `false`) se excluyen los niveles
  `1230, 1231, 1232, 2430, 2431, 2432, 2433, 2434` (ver `getListaOpGenericas`).

## Endpoint

- URL: `/src/asignaturas/asignaturas_con_separador_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/asignaturas/infrastructure/ui/http/controllers/asignaturas_con_separador_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `op_genericas` | `string` | controller | No | Leído con `inputString($_POST, 'op_genericas', '1')`; el controller lo convierte a `bool` con `!== '0'`. `'0'` excluye los niveles genéricos; cualquier otro valor (o ausencia) los incluye |

El controller lee `op_genericas`, lo transforma a `bool` y llama a `execute(bool $op_genericas)`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace un segundo
  `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es el payload (schema `asignaturas_AsignaturasConSeparadorOpcionesDataData`):
  - `a_opciones` (`array`): mapa `id_asignatura (int) => nombre_asignatura (string)`, ordenado por
    grupo y nombre, con la entrada separadora `3000 => "----------"` intercalada entre bloques.
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

- `src\asignaturas\application\AsignaturasConSeparadorOpcionesData`

## Frontend Relacionado

- `frontend/notas/controller/asig_faltan_que.php` (consume `a_opciones` vía
  `PostRequest::getDataFromUrl` y lo monta en un `Desplegable` con
  `NotasFormSupport::desplegableOpciones`).
