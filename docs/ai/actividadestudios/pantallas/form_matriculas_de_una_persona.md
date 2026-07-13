---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadestudios"
titulo: "Form Matriculas De Una Persona"
pantalla: "actividadestudios.pantalla.form_matriculas_de_una_persona"
preguntas: ["Que se puede hacer en Form Matriculas De Una Persona?", "Que campos tiene Form Matriculas De Una Persona?", "Que acciones hay en Form Matriculas De Una Persona?"]
capacidades: ["actividadestudios.form_matriculas_de_una_persona.gestionar", "actividadestudios.matricula.gestionar", "actividadestudios.matricula_editar.gestionar"]
endpoints: ["/src/actividadestudios/form_matriculas_de_una_persona_data", "/src/actividadestudios/matricula_editar", "/src/actividadestudios/matricula_nueva", "/src/notas/posibles_opcionales_data", "/src/notas/posibles_preceptores_data"]
source: "docs/catalogo/actividadestudios/pantallas/form_matriculas_de_una_persona.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Form Matriculas De Una Persona

## Resumen

Formulario de alta o edición de una `Matricula` desde los dossiers `matriculas_de_una_persona` (1303) y `matriculas_de_una_actividad` (3103). Sucesor de `apps/actividadestudios/controller/form_1303.php`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_nom`
- `html.id_asignatura`
- `html.preceptor`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.sel`

## Acciones Detectadas

- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_construir_desplegable`
- `fnjs_guardar`

## Capacidades Relacionadas

- `actividadestudios.form_matriculas_de_una_persona.gestionar`
- `actividadestudios.matricula.gestionar`
- `actividadestudios.matricula_editar.gestionar`

## Endpoints Relacionados

- `/src/actividadestudios/form_matriculas_de_una_persona_data`
- `/src/actividadestudios/matricula_editar`
- `/src/actividadestudios/matricula_nueva`
- `/src/notas/posibles_opcionales_data`
- `/src/notas/posibles_preceptores_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
