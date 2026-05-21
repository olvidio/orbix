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
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["tablonanuncios", "anuncio", "delete"]
estado_revision: "generado"
---

# Anuncio Delete

Descripcion funcional pendiente de revisar.

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

## Salida

No se ha detectado salida estandar. Revisar manualmente.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.