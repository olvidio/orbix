---
id: "actividadplazas.peticiones_eliminar"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/peticiones_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/peticiones_eliminar.php"
entrada: ["post.id_nom:integer", "post.sactividad:string"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_nom / sactividad", "hay un error, no se ha podido eliminar"]
frontend_referencias: ["frontend/actividadplazas/controller/peticiones_activ.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesEliminar"]
tags: ["actividadplazas", "peticiones", "eliminar"]
estado_revision: "generado"
---

# Peticiones Eliminar

Endpoint backend: elimina todas las peticiones de una persona+tipo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/peticiones_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller+application | Si | controller+application |
| `sactividad` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina todas las peticiones de plaza para un {id_nom, tipo}.

## Errores conocidos

- `faltan parametros id_nom / sactividad`
- `hay un error, no se ha podido eliminar`

## Casos De Uso

- `src\actividadplazas\application\PeticionesEliminar`

## Frontend Relacionado

- `frontend/actividadplazas/controller/peticiones_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.