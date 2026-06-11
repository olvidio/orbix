---
id: "zonassacd.zona_sacd_update"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_update.php"
entrada: ["post.acumular:integer", "post.id_zona:string", "post.id_zona_new:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaSacdUpdate"]
tags: ["zonassacd", "zona", "sacd", "update"]
estado_revision: "revisado"
---

# Zona Sacd Update

Mueve, crea o elimina asignaciones sacd↔zona para los sacd marcados (`sel[]`).
El comportamiento depende de `acumular`:

- **`acumular=1` (cambiar asignación zona)**: cambia la zona **propia** del sacd.
  - Si `id_zona` (origen) es `'no'`/`'0'`: crea una asignación propia nueva en `id_zona_new`.
  - Si `id_zona_new` es `'no'`: elimina la asignación existente en la zona origen.
  - En otro caso: actualiza `id_zona` de la asignación existente (`propia = true`).
- **`acumular=2` (añadir asignación iglesia/cgi)**: gestiona asignaciones **no propias**.
  - Si `id_zona_new` es `'no'`: elimina la asignación del sacd en la zona origen.
  - En otro caso: crea (o reutiliza) una asignación en `id_zona_new` con `propia = false`.

Si `id_zona_new` viene vacío, no hace nada y responde éxito.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_sacd_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acumular` | `integer` | controller | No | controller |
| `id_zona` | `string` | controller | No | controller |
| `id_zona_new` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Errores parciales de persistencia van acumulados en `mensaje` (un renglón por fallo).

## Permisos

- El caso de uso no valida permisos; el control de acceso está en la UI
  (checkboxes y botones solo con permiso oficina `des` o `vcsd`).

## Casos De Uso

- `src\zonassacd\application\ZonaSacdUpdate`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_sacd_update_ajax.php`

## Revision Manual

- Revisado jun 2026 (lectura de `ZonaSacdUpdate::execute`): semantica de `acumular`,
  destino `'no'` y errores parciales documentados.
- Pendiente: ejemplos reales de request/response.