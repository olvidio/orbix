---
id: "zonassacd.zona_ctr"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_ctr"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_ctr.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "zonassacd_ZonaCtrPageData"
respuesta_data: ["a_opciones:array", "perm_des:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_ctr.php", "frontend/zonassacd/controller/zona_ctr_lista_ajax.php", "frontend/zonassacd/controller/zona_ctr_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaCtrPage"]
tags: ["zonassacd", "zona", "ctr"]
estado_revision: "revisado"
errores: []
---

# Zona Ctr

Datos iniciales de Zonas-ctr: desplegable de zonas y perm_des (activa opción no_sf, checkboxes y botón asignar).

Linaje: Migrado desde apps/zonassacd/controller/zona_ctr.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Datos iniciales de Zonas-ctr: desplegable de zonas y perm_des (activa opción no_sf, checkboxes y botón asignar).

## Endpoint

- URL: `/src/zonassacd/zona_ctr`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo vacío):
  - `a_opciones`: map id_zona=>nombre
  - `perm_des`: boolean permiso des/vcsd

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

perm_des vía have_perm_oficina('des'|'vcsd').

## Casos De Uso

- `src\zonassacd\application\ZonaCtrPage`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/zonassacd/controller/zona_ctr.php", "frontend/zonassacd/controller/zona_ctr_lista_ajax.php", "frontend/zonassacd/controller/zona_ctr_update_ajax.php"]`).
