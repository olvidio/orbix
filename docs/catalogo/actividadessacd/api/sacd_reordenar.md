---
id: "actividadessacd.sacd_reordenar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacd_reordenar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacd_reordenar.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.num_orden:string"]
entrada_obligatoria: ["id_activ", "id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_nom", "direccion de orden incorrecta (mas / menos)"]
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SacdReordenar"]
tags: ["actividadessacd", "sacd", "reordenar"]
estado_revision: "generado"
---

# Sacd Reordenar

Endpoint backend: reordena un sacd dentro de una actividad (mas / menos prioridad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/sacd_reordenar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_reordenar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | application |
| `id_nom` | `integer` | application | Si | application |
| `num_orden` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan parametros id_activ / id_nom`
- `direccion de orden incorrecta (mas / menos)`

## Casos De Uso

- `src\actividadessacd\application\SacdReordenar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.