---
id: "dbextern.refrescar_bdu"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/refrescar_bdu"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/refrescar_bdu.php"
entrada: ["post.que:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
errores: ["Error al refrescar la BDU"]
frontend_referencias: ["frontend/dbextern/controller/sincro_index.php"]
casos_uso: ["src\\dbextern\\application\\RefrescarBduUseCase"]
tags: ["dbextern", "refrescar", "bdu"]
estado_revision: "revisado"
---

# Refrescar Bdu

Recrea la tabla temporal `tmp_bdu` copiando datos actuales de la BDU (operación larga, ~2–3 min).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Invoca `CopiarBDU::crearTablaTmp()`. Tras éxito el front recarga `sincro_index` para actualizar
`fecha_actualizacion` y contadores.

## Endpoint

- URL: `/src/dbextern/refrescar_bdu`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/refrescar_bdu.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | controller | No | El front envía `que=algo` (campo dummy para HashFront) |

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `success: true`, `data: "ok"`.
- Error: `success: false`, mensaje `Error al refrescar la BDU: <detalle excepción>`.

## Errores conocidos

- `Error al refrescar la BDU: …` (excepción en `crearTablaTmp`)

## Permisos

- Sin control propio; HashFront en `sincro_index.phtml` (`h2`).

## Casos De Uso

- `src\dbextern\application\RefrescarBduUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/sincro_index.php` → `fnjs_refrescar()` en `sincro_index.phtml`
