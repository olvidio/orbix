---
id: "actividadcargos.form_cargos_personas_en_actividad_data"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/form_cargos_personas_en_actividad_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_personas_en_actividad_data.php"
entrada: ["post.id_dossier:integer", "post.id_pau:integer", "post.id_tipo:integer", "post.mod:string", "post.permiso:integer", "post.que_dl:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php"]
casos_uso: ["src\\actividadcargos\\application\\FormCargosPersonasEnActividadData"]
tags: ["actividadcargos", "form", "cargos", "personas", "en", "actividad", "data"]
estado_revision: "generado"
---

# Form Cargos Personas En Actividad Data

Datos para `form_cargos_personas_en_actividad` (vista por persona).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadcargos/form_cargos_personas_en_actividad_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_personas_en_actividad_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_dossier` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `id_tipo` | `integer` | application | No | application |
| `mod` | `string` | application | No | application |
| `permiso` | `integer` | application | No | application |
| `que_dl` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadcargos\application\FormCargosPersonasEnActividadData`

## Frontend Relacionado

- `frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.