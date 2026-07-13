---
id: "zonassacd.zona_ctr_lista"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_ctr_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_lista.php"
entrada: ["post.id_zona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_ctr_lista_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaCtrLista"]
tags: ["zonassacd", "zona", "ctr", "lista"]
estado_revision: "revisado"
errores: []
---

# Zona Ctr Lista

Centros activos de una zona (id numérico), sin zona dl (no) o sin zona sf (no_sf). Centros sf solo visibles con perm_des.

Linaje: Case get_lista del legacy zona_ctr_ajax.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Centros activos de una zona (id numérico), sin zona dl (no) o sin zona sf (no_sf). Centros sf solo visibles con perm_des.

## Endpoint

- URL: `/src/zonassacd/zona_ctr_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo vacío):
  - `tipo`: tabla
  - `id_tabla`: zona_ctr_ajax
  - `a_cabeceras`: centro, zona
  - `con_sel`: boolean
  - `a_valores`: filas sel=id_ubi; clase tono2 en sf

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

con_sel y centros sf requieren perm_des.

## Casos De Uso

- `src\zonassacd\application\ZonaCtrLista`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/zonassacd/controller/zona_ctr_lista_ajax.php"]`).
