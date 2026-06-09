---
id: "usuarios.preferencia_tabla_get"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/preferencia_tabla_get"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/preferencia_tabla_get.php"
entrada: ["post.id_tabla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "usuarios_PreferenciaTablaDataData"
respuesta_data: ["formato_tabla:string, slickgrid: array<string, mixed>|null"]
requiere_hashb: false
frontend_referencias: ["frontend/shared/web/Lista.php", "frontend/shared/web/TablaEditable.php"]
casos_uso: ["src\\usuarios\\application\\PreferenciaTablaData"]
tags: ["usuarios", "preferencia", "tabla", "get"]
estado_revision: "generado"
---

# Preferencia Tabla Get

Devuelve las preferencias de usuario necesarias para renderizar una tabla (HTML simple o SlickGrid) en el front.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/preferencia_tabla_get`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/preferencia_tabla_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tabla` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `usuarios_PreferenciaTablaDataData`):
  - `formato_tabla` (`string, slickgrid: array<string, mixed>|null`)

## Casos De Uso

- `src\usuarios\application\PreferenciaTablaData`

## Frontend Relacionado

- `frontend/shared/web/Lista.php`
- `frontend/shared/web/TablaEditable.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.