---
id: "encargossacd.listas_a_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_a_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_a_data.php"
entrada: ["post.sf:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasADataData"
respuesta_data: ["cabecera_left:string", "cabecera_right:string", "cabecera_right_2:string", "Html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_a.php"]
casos_uso: ["src\\encargossacd\\application\\ListasAData"]
tags: ["encargossacd", "listas", "a", "data"]
estado_revision: "revisado"
---
# Listas A Data

Genera el listado de atencion SACD "a" (cr 9/05, Anexo2, 9.4 a). Sustituye la logica que habia en `frontend/encargossacd/controller/listas_a.php`. Devuelve el HTML completo junto con los textos de cabecera, listos para inyectarlos en la vista `listas.phtml`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Genera listado impreso «a» (cr 9/05, Anexo2, 9.4 a): centros SV/SF con sacd titular. Sucesor de `listas_a.php`.

## Endpoint

- URL: `/src/encargossacd/listas_a_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_a_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sf` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`.
- Claves: `cabecera_left`, `cabecera_right`, `cabecera_right_2`, `Html` (doble `JSON.parse`).


## Permisos

Sin control propio; acceso vía menú listados.

## Casos De Uso

- `src\encargossacd\application\ListasAData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_a.php`

