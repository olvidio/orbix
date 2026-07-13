---
id: "casas.grupo_form_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/grupo_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/casas/infrastructure/ui/http/controllers/grupo_form_data.php"
entrada: ["post.id_item:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_GrupoCasaFormDataData"
respuesta_data: ["es_nuevo:boolean", "id_item:string", "id_ubi_padre:integer", "id_ubi_hijo:integer", "opciones_casas:array"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/grupo_form.php"]
casos_uso: ["src\\casas\\application\\GrupoCasaFormData"]
tags: ["casas", "grupo", "form", "data"]
estado_revision: "revisado"
---

# Grupo Form Data

Datos del formulario de alta/edición de un `GrupoCasa`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de `apps/casas/controller/grupo_form.php`. Si `id_item` es vacío o `nuevo`, prepara un alta;
en caso contrario carga el registro existente. Devuelve las opciones del desplegable de casas activas y
los IDs seleccionados de padre/hijo.

## Endpoint

- URL: `/src/casas/grupo_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | controller+application | No | Vacío o `nuevo` = alta; numérico = edición |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `es_nuevo` (`boolean`): `true` si es alta o el `id_item` no existe.
  - `id_item` (`string`): `nuevo` en alta, o el ID recibido.
  - `id_ubi_padre` / `id_ubi_hijo` (`integer`): casas seleccionadas (0 en alta).
  - `opciones_casas` (`array<int|string,string>`): casas activas (`active = 't'`).

## Permisos

- Sin control propio; la autorización se resuelve en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\casas\application\GrupoCasaFormData`

## Frontend Relacionado

- `frontend/casas/controller/grupo_form.php`: modal de crear/editar grupo.
