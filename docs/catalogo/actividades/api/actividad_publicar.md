---
id: "actividades.actividad_publicar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_publicar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_publicar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["post.sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividades/view/actividades.js"]
casos_uso: ["src\\actividades\\application\\ActividadPublicar"]
tags: ["actividades", "actividad", "publicar"]
estado_revision: "revisado"
---

# Actividad Publicar

Marca `publicado = true` en cada actividad seleccionada (`sel[]`). Operacion
idempotente; los ids inexistentes se ignoran en silencio. No hay operacion
inversa masiva (despublicar se hace editando la ficha, radio "publicado: no").

Flujo de usuario: menu *Publicar* → `actividad_que.php?modo=publicar`
(el filtro organiza queda fijado a la propia dl) → listado → marcar → publicar.

## Endpoint

- URL: `/src/actividades/actividad_publicar`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_publicar.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `sel` | `array` | Si | Ids seleccionados (`id` o `id#extra`). Vacio ⇒ exito sin hacer nada. |

## Salida

- Helper: `ContestarJson::enviar`
- Exito: `success: true` (sin payload).
- Error: `success: false`, `mensaje` acumula un renglon por fallo de guardado.

## Permisos

- El caso de uso no valida permisos; el control de acceso esta en la UI
  (la pantalla en modo publicar solo lista actividades de la propia dl).

## Errores conocidos

- `hay un error, no se ha guardado` + detalle (por cada id fallido)

## Casos De Uso

- `src\actividades\application\ActividadPublicar`

## Frontend Relacionado

- `frontend/actividades/view/actividades.js` — `jsForm.update(form, 'publicar')`
  desde el listado de busqueda en modo publicar.

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadPublicar`): idempotencia,
  ids ignorados y ausencia de permisos en servidor verificados.
- Pendiente: ejemplos reales de request/response.
