---
id: "actividadcargos.form_cargos_de_actividad_data"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/form_cargos_de_actividad_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_de_actividad_data.php"
entrada: ["post.id_dossier:integer", "post.id_nom:integer", "post.id_pau:integer", "post.mod:string", "post.obj_pau:string", "post.permiso:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadcargos/controller/form_cargos_de_actividad.php"]
casos_uso: ["src\\actividadcargos\\application\\FormCargosDeActividadData"]
tags: ["actividadcargos", "form", "cargos", "de", "actividad", "data"]
estado_revision: "generado"
---

# Form Cargos De Actividad Data

Datos para `form_cargos_de_actividad`. Los desplegables se construyen en el front ({

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadcargos/form_cargos_de_actividad_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_de_actividad_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_dossier` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `mod` | `string` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `permiso` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadcargos\application\FormCargosDeActividadData`

## Frontend Relacionado

- `frontend/actividadcargos/controller/form_cargos_de_actividad.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.