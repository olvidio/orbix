---
id: "notas.persona_nota_eliminar"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/persona_nota_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/persona_nota_eliminar.php"
entrada: []
entrada_obligatoria: ["id_pau", "sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Selección de nota no válida.", "No se ha eliminado la Nota: %s"]
frontend_referencias: []
casos_uso: ["src\\notas\\application\\PersonaNotaEliminar"]
tags: ["notas", "persona", "nota", "eliminar"]
estado_revision: "revisado"
---

# Persona Nota Eliminar

Elimina la nota seleccionada (tabla padre `e_notas`).

Elimina una `PersonaNota`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/persona_nota_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/persona_nota_eliminar.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: "ok"`. Error en `mensaje`.

## Efectos colaterales

- Elimina una `PersonaNota` a traves de la tabla padre `e_notas`.

## Objetivo funcional

Borrado desde listado de notas de una persona (`fnjs_borrar`). Token `sel`: `id_nivel#id_asignatura#tipo_acta`.

## Permisos

- Frontend dossier 1011 + `$_SESSION['oPerm']`.

## Errores conocidos

- `Selección de nota no válida.`
- `No se ha eliminado la Nota: %s`

## Casos De Uso

- `src\notas\application\PersonaNotaEliminar`

## Frontend Relacionado

- `frontend/notas/controller/form_notas_de_una_persona.php`.