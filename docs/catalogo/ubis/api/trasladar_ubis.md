---
id: "ubis.trasladar_ubis"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/trasladar_ubis"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/trasladar_ubis.php"
entrada: ["post.dl_dst:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se han seleccionado ubis."]
frontend_referencias: ["frontend/ubis/controller/trasladar_ubis.php"]
casos_uso: ["src\\ubis\\application\\TrasladarUbis"]
tags: ["ubis", "trasladar"]
estado_revision: "generado"
---

# Trasladar Ubis

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/trasladar_ubis`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/trasladar_ubis.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_dst` | `string` | application | No | application |
| `sel` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `No se han seleccionado ubis.`

## Casos De Uso

- `src\ubis\application\TrasladarUbis`

## Frontend Relacionado

- `frontend/ubis/controller/trasladar_ubis.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.