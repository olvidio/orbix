---
id: "casas.grupo_eliminar"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/grupo_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/grupo_eliminar.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe indicar el grupo a eliminar", "no se encuentra el grupo", "Hay un error, no se ha eliminado."]
frontend_referencias: ["frontend/casas/controller/grupo.php", "frontend/casas/view/grupo.phtml"]
casos_uso: ["src\\casas\\application\\GrupoCasaEliminar"]
tags: ["casas", "grupo", "eliminar"]
estado_revision: "generado"
---

# Grupo Eliminar

Endpoint backend: elimina un `GrupoCasa`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/grupo_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Mutación: elimina un `GrupoCasa` por `id_item`.
- Sucesor de la rama `eliminar` de `apps/casas/controller/grupo_ajax.php`.

## Errores conocidos

- `debe indicar el grupo a eliminar`
- `no se encuentra el grupo`
- `Hay un error, no se ha eliminado.`

## Casos De Uso

- `src\casas\application\GrupoCasaEliminar`

## Frontend Relacionado

- `frontend/casas/controller/grupo.php`
- `frontend/casas/view/grupo.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.