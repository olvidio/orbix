---
id: "cambios.cambio_usuario_eliminar"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cambios_CambioUsuarioEliminarData"
respuesta_data: ["ok:bool, mensaje: string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\cambios\\application\\CambioUsuarioEliminar"]
tags: ["cambios", "cambio", "usuario", "eliminar"]
estado_revision: "generado"
---

# Cambio Usuario Eliminar

Endpoint backend: elimina `CambioUsuario` por la clave compuesta `id_item_cambio#id_usuario#sfsv#aviso_tipo` recibida en `sel[]`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/cambio_usuario_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cambios_CambioUsuarioEliminarData`):
  - `ok` (`bool, mensaje: string`)

## Efectos colaterales

- Caso de uso: elimina `CambioUsuario` por la clave compuesta seleccionada en el listado (`id_item_cambio#id_usuario#sfsv#aviso_tipo`).
- Sucesor de la rama `que=eliminar` del dispatcher `apps/cambios/controller/avisos_generar_ajax.php`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.