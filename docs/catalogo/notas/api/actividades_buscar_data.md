---
id: "notas.actividades_buscar_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/actividades_buscar_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/actividades_buscar_data.php"
entrada: ["post.dl_org:string", "post.f_acta_iso:string", "post.id_activ:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/actividad_buscar_form.php"]
casos_uso: ["src\\notas\\application\\ActividadesBuscarData"]
tags: ["notas", "actividades", "buscar", "data"]
estado_revision: "generado"
---

# Actividades Buscar Data

Datos (delegaciones + actividades) para el dialogo "buscar actividad" que abre `frontend/notas/controller/actividad_buscar_form.php` desde `form_notas_de_una_persona.phtml` al modificar una nota asociada a una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/actividades_buscar_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/actividades_buscar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_org` | `string` | application | No | application |
| `f_acta_iso` | `string` | application | No | application |
| `id_activ` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\notas\application\ActividadesBuscarData`

## Frontend Relacionado

- `frontend/notas/controller/actividad_buscar_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.