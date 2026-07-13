---
id: "zonassacd.zona_sacd"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "zonassacd_ZonaSacdPageData"
respuesta_data: ["a_opciones:array", "perm_des:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd.php", "frontend/zonassacd/controller/zona_sacd_lista_ajax.php", "frontend/zonassacd/controller/zona_sacd_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaSacdPage"]
tags: ["zonassacd", "zona", "sacd"]
estado_revision: "revisado"
errores: []
---

# Zona Sacd

Datos iniciales de la pantalla Zonas-sacd: opciones del desplegable de zonas y flag perm_des que activa checkboxes, botones de asignación y modal de días.

Linaje: Migrado desde apps/zonassacd/controller/zona_sacd.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Datos iniciales de la pantalla Zonas-sacd: opciones del desplegable de zonas y flag perm_des que activa checkboxes, botones de asignación y modal de días.

## Endpoint

- URL: `/src/zonassacd/zona_sacd`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo vacío):
  - `a_opciones`: map id_zona=>nombre para desplegable
  - `perm_des`: boolean permiso oficina des o vcsd

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

perm_des vía have_perm_oficina('des'|'vcsd') en sesión; sin validación en caso de uso.

## Casos De Uso

- `src\zonassacd\application\ZonaSacdPage`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/zonassacd/controller/zona_sacd.php", "frontend/zonassacd/controller/zona_sacd_lista_ajax.php", "frontend/zonassacd/controller/zona_sacd_update_ajax.php"]`).
