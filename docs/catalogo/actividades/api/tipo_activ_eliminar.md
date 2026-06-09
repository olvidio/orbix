---
id: "actividades.tipo_activ_eliminar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_eliminar.php"
entrada: ["post.id_tipo_activ:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["tipo de actividad no encontrado", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivEliminar"]
tags: ["actividades", "tipo", "activ", "eliminar"]
estado_revision: "generado"
---

# Tipo Activ Eliminar

Elimina un tipo de actividad. Portado del case `eliminar` del dispatcher legacy.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/tipo_activ_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina un tipo de actividad.
- Portado del case `eliminar` del dispatcher legacy.

## Errores conocidos

- `tipo de actividad no encontrado`
- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\actividades\application\TipoActivEliminar`

## Frontend Relacionado

- `frontend/actividades/controller/tipo_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.