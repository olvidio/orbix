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
errores: ["no se encuentra la actividad", "hay un error, no se ha guardado", "No puede guardar estas plazas todavía… (aviso de calendario)"]
frontend_referencias: ["frontend/actividadplazas/controller/gestion_plazas.php", "frontend/actividadplazas/controller/plazas_balance_dl.php"]
casos_uso: ["src\\actividadplazas\\application\\GestionPlazasUpdate"]
tags: ["actividadplazas", "gestion", "plazas", "update"]
estado_revision: "revisado"
---

# Gestion Plazas Update

Mutación de una celda de la `frontend\shared\web\TablaEditable` de `gestion_plazas` (y también de
`plazas_balance_dl`): actualiza las plazas totales, concedidas o pedidas de una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe la fila editada (`data`) y el nombre de la columna modificada (`colName`) y persiste el cambio:

- Columna `tot` y actividad de mi dl → actualiza las plazas totales del `ActividadDl`.
- Columnas `<dl>-c` (concedidas) o `<dl>-p` (pedidas) → obtiene/crea el `ActividadPlazasDl` de mi dl
  desde el calendario común (`PlazasDlEdicion::obtenerOCrearDesdeCalendario`) y guarda las plazas.
- Si falta el registro de calendario, devuelve el aviso largo de `PlazasCalendarioMensaje::faltaRegistro()`
  explicando cómo dar de alta plazas en el calendario antes de editar.
- Si `data`/`colName` están vacíos o mal formados, o `id_activ` es 0, no hace nada (string vacío).

## Endpoint

- URL: `/src/actividadplazas/gestion_plazas_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `data` | `string` | controller | No | JSON de la fila editada (`id`, `dlorg`, `tot`, `<dl>-c`, `<dl>-p`…) |
| `colName` | `string` | controller | No | JSON con el nombre de la columna modificada (`tot`, `<dl>-c`, `<dl>-p`, `<dl>-l`) |

El controller normaliza ambos campos con `FuncTablasSupport::inputString`. `TablaEditable` envía estos
dos campos por su form interno.

## Salida

- Helper: `ContestarJson::enviar` (`enviar($mensaje, 'ok')`: el string devuelto por el caso de uso es el mensaje de error, vacío en éxito).
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra la actividad`
- `hay un error, no se ha guardado` (se concatena el detalle del repositorio)
- Aviso de calendario (`PlazasCalendarioMensaje::faltaRegistro()`): texto largo que se muestra cuando la
  actividad aún no tiene plazas en el calendario común y no se puede editar concedidas/pedidas.

## Permisos

- Sin control de permisos propio; la editabilidad de la celda la decide `GestionPlazasData` (flags
  `editable`) y la autorización de oficina se resuelve en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\GestionPlazasUpdate`

## Frontend Relacionado

- `frontend/actividadplazas/controller/gestion_plazas.php`
- `frontend/actividadplazas/controller/plazas_balance_dl.php`
