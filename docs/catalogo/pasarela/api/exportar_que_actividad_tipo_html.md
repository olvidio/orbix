---
id: "pasarela.exportar_que_actividad_tipo_html"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/exportar_que_actividad_tipo_html"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/exportar_que_actividad_tipo_html.php"
entrada: ["post.id_tipo_activ:string", "post.sactividad:string", "post.sasistentes:string", "post.snom_tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/pasarela/controller/exportar_que.php"]
casos_uso: ["src\\pasarela\\application\\ExportarQueActividadTipoHtml"]
tags: ["pasarela", "exportar", "que", "actividad", "tipo", "html"]
estado_revision: "generado"
---

# Exportar Que Actividad Tipo Html

HTML del selector de tipo de actividad para la pantalla «exportar qué». Replica la configuración que antes hacía {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/exportar_que_actividad_tipo_html`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/exportar_que_actividad_tipo_html.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller+application | No | controller+application |
| `sactividad` | `string` | controller+application | No | controller+application |
| `sasistentes` | `string` | controller+application | No | controller+application |
| `snom_tipo` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`
- Permiso oficina `calendario`
- Permiso oficina `admin_sf`

## Casos De Uso

- `src\pasarela\application\ExportarQueActividadTipoHtml`

## Frontend Relacionado

- `frontend/pasarela/controller/exportar_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.