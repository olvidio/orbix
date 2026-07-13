---
id: "asistentes.lista_asistentes_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_asistentes_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_asistentes_data.php"
entrada: ["post.id_pau:integer", "post.queSel:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asistentes_ListaAsistentesDataData"
respuesta_data: ["nom_activ:string", "queSel:string", "aAsistentes:array"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_asistentes.php"]
casos_uso: ["src\\asistentes\\application\\ListaAsistentesData"]
tags: ["asistentes", "lista", "data"]
estado_revision: "revisado"
---

# Lista Asistentes Data

Listado de asistentes (y cargos-asistentes) de una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Combina cargos de `actividadcargos` (si instalado) con asistentes adicionales (plaza ≥ 4 si
`actividadplazas`). Con `queSel=listcl` añade datos de persona (`a_datos_cl`: estudios, edad, eap…).

## Endpoint

- URL: `/src/asistentes/lista_asistentes_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_asistentes_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_activ#nom_activ` |
| `id_pau` | `integer` | application | No | `id_activ` alternativo |
| `queSel` | `string` | application | No | `listcl` activa columnas extra |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `nom_activ` (`string`)
  - `queSel` (`string`)
  - `aAsistentes` (`array`): clave → `{nombre, a_datos_cl}`

## Permisos

- Sin control propio; acceso desde ficha actividad: frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\ListaAsistentesData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_asistentes.php` (desde `actividades.js` / navegación actividad).
