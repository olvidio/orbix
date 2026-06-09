---
id: "configuracion.parametros_update"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/parametros_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/configuracion/infrastructure/ui/http/controllers/parametros_update.php"
entrada: ["post.fin_dia:integer", "post.fin_mes:integer", "post.ini_dia:integer", "post.ini_mes:integer", "post.parametro:string", "post.valor:string"]
entrada_obligatoria: []
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["configuracion", "parametros", "update"]
estado_revision: "generado"
---

# Parametros Update

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/configuracion/parametros_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/parametros_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `fin_dia` | `integer` | controller | No | controller |
| `fin_mes` | `integer` | controller | No | controller |
| `ini_dia` | `integer` | controller | No | controller |
| `ini_mes` | `integer` | controller | No | controller |
| `parametro` | `string` | controller | No | controller |
| `valor` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

No se ha detectado salida estandar. Revisar manualmente.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.