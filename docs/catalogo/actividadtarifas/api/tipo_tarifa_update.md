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
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa.php", "frontend/actividadtarifas/controller/tarifa_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaUpdate"]
tags: ["actividadtarifas", "tipo", "tarifa", "update"]
estado_revision: "revisado"
---

# Tipo Tarifa Update

Crea o actualiza un `TipoTarifa` del catálogo maestro (letra, modo, observaciones).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta cuando `id_tarifa` es `nuevo` o vacío (asigna nuevo id y `sfsv = mi_sfsv`). Edición carga el
registro por id numérico. Solo actualiza campos no vacíos (`letra`, `modo`, `observ`).

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tarifa` | `string` | application | No | `nuevo`/vacío = alta; numérico = edición |
| `letra` | `string` | application | No | Código de tarifa |
| `modo` | `string` | application | No | Id de modo (`TarifaModoId`) |
| `observ` | `string` | application | No | Observaciones |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra la tarifa`
- `hay un error, no se ha guardado` (puede incluir detalle del repositorio)

## Permisos

- Sin control propio; el listado muestra modificar solo con `have_perm_oficina('adl')` y sección
  coincidente; alta si `adl`|`pr`|`calendario`.

## Casos De Uso

- `src\actividadtarifas\application\TipoTarifaUpdate`

## Frontend Relacionado

- `frontend/actividadtarifas/view/tarifa_form.phtml`: submit del form `#frm_tarifa` vía
  `fnjs_guardar` en `tarifa.phtml`.
