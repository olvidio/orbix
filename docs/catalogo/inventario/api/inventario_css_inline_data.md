---
id: "inventario.inventario_css_inline_data"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/inventario_css_inline_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/inventario_css_inline_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "inventario_InventarioCssInlineDataData"
respuesta_data: ["css:string"]
requiere_hashb: false
frontend_referencias: ["frontend/inventario/controller/doc_imprimir_ctr.php", "frontend/inventario/controller/doc_imprimir_dlb.php"]
casos_uso: ["src\\inventario\\application\\InventarioCssInlineData"]
tags: ["inventario", "css", "inline", "data"]
estado_revision: "generado"
---

# Inventario Css Inline Data

CSS embebido para impresión de inventario (`inventario.css.php` en disco).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/inventario_css_inline_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/inventario_css_inline_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `inventario_InventarioCssInlineDataData`):
  - `css` (`string`)

## Casos De Uso

- `src\inventario\application\InventarioCssInlineData`

## Frontend Relacionado

- `frontend/inventario/controller/doc_imprimir_ctr.php`
- `frontend/inventario/controller/doc_imprimir_dlb.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.