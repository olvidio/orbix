---
id: "ubis.trasladar_ubis"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/trasladar_ubis"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/trasladar_ubis.php"
entrada: ["post.dl_dst:string", "post.sel:string"]
entrada_obligatoria: ["dl_dst", "sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se han seleccionado ubis."]
frontend_referencias: ["frontend/ubis/controller/trasladar_ubis.php"]
casos_uso: ["src\\ubis\\application\\TrasladarUbis"]
tags: ["ubis", "trasladar"]
estado_revision: "revisado"
---

# Trasladar Ubis

Traslada centros y casas seleccionados a otra delegación destino.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Traslada centros y casas seleccionados a otra delegación destino.

## Endpoint

- URL: `/src/ubis/trasladar_ubis`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/trasladar_ubis.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_dst` | `string` | application | Si | |
| `sel` | `mixed` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `ok`: 1

## Errores conocidos
- `No se han seleccionado ubis.`

## Permisos

have_perm_oficina(admin_sv): botón trasladar en list_ctr frontend.

## Casos De Uso

- `src\ubis\application\TrasladarUbis`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/trasladar_ubis.php"]`).
