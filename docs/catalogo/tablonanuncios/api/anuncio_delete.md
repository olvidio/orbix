---
id: "tablonanuncios.anuncio_delete"
tipo: "endpoint"
modulo: "tablonanuncios"
url: "/src/tablonanuncios/anuncio_delete"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/tablonanuncios/infrastructure/ui/http/controllers/anuncio_delete.php"
entrada: ["post.sel:array", "post.uuid_item:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra el anuncio", "error al borrar el anuncio"]
frontend_referencias: []
casos_uso: ["src\\tablonanuncios\\application\\AnuncioDelete"]
tags: ["tablonanuncios", "anuncio", "delete"]
estado_revision: "generado"
---

# Anuncio Delete

Borrado lógico de un anuncio (marca t_eliminado).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/tablonanuncios/anuncio_delete`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/tablonanuncios/infrastructure/ui/http/controllers/anuncio_delete.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | controller | No | controller |
| `uuid_item` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Borrado lógico de un anuncio (marca t_eliminado).

## Errores conocidos

- `No se encuentra el anuncio`
- `error al borrar el anuncio`

## Casos De Uso

- `src\tablonanuncios\application\AnuncioDelete`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.