---
id: "asistentes.asistente_mover_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/asistente_mover_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/asistente_mover_data.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/asistente_mover.php"]
casos_uso: ["src\\asistentes\\application\\AsistenteMoverData"]
tags: ["asistentes", "asistente", "mover", "data"]
estado_revision: "revisado"
---

# Asistente Mover Data

Payload del modal para mover un asistente a otra actividad del mismo tipo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el desplegable de actividades destino (curso vigente del tipo de la actividad origen),
con plazas libres/concedidas y créditos CA si aplica. Incluye peticiones preferidas de
`actividadplazas` al inicio del listado. Emite `hash_main` para el submit a `asistente_guardar`
con `mod=mover`.

## Endpoint

- URL: `/src/asistentes/asistente_mover_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_mover_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_nom#...`; alternativa a `id_nom` |
| `id_activ` | `integer` | application | Si* | Actividad origen (`id_activ_old` en hidden) |
| `id_pau` | `integer` | application | No | Override de `id_nom` extraído de `sel` |
| `id_nom` | `integer` | application | No | Alternativa sin `sel` |

\* Debe resolverse el par asistente/actividad origen.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en frontend).
- Payload en `data`:
  - `aviso_txt` (`string`): error de permiso o asistente no encontrado; vacío si OK.
  - `observ` (`string`)
  - `paths.guardar` (`string`): `src/asistentes/asistente_guardar`
  - `hash_main` (`object`): `campos_no`, `campos_form`, `campos_hidden` (`id_nom`, `id_activ_old`, `mod=mover`, `propio`, `plaza`, `propietario`)
  - `opciones_actividades` (`object`): `id_activ` → etiqueta con plazas y créditos

## Errores conocidos

- Mensajes en `aviso_txt` (no envelope de error): `no se encuentra el asistente...`, `los datos de asistencia los modifica la dl del asistente`.

## Permisos

- Comprueba `perm_modificar()`; si falla, devuelve `aviso_txt` sin opciones.

## Casos De Uso

- `src\asistentes\application\AsistenteMoverData`

## Frontend Relacionado

- `frontend/asistentes/controller/asistente_mover.php`: carga payload y `AsistenteMoverRender::enrich`.
