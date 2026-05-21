---
id: "actividadtarifas.tipo_tarifa_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tipo_tarifa_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_eliminar.php"
entrada: ["post.id_tarifa:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borrar", "no se encuentra la tarifa", "hay un error, no se ha borrado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa.php"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaEliminar"]
tags: ["actividadtarifas", "tipo", "tarifa", "eliminar"]
estado_revision: "generado"
---

# Tipo Tarifa Eliminar

Endpoint backend: elimina un `TipoTarifa`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tarifa` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Mutacion: elimina un `TipoTarifa`.
- Sucesor de la rama `tar_eliminar` del dispatcher legacy `apps/actividadtarifas/controller/tarifa_ajax.php`.

## Errores conocidos

- `no sé cuál he de borrar`
- `no se encuentra la tarifa`
- `hay un error, no se ha borrado`

## Casos De Uso

- `src\actividadtarifas\application\TipoTarifaEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.