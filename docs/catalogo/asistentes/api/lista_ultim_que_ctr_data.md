---
id: "asistentes.lista_ultim_que_ctr_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_ultim_que_ctr_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_ultim_que_ctr_data.php"
entrada: ["post.curso:string", "post.que:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_ultim_que_ctr.php"]
casos_uso: ["src\\asistentes\\application\\ListaUltimQueCtrData"]
tags: ["asistentes", "lista", "ultim", "que", "ctr", "data"]
estado_revision: "revisado"
---

# Lista Ultim Que Ctr Data

Selector de centro para informes de última asistencia.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el formulario de elección de centro (`id_ubi`) antes de abrir `lista_ultima_activ`. Propaga
`que` y `curso` en hidden. Opción `999` = «todos».

## Endpoint

- URL: `/src/asistentes/lista_ultim_que_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_ultim_que_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | application | No | Tipo informe (heredado de menú) |
| `curso` | `string` | application | No | `actual`/`anterior` si aplica |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `opciones_centros` (`object`): `id_ubi` → nombre; incluye `999` = todos
  - `hash_main`, `paths.form_action` → `lista_ultima_activ.php`

## Permisos

- Sin control propio; entrada menú vsg: frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\ListaUltimQueCtrData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_ultim_que_ctr.php` + `ListaUltimQueCtrRender`.
