---
id: "zonassacd.zona_sacd_lista"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista.php"
entrada: ["post.id_zona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd_lista_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaSacdLista"]
tags: ["zonassacd", "zona", "sacd", "lista"]
estado_revision: "revisado"
errores: []
---

# Zona Sacd Lista

Tabla de sacd de una zona (id_zona numérico), sacd sin zona (id_zona=no) o vacía si id_zona inválido. Columnas propia y días L–D como x/-.

Linaje: Case get_lista del legacy zona_sacd_ajax.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Tabla de sacd de una zona (id_zona numérico), sacd sin zona (id_zona=no) o vacía si id_zona inválido. Columnas propia y días L–D como x/-.

## Endpoint

- URL: `/src/zonassacd/zona_sacd_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo vacío):
  - `tipo`: tabla
  - `id_tabla`: zona_sacd_ajax
  - `a_cabeceras`: sacd, zona, propia, L..D
  - `a_botones`: modificar si perm_des
  - `con_sel`: boolean
  - `a_valores`: filas con sel=id_nom
  - `error`: string vacío

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

con_sel y botón modificar solo con perm_des (des/vcsd).

## Casos De Uso

- `src\zonassacd\application\ZonaSacdLista`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/zonassacd/controller/zona_sacd_lista_ajax.php"]`).
