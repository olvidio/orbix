---
id: "configuracion.modulos_update"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/modulos_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/configuracion/infrastructure/ui/http/controllers/modulos_update.php"
entrada: ["post.descripcion:string", "post.id_mod:integer", "post.mod:string", "post.nom:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
errores: ["hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/configuracion/controller/modulos_update.php"]
casos_uso: ["src\\configuracion\\application\\ModulosUpdateAction"]
tags: ["configuracion", "modulos", "update"]
estado_revision: "generado"
---

# Modulos Update

Alta / baja / modificación de módulos (respuesta texto plano para AJAX legacy).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/configuracion/modulos_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/modulos_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `descripcion` | `string` | application | No | application |
| `id_mod` | `integer` | application | No | application |
| `mod` | `string` | application | No | application |
| `nom` | `string` | application | No | application |
| `sel` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\configuracion\application\ModulosUpdateAction`

## Frontend Relacionado

- `frontend/configuracion/controller/modulos_update.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.