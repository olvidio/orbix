---
id: "encargossacd.propuestas_lista_enc_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/propuestas_lista_enc_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/propuestas_lista_enc_data.php"
entrada: ["post.filtro_ctr:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_PropuestasListaEncDataData"
respuesta_data: ["html:string", "error?:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/propuestas_lista_enc.php"]
casos_uso: ["src\\encargossacd\\application\\PropuestasListaEncData"]
errores: ["Debe crear la tabla de propuestas"]
tags: ["encargossacd", "propuestas", "lista", "enc", "data"]
estado_revision: "revisado"
---
# Propuestas Lista Enc Data

Listado HTML de propuestas agrupadas por encargo/centro (vista de solo lectura). Usa
`PropuestasEncargosUbiHtml::simple` sobre los centros del filtro.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el HTML de «listado propuestas por encargos». Si no hay tablas staging, devuelve
`html` vacío y `error` con el mensaje de crear tabla.

Con `filtro_ctr` reconocido (1–5) filtra centros; con valor por defecto (`0` u otro) y
`todosEnDefault: true` lista todos los centros activos SV+SF.

## Endpoint

- URL: `/src/encargossacd/propuestas_lista_enc_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/propuestas_lista_enc_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro_ctr` | `integer` | controller | No | POST/GET; 0 → todos los centros (`todosEnDefault`) |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `{html: string}`.
- Sin tablas: `{html: "", error: "Debe crear la tabla de propuestas"}`.
- FE: si `error` no vacío, imprime el error; si no, imprime `html`.

## Errores conocidos

- `Debe crear la tabla de propuestas` (en clave `error`, no en sobre `mensaje`)

## Permisos

Sin control propio; frontend + menú. Pendiente: confirmar oficina requerida.

## Casos De Uso

- `src\encargossacd\application\PropuestasListaEncData`

## Frontend Relacionado

- `frontend/encargossacd/controller/propuestas_lista_enc.php`

## Notas

- El menú `propuestas_menu` enlaza con `sel=nagd`, pero este endpoint/FE solo leen `filtro_ctr`
  (`sel` se ignora). Pendiente: limpiar query o usar `sel`.
- Relación pantallas↔API: no está en `relaciones/pantallas_api.md` (índice generado previo).
