---
id: "actividadtarifas.tipo_tarifa_update"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tipo_tarifa_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_update.php"
entrada: ["post.id_tarifa:string", "post.letra:string", "post.modo:string", "post.observ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra la tarifa", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa.php", "frontend/actividadtarifas/controller/tarifa_form.php", "frontend/actividadtarifas/view/tarifa_form.phtml"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaUpdate"]
tags: ["actividadtarifas", "tipo", "tarifa", "update"]
estado_revision: "generado"
---

# Tipo Tarifa Update

Endpoint backend: crea o actualiza un `TipoTarifa`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tarifa` | `string` | controller+application | No | controller+application |
| `letra` | `string` | controller+application | No | controller+application |
| `modo` | `string` | controller+application | No | controller+application |
| `observ` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra la tarifa`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\actividadtarifas\application\TipoTarifaUpdate`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa.php`
- `frontend/actividadtarifas/controller/tarifa_form.php`
- `frontend/actividadtarifas/view/tarifa_form.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.