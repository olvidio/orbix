---
id: "casas.ingreso_plazas_previstas_update"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/ingreso_plazas_previstas_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/ingreso_plazas_previstas_update.php"
entrada: ["post.colName:string", "post.data:string"]
entrada_obligatoria: ["data", "colName"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra el ingreso", "Hay un error, no se ha guardado"]
frontend_referencias: ["frontend/casas/controller/prevision_asistentes.php"]
casos_uso: ["src\\casas\\application\\IngresoPlazasPrevistasUpdate"]
tags: ["casas", "ingreso", "plazas", "previstas", "update"]
estado_revision: "revisado"
---

# Ingreso Plazas Previstas Update

Actualiza `num_asistentes_previstos` de un `Ingreso` desde `TablaEditable`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `update` de `apps/casas/controller/prevision_asistentes_ajax.php`. Recibe `data` y
`colName` como JSON (formato SlickGrid): extrae `id` (=`id_activ`) y el valor de la columna editada
(normalmente `previstas`).

## Endpoint

- URL: `/src/casas/ingreso_plazas_previstas_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/ingreso_plazas_previstas_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `data` | `string` | controller+application | Sí | JSON con `id` y campos de fila |
| `colName` | `string` | controller+application | Sí | JSON string del nombre de columna editada |

## Salida

- Helper: `ContestarJson::enviar($error, 'ok')`.
- Éxito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra el ingreso`
- `Hay un error, no se ha guardado` (+ texto de repositorio)

## Permisos

- Sin control propio; la tabla solo se muestra si `prevision_asistentes_data` devolvió `permitido: true`.

## Casos De Uso

- `src\casas\application\IngresoPlazasPrevistasUpdate`

## Frontend Relacionado

- `frontend/casas/controller/prevision_asistentes.php`: callback `onCellChange` de `TablaEditable`.
