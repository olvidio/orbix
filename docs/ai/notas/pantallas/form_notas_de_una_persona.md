---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Form Notas De Una Persona"
pantalla: "notas.pantalla.form_notas_de_una_persona"
preguntas: ["Que se puede hacer en Form Notas De Una Persona?", "Que campos tiene Form Notas De Una Persona?", "Que acciones hay en Form Notas De Una Persona?"]
capacidades: ["notas.buscar_acta.gestionar", "notas.nota_persona.gestionar", "notas.persona_nota.gestionar", "notas.persona_nota_editar.gestionar", "notas.posibles_opcionales.gestionar", "notas.posibles_preceptores.gestionar"]
endpoints: ["/src/notas/buscar_acta", "/src/notas/nota_persona_form_data", "/src/notas/persona_nota_editar", "/src/notas/persona_nota_nueva", "/src/notas/posibles_opcionales_data", "/src/notas/posibles_preceptores_data"]
source: "docs/catalogo/notas/pantallas/form_notas_de_una_persona.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Form Notas De Una Persona

## Resumen

Form de alta / edicion de una `PersonaNota` de un dossier.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.acta`
- `form.dl_org`
- `form.f_acta_iso`
- `form.id_nom`
- `html.acta`
- `html.detalle`
- `html.epoca`
- `html.f_acta`
- `html.id_asignatura`
- `html.nota_max`
- `html.nota_num`
- `html.preceptor`
- `html.tipo_acta`
- `post.id_asignatura_real`
- `post.id_pau`
- `post.mod`
- `post.obj_pau`
- `post.pau`
- `post.permiso`
- `post.sel`

## Acciones Detectadas

- `fnjs_buscar_acta`
- `fnjs_buscar_ca`
- `fnjs_cerrar`
- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_comprobar_fecha`
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_nota`
- `fnjs_update_activ`
- `fnjs_update_div`

## Capacidades Relacionadas

- `notas.buscar_acta.gestionar`
- `notas.nota_persona.gestionar`
- `notas.persona_nota.gestionar`
- `notas.persona_nota_editar.gestionar`
- `notas.posibles_opcionales.gestionar`
- `notas.posibles_preceptores.gestionar`

## Endpoints Relacionados

- `/src/notas/buscar_acta`
- `/src/notas/nota_persona_form_data`
- `/src/notas/persona_nota_editar`
- `/src/notas/persona_nota_nueva`
- `/src/notas/posibles_opcionales_data`
- `/src/notas/posibles_preceptores_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
