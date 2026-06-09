---
id: "casas.ingreso_plazas_previstas_update"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/ingreso_plazas_previstas_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/ingreso_plazas_previstas_update.php"
entrada: ["post.colName:string", "post.data:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra el ingreso", "Hay un error, no se ha guardado"]
frontend_referencias: ["frontend/casas/controller/prevision_asistentes.php"]
casos_uso: ["src\\casas\\application\\IngresoPlazasPrevistasUpdate"]
tags: ["casas", "ingreso", "plazas", "previstas", "update"]
estado_revision: "generado"
---

# Ingreso Plazas Previstas Update

Endpoint backend: actualiza plazas previstas de un ingreso (TablaEditable).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/ingreso_plazas_previstas_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/ingreso_plazas_previstas_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `colName` | `string` | controller+application | No | controller+application |
| `data` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Mutación: actualiza `num_asistentes_previstos` de un `Ingreso`.
- Sucesor de la rama `update` del dispatcher legacy `apps/casas/controller/prevision_asistentes_ajax.php`.

## Errores conocidos

- `no se encuentra el ingreso`
- `Hay un error, no se ha guardado`

## Casos De Uso

- `src\casas\application\IngresoPlazasPrevistasUpdate`

## Frontend Relacionado

- `frontend/casas/controller/prevision_asistentes.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.