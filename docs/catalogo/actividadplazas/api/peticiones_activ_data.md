---
id: "actividadplazas.peticiones_activ_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/peticiones_activ_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/peticiones_activ_data.php"
entrada: ["post.id_ctr_agd:integer", "post.id_ctr_n:integer", "post.id_nom:integer", "post.na:string", "post.que:string", "post.sactividad:string", "post.todos:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PeticionesActivDataData"
respuesta_data: ["id_nom:integer", "ap_nom:string", "na:string", "sactividad:string", "sid_activ:string", "opciones:array", "tipo:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadplazas/controller/peticiones_activ.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesActivData"]
tags: ["actividadplazas", "peticiones", "activ", "data"]
estado_revision: "revisado"
---

# Peticiones Activ Data

Data builder de la pantalla `peticiones_activ`: lista de actividades candidatas (opciones del
desplegable) + peticiones actuales de una persona para un tipo de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Resuelve el colectivo `na` (`n`, `a`/`agd`) y el tipo `sactividad` (`ca`/`cv`/`crt`); para agd con
  `ca` fuerza `cv`.
- Calcula el `id_tipo_activ` según colectivo y tipo, y el rango del curso vigente desde el
  `ConfigSnapshot`.
- Reúne las actividades candidatas (de mi dl vía `ActividadDl` + publicadas de otras dl vía
  `ActividadPub`, separadas por un `-------`) como `opciones` `id_activ => nom_activ`.
- Carga las peticiones actuales de la persona (`id_nom` + tipo) ordenadas por `orden`; **elimina del
  repositorio** las peticiones cuyas actividades ya no están en la lista candidata, y devuelve las
  vigentes en `sid_activ` (CSV de `id_activ`).

## Endpoint

- URL: `/src/actividadplazas/peticiones_activ_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_activ_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | Persona cuyas peticiones se gestionan |
| `na` | `string` | controller | No | Colectivo: `n` (numerarios) o `a`/`agd` (agregados) |
| `sactividad` | `string` | controller | No | Tipo de actividad; si vacío se toma de `que` |
| `que` | `string` | controller | No | Alias legacy de `sactividad` (fallback) |
| `todos` | `integer` | controller | No | Si no es 0/1, se interpreta como `grupo_estudios` para filtrar dl |
| `id_ctr_agd` | `integer` | controller | No | Contexto legacy (centro agd); se lee pero no filtra la lista |
| `id_ctr_n` | `integer` | controller | No | Contexto legacy (centro n); se lee pero no filtra la lista |

El controller resuelve `sactividad` con fallback a `que` antes de invocar el caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (`data` serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadplazas_PeticionesActivDataData`):
  - `id_nom` (`integer`), `ap_nom` (`string`): id y apellidos+nombre de la persona.
  - `na` (`string`): colectivo resuelto.
  - `sactividad` (`string`) y `tipo` (`string`): tipo de actividad (mismo valor).
  - `sid_activ` (`string`): CSV de `id_activ` de las peticiones vigentes (preselección del desplegable).
  - `opciones` (`array`): `id_activ => nom_activ` de las actividades candidatas (con separador `--------`).

## Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend
  (`peticiones_activ.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\PeticionesActivData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/peticiones_activ.php`
