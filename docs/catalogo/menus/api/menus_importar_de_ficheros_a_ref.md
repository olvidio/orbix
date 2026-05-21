---
id: "menus.menus_importar_de_ficheros_a_ref"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_importar_de_ficheros_a_ref"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "frontend/menus/controller/menus_importar_de_ficheros_a_ref.php"
entrada: ["post.seguro:mixed", "post.todos:mixed"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["menus", "importar", "de", "ficheros", "a", "ref"]
estado_revision: "generado"
---

# Menus Importar De Ficheros A Ref

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/menus_importar_de_ficheros_a_ref`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `seguro` | `mixed` | controller | No | controller |
| `todos` | `mixed` | controller | No | controller |

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.