---
id: "casas.casa_ingreso_update"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ingreso_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ingreso_update.php"
entrada: ["post.id_activ:integer", "post.id_tarifa:string", "post.ingresos:string", "post.num_asistentes:integer", "post.observ:string", "post.precio:string"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Falta id_activ", "Actividad no encontrada", "Hay un error, no se ha guardado la actividad.", "Hay un error, no se ha guardado."]
frontend_referencias: ["frontend/casas/controller/casa.php", "frontend/casas/controller/casa_ingreso_form.php"]
casos_uso: ["src\\casas\\application\\CasaIngresoUpdate"]
tags: ["casas", "casa", "ingreso", "update"]
estado_revision: "revisado"
---

# Casa Ingreso Update

Crea o actualiza el `Ingreso` de una actividad y, opcionalmente, su tarifa y precio.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `que=guardar` de `apps/casas/controller/casa_ajax.php`. Persiste `tarifa`/`precio`
en la actividad y `ingresos`/`num_asistentes`/`observ` en el registro `Ingreso`. Acepta decimales con
coma o punto.

## Endpoint

- URL: `/src/casas/casa_ingreso_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | Sí | |
| `id_tarifa` | `string` | controller+application | No | Solo si el usuario puede modificar tarifa |
| `precio` | `string` | controller+application | No | Decimal con coma/punto |
| `ingresos` | `string` | controller+application | No | Importe real |
| `num_asistentes` | `integer` | controller+application | No | Asistentes reales |
| `observ` | `string` | controller+application | No | |

## Salida

- Helper: `ContestarJson::enviar($mensaje, $data)`.
- Éxito: `success: true`, `data: ""` (cadena vacía).
- Error: mensaje en `data`, `success: false`.

## Errores conocidos

- `Falta id_activ`
- `Actividad no encontrada`
- `Hay un error, no se ha guardado la actividad.`
- `Hay un error, no se ha guardado.`

## Permisos

- Sin control propio; el formulario solo muestra tarifa editable si `casa_ingreso_form_data` devolvió
  `puede_modificar_tarifa`.

## Casos De Uso

- `src\casas\application\CasaIngresoUpdate`

## Frontend Relacionado

- `frontend/casas/controller/casa_ingreso_form.php`: `fnjs_guardar`.
