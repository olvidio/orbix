---
id: "dbextern.sincro_unir"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_unir"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_unir.php"
entrada: ["post.id:integer", "post.id_nom_listas:integer", "post.id_orbix:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/dbextern/controller/ver_listas.php", "frontend/dbextern/controller/ver_orbix.php"]
casos_uso: ["src\\dbextern\\application\\UnirPersonaUseCase"]
tags: ["dbextern", "sincro", "unir"]
estado_revision: "generado"
---

# Sincro Unir

Vincula una persona de BDU con una persona de Orbix.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/sincro_unir`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_unir.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id` | `integer` | controller | No | controller |
| `id_nom_listas` | `integer` | controller | No | controller |
| `id_orbix` | `integer` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `hay un error, no se ha guardado`

## Casos De Uso

- `src\dbextern\application\UnirPersonaUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_listas.php`
- `frontend/dbextern/controller/ver_orbix.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.