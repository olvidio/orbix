---
id: "encargossacd.ctr_ficha_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/ctr_ficha_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/ctr_ficha_data.php"
entrada: ["post.filtro_ctr:mixed", "post.id_ubi:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_CtrFichaDataData"
respuesta_data: ["filtro_ctr:int, opciones_seccion: array<string, string>"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/ctr_ficha.php"]
casos_uso: ["src\\encargossacd\\application\\CtrFichaData"]
tags: ["encargossacd", "ctr", "ficha", "data"]
estado_revision: "revisado"
---
# Ctr Ficha Data

Datos de la pantalla `ctr_ficha`: - calcula el `filtro_ctr` efectivo a partir del centro (cuando no viene del POST) - devuelve las `opciones_seccion` para el desplegable de grupo de ctrs. Reemplaza la lectura directa de repos y el acceso a `EncargoAplicacionService` que el frontend hacia en `ctr_ficha.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Calcula el `filtro_ctr` efectivo a partir del centro (`id_ubi`) cuando no viene en POST y devuelve `opciones_seccion` para el desplegable de grupo de ctrs en la ficha ctr. Sucesor de la carga inicial de `apps/encargossacd/controller/ctr_ficha.php`.

## Endpoint

- URL: `/src/encargossacd/ctr_ficha_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/ctr_ficha_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro_ctr` | `mixed` | controller | No | controller |
| `id_ubi` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `success: true`, `data` JSON-string con `{filtro_ctr: int, opciones_seccion: object<string,string>}` (doble `JSON.parse`).


## Permisos

Sin control propio; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\CtrFichaData`

## Frontend Relacionado

- `frontend/encargossacd/controller/ctr_ficha.php`

