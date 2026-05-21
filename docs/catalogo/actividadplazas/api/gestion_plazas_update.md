---
id: "actividadplazas.gestion_plazas_update"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/gestion_plazas_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_update.php"
entrada: ["post.colName:string", "post.data:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra la actividad"]
frontend_referencias: ["frontend/actividadplazas/controller/gestion_plazas.php", "frontend/actividadplazas/controller/plazas_balance_dl.php"]
casos_uso: ["src\\actividadplazas\\application\\GestionPlazasUpdate"]
tags: ["actividadplazas", "gestion", "plazas", "update"]
estado_revision: "generado"
---

# Gestion Plazas Update

Endpoint backend: actualiza las plazas (totales, concedidas o pedidas) desde la edicion inline de `frontend\shared\web\TablaEditable`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/gestion_plazas_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `colName` | `string` | application | No | application |
| `data` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra la actividad`

## Casos De Uso

- `src\actividadplazas\application\GestionPlazasUpdate`

## Frontend Relacionado

- `frontend/actividadplazas/controller/gestion_plazas.php`
- `frontend/actividadplazas/controller/plazas_balance_dl.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.