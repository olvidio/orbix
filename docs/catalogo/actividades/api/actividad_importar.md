---
id: "actividades.actividad_importar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_importar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_importar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["post.sel"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadImportarData"
respuesta_data: ["avisos:list<string>"]
requiere_hashb: false
errores: ["hay un error, no se ha importado"]
frontend_referencias: ["frontend/actividades/view/actividades.js"]
casos_uso: ["src\\actividades\\application\\ActividadImportar"]
tags: ["actividades", "actividad", "importar"]
estado_revision: "revisado"
---

# Actividad Importar

Importa a la propia dl las actividades de otras dl seleccionadas (`sel[]`):
crea un registro `Importada` por cada id y, si la app `procesos` esta instalada,
regenera el proceso de cada actividad (`generarProceso` con reset).

Los avisos que emite la regeneracion de procesos (p. ej. fases no aplicables)
se devuelven en `data.avisos` cuando no hubo errores.

Flujo de usuario: menu *Importar* → `actividad_que.php?modo=importar` →
listado de actividades de otras dl → marcar → boton importar.

## Endpoint

- URL: `/src/actividades/actividad_importar`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_importar.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `sel` | `array` | Si | Ids seleccionados (`id` o `id#extra`). Vacio ⇒ exito sin hacer nada. |

## Salida

- Helper: `ContestarJson::enviar`
- Exito sin avisos: `success: true` (sin payload).
- Exito con avisos: `success: true`, `data: {"avisos": ["..."]}`.
- Error: `success: false`, `mensaje` acumula un renglon por fallo.

## Permisos

- El caso de uso no valida permisos; el control de acceso esta en la UI
  (la pantalla `actividad_que?modo=importar` y su listado).

## Errores conocidos

- `hay un error, no se ha importado` + detalle (por cada id fallido)

## Casos De Uso

- `src\actividades\application\ActividadImportar`

## Frontend Relacionado

- `frontend/actividades/view/actividades.js` — `jsForm.update(form, 'importar')`
  desde el listado de busqueda en modo importar.

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadImportar`): efecto Importada +
  regeneracion de proceso y forma de `data.avisos` verificados.
- Pendiente: ejemplos reales de request/response.
