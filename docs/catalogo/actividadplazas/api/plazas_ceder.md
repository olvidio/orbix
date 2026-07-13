---
id: "actividadplazas.plazas_ceder"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/plazas_ceder"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/plazas_ceder.php"
entrada: ["post.id_activ:integer", "post.num_plazas:integer", "post.region_dl:string"]
entrada_obligatoria: ["id_activ", "region_dl"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / region_dl", "No tiene plazas para ceder", "No tiene plazas suficientes para ceder. Puede ceder como máximo <n>", "hay un error, no se ha guardado", "No puede guardar estas plazas todavía… (aviso de calendario)"]
frontend_referencias: ["frontend/actividadplazas/controller/resumen_plazas.php"]
casos_uso: ["src\\actividadplazas\\application\\PlazasCeder"]
tags: ["actividadplazas", "plazas", "ceder"]
estado_revision: "revisado"
---

# Plazas Ceder

Cede (o deja de ceder) plazas de mi dl a otra dl en una actividad, actualizando el array `cedidas` de
`ActividadPlazasDl`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Extrae la dl destino de `region_dl` (formato `region-dl`, se toma la parte tras el `-`).
- Obtiene/crea el `ActividadPlazasDl` de mi dl desde el calendario común
  (`PlazasDlEdicion::obtenerOCrearDesdeCalendario`); si falta el registro devuelve el aviso de calendario.
- Si `num_plazas > 0`, valida que mi dl dispone de suficientes plazas de calendario libres para ceder
  (`calendario - cedidas_totales + cedidas_a_ese_destino`).
- Si `num_plazas === 0`, elimina la cesión a esa dl; en otro caso fija `cedidas[dl] = num_plazas`.
- Guarda el `ActividadPlazasDl`.

## Endpoint

- URL: `/src/actividadplazas/plazas_ceder`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/plazas_ceder.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller | Si | Actividad sobre la que se ceden plazas |
| `region_dl` | `string` | controller | Si | dl destino en formato `region-dl`; se usa la parte tras el `-` |
| `num_plazas` | `integer` | controller | No | Nº de plazas a ceder; `0` elimina la cesión a esa dl |

## Salida

- Helper: `ContestarJson::enviar` (`enviar($mensaje, 'ok')`: el caso de uso devuelve string vacío en éxito, o el mensaje de error).
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan parametros id_activ / region_dl`
- `No tiene plazas para ceder`
- `No tiene plazas suficientes para ceder. Puede ceder como máximo <n>`
- `hay un error, no se ha guardado` (se concatena el detalle del repositorio)
- Aviso de calendario (`PlazasCalendarioMensaje::faltaRegistro()`): cuando la actividad aún no tiene
  plazas en el calendario común.

## Permisos

- Sin control de permisos propio; solo se ceden plazas de mi dl (`ConfigGlobal::mi_delef()`) y la
  autorización de oficina se resuelve en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\PlazasCeder`

## Frontend Relacionado

- `frontend/actividadplazas/controller/resumen_plazas.php` (formulario "ceder", URL emitida como `url_ceder`).
