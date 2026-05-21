---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadestudios"
titulo: "Form Asignaturas De Una Actividad"
pantalla: "actividadestudios.pantalla.form_asignaturas_de_una_actividad"
preguntas: ["Que se puede hacer en Form Asignaturas De Una Actividad?", "Que campos tiene Form Asignaturas De Una Actividad?", "Que acciones hay en Form Asignaturas De Una Actividad?"]
capacidades: ["actividadestudios.actividad_asignatura.gestionar", "actividadestudios.actividad_asignatura_editar.gestionar", "actividadestudios.form_asignaturas_de_una_actividad.gestionar", "actividadestudios.profesores_desplegable.gestionar"]
endpoints: ["/src/actividadestudios/actividad_asignatura_editar", "/src/actividadestudios/actividad_asignatura_nueva", "/src/actividadestudios/form_asignaturas_de_una_actividad_data", "/src/actividadestudios/profesores_desplegable_data"]
source: "docs/catalogo/actividadestudios/pantallas/form_asignaturas_de_una_actividad.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Form Asignaturas De Una Actividad

## Resumen

Form de alta / edicion de una `ActividadAsignatura` desde el dossier `asignaturas_de_una_actividad` (3005).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_activ`
- `form.id_asignatura`
- `form.salida`
- `html.avis_profesor`
- `html.f_fin`
- `html.f_ini`
- `html.guardar`
- `html.mod`
- `html.tipo`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_pau`
- `post.pau`
- `post.sel`

## Acciones Detectadas

- `fnjs_comprobar_fecha`
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_mas_profes`

## Capacidades Relacionadas

- `actividadestudios.actividad_asignatura.gestionar`
- `actividadestudios.actividad_asignatura_editar.gestionar`
- `actividadestudios.form_asignaturas_de_una_actividad.gestionar`
- `actividadestudios.profesores_desplegable.gestionar`

## Endpoints Relacionados

- `/src/actividadestudios/actividad_asignatura_editar`
- `/src/actividadestudios/actividad_asignatura_nueva`
- `/src/actividadestudios/form_asignaturas_de_una_actividad_data`
- `/src/actividadestudios/profesores_desplegable_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
