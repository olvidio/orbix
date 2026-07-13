---
id: "asistentes.lista_asis_conjunto_activ_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_asis_conjunto_activ_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_asis_conjunto_activ_data.php"
entrada: ["post.dl_org:string", "post.empiezamax:string", "post.empiezamin:string", "post.id_tipo_activ:string", "post.id_ubi:integer", "post.modo:string", "post.nom_activ:string", "post.periodo:string", "post.que:string", "post.sactividad:string", "post.sasistentes:string", "post.sfsv:string", "post.snom_tipo:string", "post.status:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["content_html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_asis_conjunto_activ.php"]
casos_uso: ["src\\asistentes\\application\\ListaAsisConjuntoActivData"]
tags: ["asistentes", "lista", "asis", "conjunto", "activ", "data"]
estado_revision: "revisado"
---

# Lista Asis Conjunto Activ Data

Listado conjunto de plazas/asistencias por actividades filtradas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Genera HTML paginado vía `ListaPlazasConjuntoActividades`: bloque «actividades de la dl» y/o «de otras
dl» según `dl_org`. Filtros: tipo actividad (`sfsv`/`sasistentes`/`sactividad` o `id_tipo_activ`),
periodo, centro, nombre, status, modo `publicar`. `que=list_cjto_sacd` activa vista SACD.

## Endpoint

- URL: `/src/asistentes/lista_asis_conjunto_activ_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_asis_conjunto_activ_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sfsv`, `sasistentes`, `sactividad`, `snom_tipo` | `string` | application | No | Resuelven `id_tipo_activ` si vacío |
| `id_tipo_activ` | `string` | application | No | Alternativa explícita |
| `que` | `string` | application | No | `list_cjto_sacd` para SACD |
| `status` | `integer` | application | No | Default `ACTUAL` |
| `year`, `periodo`, `empiezamin`, `empiezamax` | mixed | application | No | |
| `id_ubi`, `nom_activ`, `dl_org`, `modo` | mixed | application | No | |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `content_html` (`string`): tablas HTML de plazas por actividad

## Permisos

- Sin control propio; desde `actividad_que` (`que=list_cjto`): frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\ListaAsisConjuntoActivData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_asis_conjunto_activ.php` (redir desde `actividad_que.php`).
