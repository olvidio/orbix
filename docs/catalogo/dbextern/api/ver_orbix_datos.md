---
id: "dbextern.ver_orbix_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_orbix_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_orbix_datos.php"
entrada: ["post.dl:string", "post.id_nom_orbix:integer", "post.region:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/ver_orbix.php"]
casos_uso: ["src\\dbextern\\application\\VerOrbixData"]
tags: ["dbextern", "ver", "orbix", "datos"]
estado_revision: "generado"
---

# Ver Orbix Datos

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/ver_orbix_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_orbix_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller | No | controller |
| `id_nom_orbix` | `integer` | controller | No | controller |
| `region` | `string` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\dbextern\application\VerOrbixData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_orbix.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.