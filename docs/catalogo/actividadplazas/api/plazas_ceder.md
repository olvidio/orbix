---
id: "actividadplazas.plazas_ceder"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/plazas_ceder"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/plazas_ceder.php"
entrada: ["post.id_activ:integer", "post.num_plazas:integer", "post.region_dl:string"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / region_dl", "hay un error, no se ha guardado", "No tiene plazas para ceder"]
frontend_referencias: ["frontend/actividadplazas/controller/resumen_plazas.php"]
casos_uso: ["src\\actividadplazas\\application\\PlazasCeder"]
tags: ["actividadplazas", "plazas", "ceder"]
estado_revision: "generado"
---

# Plazas Ceder

Endpoint backend: actualiza el array `cedidas` de `ActividadPlazasDl` para ceder (o quitar) plazas de `mi_dele` a otra dl en una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/plazas_ceder`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/plazas_ceder.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | Si | controller+application |
| `num_plazas` | `integer` | controller+application | No | controller+application |
| `region_dl` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan parametros id_activ / region_dl`
- `hay un error, no se ha guardado`
- `No tiene plazas para ceder`

## Casos De Uso

- `src\actividadplazas\application\PlazasCeder`

## Frontend Relacionado

- `frontend/actividadplazas/controller/resumen_plazas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.