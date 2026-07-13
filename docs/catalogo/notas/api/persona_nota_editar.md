---
id: "notas.persona_nota_editar"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/persona_nota_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/persona_nota_editar.php"
entrada: ["post.id_asignatura_real:integer"]
entrada_obligatoria: ["id_pau"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Selección de nota no válida.", "No se encuentra una asignatura para el nivel: %s", "No se ha guardado la nota"]
frontend_referencias: ["frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\PersonaNotaEditar"]
tags: ["notas", "persona", "nota", "editar"]
estado_revision: "revisado"
---

# Persona Nota Editar

Modifica una nota existente de persona.

Edita una `PersonaNota` existente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/persona_nota_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/persona_nota_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_asignatura_real` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: "ok"`. Error en `mensaje`.

## Objetivo funcional

Edición desde formulario de notas; mismos campos que alta (`nota_num`, `nota_max`, `id_situacion`, `acta`, `f_acta`, preceptor, época, `id_activ`, `detalle`).

## Permisos

- Frontend dossier 1011 + `$_SESSION['oPerm']`.

## Errores conocidos

- `Selección de nota no válida.`
- `No se encuentra una asignatura para el nivel: %s`
- `No se ha guardado la nota`

## Casos De Uso

- `src\notas\application\PersonaNotaEditar`

## Frontend Relacionado

- `frontend/notas/controller/form_notas_de_una_persona.php`.