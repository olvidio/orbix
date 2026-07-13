---
id: "notas.persona_nota_nueva"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/persona_nota_nueva"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/persona_nota_nueva.php"
entrada: []
entrada_obligatoria: ["id_pau"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Selección de nota no válida.", "No se encuentra una asignatura para el nivel: %s", "No se ha guardado la nota"]
frontend_referencias: ["frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\PersonaNotaNueva"]
tags: ["notas", "persona", "nota", "nueva"]
estado_revision: "revisado"
---

# Persona Nota Nueva

Crea una nota de persona (asignatura/nivel/tipo acta) con replicación DL/certificado.

Crea una `PersonaNota`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/persona_nota_nueva`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/persona_nota_nueva.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: "ok"`. Error en `mensaje`.

## Objetivo funcional

Alta de nota en dossier 1011 / `form_notas_de_una_persona`. Parsea `sel` como `id_nivel#id_asignatura#tipo_acta` o campos sueltos; delega en `EditarPersonaNota::nuevo()`.

## Permisos

- Frontend dossier 1011 + `$_SESSION['oPerm']`; sin `perm_*` en caso de uso.

## Errores conocidos

- `Selección de nota no válida.`
- `No se encuentra una asignatura para el nivel: %s`
- `No se ha guardado la nota`

## Casos De Uso

- `src\notas\application\PersonaNotaNueva`

## Frontend Relacionado

- `frontend/notas/controller/form_notas_de_una_persona.php`.