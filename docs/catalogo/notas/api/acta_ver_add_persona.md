---
id: "notas.acta_ver_add_persona"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_ver_add_persona"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/acta_ver_add_persona.php"
entrada: ["post.acta:string", "post.id_nom:integer", "post.nota_num:number", "post.nota_max:integer"]
entrada_obligatoria: ["acta", "id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan acta o persona", "No se encuentra el acta", "El acta está firmada y no se puede modificar", "El acta no tiene asignatura", "error"]
frontend_referencias: ["frontend/notas/controller/acta_ver.php", "frontend/notas/view/acta_ver.phtml"]
casos_uso: ["src\\notas\\application\\ActaVerAddPersona"]
tags: ["notas", "acta", "ver", "add", "persona", "mutacion"]
estado_revision: "revisado"
---

# Acta Ver Add Persona

Añade (o actualiza) la nota de un alumno en un acta, escribiendo directamente en el dossier (`e_notas_dl` vía `EditarPersonaNota`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_ver_add_persona`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_ver_add_persona.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta` | `string` | application | Sí | |
| `id_nom` | `integer` | application | Sí | Debe ser > 0 |
| `nota_num` | `number` | application | No | Si no numérico → `null` |
| `nota_max` | `integer` | application | No | Si < 1 → `oConfig->getNotaMax()` |

HashFront en UI: campos form `id_nom!nota_num!nota_max` + hidden `acta`.

## Salida

- Helper: `ContestarJson::enviar($error, ['mensaje' => …])`
- Éxito: `mensaje` vacío en envelope; `data.mensaje` = `Nota guardada en el esquema %s` (esquema de escritura de `EditarPersonaNota`).
- Error: `mensaje` del envelope = texto de fallo; `data.mensaje` repetido.

## Objetivo funcional

Persiste una `PersonaNota` ligada al acta (asignatura/actividad/fecha del acta), con situación numérica o «examinado» según corte.

## Casos particulares

- Acta con PDF (`getpdf() !== null`) → rechazo: firmada, no modificable.
- Si ya existe nota de esa persona+asignatura+acta → `EditarPersonaNota::editar`; si no → `nuevo`.
- Situación: `NUMERICA` por defecto; si `nota_num/nota_max < nota_corte` (config) → `EXAMINADO`.
- Campos fijos al guardar: `tipo_acta=FORMATO_ACTA`, `preceptor=false`, `epoca=EPOCA_OTRO`, `detalle` vacío.
- Excepciones de `EditarPersonaNota` → `success:false` con `getMessage()` (puede no estar gettext).

## Permisos

- Sin control en el caso de uso. UI solo muestra el form con `permiso === 3` y sin `readonly` (no firmada). Ámbito `rstgr`/`r` suele ir en solo lectura en `acta_ver`.

## Errores conocidos

- `Faltan acta o persona`
- `No se encuentra el acta`
- `El acta está firmada y no se puede modificar`
- `El acta no tiene asignatura`
- `error` (fallback del controller si falta mensaje)
- Mensajes crudos de `EditarPersonaNota` / persistencia

## Casos De Uso

- `src\notas\application\ActaVerAddPersona`

## Frontend Relacionado

- Formulario en `frontend/notas/view/acta_ver.phtml` cuando `$puede_anadir_persona`; POST a `url_add_persona`.
