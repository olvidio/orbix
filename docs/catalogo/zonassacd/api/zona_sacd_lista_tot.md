---
id: "zonassacd.zona_sacd_lista_tot"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd_lista_tot"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista_tot.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd_lista_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaSacdListaTot"]
tags: ["zonassacd", "zona", "sacd", "lista", "tot"]
estado_revision: "revisado"
errores: []
---

# Zona Sacd Lista Tot

Listado global de todos los sacd de la delegación con sus zonas (zona propia primero). Sacd sin zona aparece con zona vacía.

Linaje: Case get_lista_tot del legacy zona_sacd_ajax.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Listado global de todos los sacd de la delegación con sus zonas (zona propia primero). Sacd sin zona aparece con zona vacía.

## Endpoint

- URL: `/src/zonassacd/zona_sacd_lista_tot`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista_tot.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo vacío):
  - `tipo`: lista
  - `a_cabeceras`: sacd, zona, propia
  - `a_valores`: una fila por asignación sacd-zona
  - `error`: string vacío

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos en caso de uso.

## Casos De Uso

- `src\zonassacd\application\ZonaSacdListaTot`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/zonassacd/controller/zona_sacd_lista_ajax.php"]`).
