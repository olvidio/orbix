---
id: "actividadestudios.acta_notas_definitivas_grabar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/acta_notas_definitivas_grabar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_definitivas_grabar.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer"]
entrada_obligatoria: ["id_activ", "id_asignatura"]
respuesta: "raw_response"
respuesta_data_schema: "actividadestudios_ActaNotasDefinitivasGrabarData"
respuesta_data: ["success:bool", "mensaje:string"]
requiere_hashb: false
errores: ["no encuentro la actividad", "no se puede definir cursada con preceptor", "no encuentro el acta", "debe introducir los datos del acta. No se ha guardado nada.", "falta definir el acta para alguna nota", "no encuentro la asignatura de actividad", "no encuentro la asignatura", "nota no guardada para %s porque la nota (%s) no llega al mínimo: 6", "ha cursado una opcional que no tocaba (id_nom=%s)", "está intentando poner una nota que ya existe para: %s"]
frontend_referencias: ["frontend/actividadestudios/controller/acta_notas.php"]
casos_uso: ["src\\actividadestudios\\application\\ActaNotasDefinitivasGrabar"]
tags: ["actividadestudios", "acta", "notas", "definitivas", "grabar"]
estado_revision: "revisado"
---

# Acta Notas Definitivas Grabar

Convierte las matrículas/notas borrador en `PersonaNota` definitivas (rama `que=3` del legacy
`apps/actividadestudios/controller/acta_notas_update.php`). Se invoca desde `acta_notas` al grabar
definitivas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para cada matrícula de `id_activ` + `id_asignatura`:

1. Resuelve época (`EPOCA_CA` o `EPOCA_INVIERNO` si tipo = agd+ca), corte y nota máxima de config.
2. Valida acta/fecha y preceptor; recalcula `id_situacion` si la nota no llega al corte.
3. Asigna `id_nivel` (asignatura normal vs opcionales `id_asignatura > 3000`).
4. Crea o edita `PersonaNota` (o elimina la anterior si el acta queda vacío / sin nota).

**Casos particulares**

- **Preceptor:** si la nota no llega al mínimo, acumula aviso y salta; `CURSADA` con preceptor aborta;
  usa el profesor de `ActividadAsignatura` como `id_preceptor`.
- **No preceptor:** si situación es cursada/examinado/vacía, toma la fecha del primer acta de la
  actividad; si no, exige acta con fecha.
- **Asignatura opcional (`id_asignatura > 3000`):** elige el siguiente `id_nivel` libre en
  `[1230, 1231, 2430, 2431, 2432]`; si se pasa del máximo, acumula error y continúa.
- **`switch ($acta)`:** `''` elimina nota anterior; `CURSADA` fuerza situación cursada; por defecto
  calcula numérica/examinado o elimina si no hay nota ni situación.
- **Nota ya existente en otra actividad:** acumula error y no sobrescribe.

## Endpoint

- URL: `/src/actividadestudios/acta_notas_definitivas_grabar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_definitivas_grabar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Sí | Actividad del acta |
| `id_asignatura` | `integer` | application | Sí | Asignatura del acta |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `echo json_encode(...)` (no usa `ContestarJson::enviar`).
- Forma: `raw_response` — objeto JSON plano `{success, mensaje}` (un solo parse en el front).
- Payload (schema `actividadestudios_ActaNotasDefinitivasGrabarData`):
  - `success` (`bool`): `true` si `mensaje` queda vacío.
  - `mensaje` (`string`): errores acumulados o mensaje de fallo inmediato.

## Efectos colaterales

- Crea/edita/elimina `PersonaNota` definitivas a partir de las matrículas del acta.

## Errores conocidos

- `no encuentro la actividad`
- `no se puede definir cursada con preceptor`
- `no encuentro el acta`
- `debe introducir los datos del acta. No se ha guardado nada.`
- `falta definir el acta para alguna nota`
- `no encuentro la asignatura de actividad`
- `no encuentro la asignatura`
- `nota no guardada para %s porque la nota (%s) no llega al mínimo: 6` (acumulado en `mensaje`)
- `ha cursado una opcional que no tocaba (id_nom=%s)` (acumulado)
- `está intentando poner una nota que ya existe para: %s` (acumulado)
- Mensajes de `RuntimeException` de `EditarPersonaNota` (acumulados en `mensaje`)

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`acta_notas.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\ActaNotasDefinitivasGrabar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/acta_notas.php`
