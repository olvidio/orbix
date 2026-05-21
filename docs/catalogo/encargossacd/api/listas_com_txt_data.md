---
id: "encargossacd.listas_com_txt_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_com_txt_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasComTxtDataData"
respuesta_data: ["a_locales:array"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_com_txt.php"]
casos_uso: ["src\\encargossacd\\application\\ListasComTxtData"]
tags: ["encargossacd", "listas", "com", "txt", "data"]
estado_revision: "generado"
---

# Listas Com Txt Data

Datos para la pantalla de textos de comunicacion (`frontend/encargossacd/controller/listas_com_txt.php`). Devuelve las opciones de idiomas configurados y el texto inicial correspondiente a la clave/idioma por defecto (`com_sacd` / `es`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/listas_com_txt_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_ListasComTxtDataData`):
  - `a_locales` (`array`)

## Casos De Uso

- `src\encargossacd\application\ListasComTxtData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_com_txt.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.