---
id: "casas.casa_ingreso_form_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ingreso_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ingreso_form_data.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Falta id_activ", "Actividad no encontrada"]
frontend_referencias: ["frontend/casas/controller/casa_ingreso_form.php"]
casos_uso: ["src\\casas\\application\\CasaIngresoFormData"]
tags: ["casas", "casa", "ingreso", "form", "data"]
estado_revision: "revisado"
---

# Casa Ingreso Form Data

Datos del formulario modal de ingreso económico de una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga la actividad, resuelve permiso `id_tarifa` vía `PermisosActividades`, y devuelve tarifa/precio
actuales más los valores de `Ingreso` (ingresos, asistentes, observaciones). Si no hay `id_activ` o la
actividad no existe, devuelve `ok: false` con `error`.

## Endpoint

- URL: `/src/casas/casa_ingreso_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | Sí | Actividad a editar |

## Salida

- Helper: `ContestarJson::enviar('', $payload)` (doble `JSON.parse`).
- Éxito (`ok: true`):
  - `id_activ`, `nom_activ`, `id_tarifa`, `letra_tarifa`, `puede_modificar_tarifa`, `a_opciones_tarifa`,
    `precio`, `ingresos`, `num_asistentes`, `observ`.
- Error (`ok: false`): `error` con mensaje traducido.

## Errores conocidos

- `Falta id_activ`
- `Actividad no encontrada`

## Permisos

- `puede_modificar_tarifa`: `PermisosActividades` faceta `id_tarifa`, acción `modificar` (requiere
  `$_SESSION['oPermActividades']`).

## Casos De Uso

- `src\casas\application\CasaIngresoFormData`

## Frontend Relacionado

- `frontend/casas/controller/casa_ingreso_form.php`: modal abierto desde `fnjs_modificar` del listado
  económico (`casa_ingresos_lista`).
