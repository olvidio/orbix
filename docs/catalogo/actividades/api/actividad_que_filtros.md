---
id: "actividades.actividad_que_filtros"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_que_filtros"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_que_filtros.php"
entrada: ["post.dl_org:string", "post.filtro_lugar:string", "post.id_ubi:integer", "post.modo:string", "post.publicado:integer", "post.sfsv:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadQueFiltrosData"
respuesta_data: ["html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_que.php", "frontend/actividades/view/actividad_que.html.twig"]
casos_uso: ["src\\actividades\\application\\ActividadQueFiltrosBloque"]
tags: ["actividades", "actividad", "que", "filtros"]
estado_revision: "revisado"
---

# Actividad Que Filtros

Devuelve en `data.html` las filas `<tr>` del bloque **filtros extra** de la
pantalla `actividad_que`: lugar segun pais/dl (`filtro_lugar`), lugar concreto
(`id_ubi`), organiza (`dl_org`) y radios de publicada. La pantalla lo inyecta
via AJAX en `<tbody id="filtros_extra">` al cargar.

Comportamiento segun `modo`:

- `buscar` (defecto): desplegable organiza completo, con radios publicada.
- `importar`: excluye la propia dl del desplegable organiza y **omite** los
  radios de publicada.
- `publicar`: el desplegable organiza queda fijado a la propia dl (sin blanco).

Si la app `procesos` esta instalada, el desplegable organiza lleva
`onchange=fnjs_actualizar_fases()` (los cuadros de fases dependen de la dl).

Valores de `publicado` (radios): `1` = si, `2` = no, `3` (u otro) = todas.

## Endpoint

- URL: `/src/actividades/actividad_que_filtros`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `consulta` (sin efectos)
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_que_filtros.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `sfsv` | `integer` | No | `0` ⇒ se usa `ConfigGlobal::mi_sfsv()`. |
| `modo` | `string` | No | `buscar` (defecto) / `importar` / `publicar`. |
| `dl_org` | `string` | No | Preseleccion del desplegable organiza. |
| `filtro_lugar` | `string` | No | Preseleccion; si llega, tambien se pinta el desplegable de casas. |
| `id_ubi` | `integer` | No | Preseleccion del desplegable de casas. |
| `publicado` | `integer` | No | Radio preseleccionado (1/2/otro). |

## Salida

- Helper: `ContestarJson::enviar`
- `data`: `{ "html": "<tr>…</tr><tr>…</tr>" }`.
- Para usuarios sin permiso de control: `{ "html": "" }` (el bloque no se pinta).

## Permisos

- El bloque solo se devuelve si el rol del usuario **no** es un rol PAU de
  centro (`Role::isRolePau('ctr')` devuelve false). Es decir: los roles de
  centro no ven los filtros lugar/organiza/publicada.

## Casos De Uso

- `src\actividades\application\ActividadQueFiltrosBloque`
- `src\actividades\application\ActividadLugar` (opciones del desplegable de casas)

## Frontend Relacionado

- `frontend/actividades/controller/actividad_que.php` (prepara URL + hash)
- `frontend/actividades/view/actividad_que.html.twig` — `fnjs_cargar_filtros_extra()`

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadQueFiltrosBloque`): semantica de
  `modo`, valores de `publicado` y regla real del permiso (`!isRolePau('ctr')`) verificadas.
- Pendiente: ejemplos reales de request/response.
