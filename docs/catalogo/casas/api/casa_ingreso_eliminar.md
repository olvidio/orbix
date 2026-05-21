---
id: "casas.casa_ingreso_eliminar"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ingreso_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ingreso_eliminar.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casa.php"]
casos_uso: ["src\\casas\\application\\CasaIngresoEliminar"]
tags: ["casas", "casa", "ingreso", "eliminar"]
estado_revision: "generado"
---

# Casa Ingreso Eliminar

Endpoint backend: eliminar el Ingreso de una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/casa_ingreso_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Use case: eliminar el Ingreso asociado a una actividad.
- Sucesor de la rama `que=eliminar` de `apps/casas/controller/casa_ajax.php`.

## Casos De Uso

- `src\casas\application\CasaIngresoEliminar`

## Frontend Relacionado

- `frontend/casas/controller/casa.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.