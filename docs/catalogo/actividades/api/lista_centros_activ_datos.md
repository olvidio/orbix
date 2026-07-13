---
id: "actividades.lista_centros_activ_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_centros_activ_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_centros_activ_datos.php"
entrada: ["post.id_ctr_num:integer", "post.id_ctr:array", "post.periodo:string", "post.year:string", "post.empiezamin:string", "post.empiezamax:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/lista_centros_activ.php"]
casos_uso: ["src\\actividades\\application\\ListaCentrosActivDatos"]
tags: ["actividades", "lista", "centros", "activ", "datos"]
estado_revision: "revisado"
---

# Lista Centros Activ Datos

Lista los centros encargados de actividades en un periodo dado y, para cada actividad, enumera los
demás centros encargados. Devuelve el HTML del bloque listo para inyectar en el DOM.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Selecciona los centros (por defecto los de tipo `s[^s]`, o los indicados en `id_ctr`) y, para cada uno,
recupera sus actividades del periodo. Renderiza un `<h3>` por centro seguido de una tabla con el nombre
de cada actividad y los otros centros encargados (el responsable, primero, subrayado). El periodo por
defecto es `actual`; con `desdeHoy` filtra por `f_fin`.

## Endpoint

- URL: `/src/actividades/lista_centros_activ_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_centros_activ_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ctr_num` | `integer` | controller | No | Si es 0/vacío, lista todos los centros `s[^s]` |
| `id_ctr` | `array` | controller | No | Ids de centro concretos (operador `IN`) |
| `periodo` | `string` | controller | No | Por defecto `actual`; `desdeHoy` filtra por `f_fin` |
| `year` | `string` | controller | No | Año/curso |
| `empiezamin` / `empiezamax` | `string` | controller | No | Rango de fechas |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es un objeto con una única clave `html` (bloque `<h3>` + `<table>` por centro).

## Permisos

- Sin control de permisos propio en el caso de uso. La autorización se resuelve en el frontend
  (`lista_centros_activ.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividades\application\ListaCentrosActivDatos`

## Frontend Relacionado

- `frontend/actividades/controller/lista_centros_activ.php`
