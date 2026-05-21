---
id: "casas.grupo_update"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/grupo_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/grupo_update.php"
entrada: ["post.id_item:string", "post.id_ubi_hijo:integer", "post.id_ubi_padre:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe indicar las dos casas", "No puede ser la misma casa", "no se encuentra el grupo", "Hay un error, no se ha guardado."]
frontend_referencias: ["frontend/casas/controller/grupo.php", "frontend/casas/view/grupo.phtml"]
casos_uso: ["src\\casas\\application\\GrupoCasaUpdate"]
tags: ["casas", "grupo", "update"]
estado_revision: "generado"
---

# Grupo Update

Endpoint backend: crea o actualiza un `GrupoCasa`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/grupo_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | controller+application | No | controller+application |
| `id_ubi_hijo` | `integer` | controller+application | No | controller+application |
| `id_ubi_padre` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `debe indicar las dos casas`
- `No puede ser la misma casa`
- `no se encuentra el grupo`
- `Hay un error, no se ha guardado.`

## Casos De Uso

- `src\casas\application\GrupoCasaUpdate`

## Frontend Relacionado

- `frontend/casas/controller/grupo.php`
- `frontend/casas/view/grupo.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.