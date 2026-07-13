---
id: "inventario.inventario_css_inline_data"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/inventario_css_inline_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/inventario/infrastructure/ui/http/controllers/inventario_css_inline_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/doc_imprimir_ctr.php", "frontend/inventario/controller/doc_imprimir_dlb.php"]
casos_uso: ["src\inventario\application\InventarioCssInlineData"]
tags: ["inventario", "css", "inline", "data"]
estado_revision: "revisado"
---

# CSS embebido para impresión

Lee `inventario.css.php` del disco y devuelve el CSS inline para vistas de impresión de inventario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lee `inventario.css.php` del disco y devuelve el CSS inline para vistas de impresión de inventario.

## Endpoint

- URL: `/src/inventario/inventario_css_inline_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/inventario_css_inline_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| *(ninguno)* | — | — | — | Sin parámetros en controller |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{css}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

src\inventario\application\InventarioCssInlineData

## Frontend Relacionado

- `frontend/inventario/controller/doc_imprimir_ctr.php`
- `frontend/inventario/controller/doc_imprimir_dlb.php`
