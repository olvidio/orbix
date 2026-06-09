---
id: "actividades.tipo_activ_nuevo"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_nuevo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_nuevo.php"
entrada: ["post.iactividad_val:string", "post.iasistentes_val:string", "post.id_nom_tipo_activ:string", "post.isfsv_val:string", "post.nom_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivNuevo"]
tags: ["actividades", "tipo", "activ", "nuevo"]
estado_revision: "generado"
---

# Tipo Activ Nuevo

Crea un nuevo tipo de actividad. Portado del case `nuevo` del dispatcher legacy. Devuelve cadena vacia si todo va bien o un texto de error/aviso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/tipo_activ_nuevo`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_nuevo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `iactividad_val` | `string` | application | No | application |
| `iasistentes_val` | `string` | application | No | application |
| `id_nom_tipo_activ` | `string` | application | No | application |
| `isfsv_val` | `string` | application | No | application |
| `nom_tipo_activ` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\TipoActivNuevo`

## Frontend Relacionado

- `frontend/actividades/controller/tipo_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.