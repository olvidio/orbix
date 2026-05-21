---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Personas Select"
pantalla: "personas.pantalla.personas_select"
preguntas: ["Que se puede hacer en Personas Select?", "Que campos tiene Personas Select?", "Que acciones hay en Personas Select?"]
capacidades: ["personas.personas_select.gestionar"]
endpoints: ["/src/personas/personas_select_data"]
source: "docs/catalogo/personas/pantallas/personas_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Personas Select

## Resumen

Tabla de personas que cumplen la condicion introducida en `personas_que`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_dossier`
- `form.que`
- `form.sel`
- `html.id_dossier`
- `html.que`
- `post.apellido1`
- `post.apellido2`
- `post.centro`
- `post.cmb`
- `post.es_sacd`
- `post.exacto`
- `post.id_sel`
- `post.na`
- `post.nombre`
- `post.que`
- `post.scroll_id`
- `post.stack`
- `post.tabla`
- `post.tipo`

## Acciones Detectadas

- `fnjs_actividades`
- `fnjs_copiar_tessera`
- `fnjs_dossiers`
- `fnjs_enviar_formulario`
- `fnjs_ficha`
- `fnjs_ficha_profe`
- `fnjs_home`
- `fnjs_imp_certificado`
- `fnjs_imp_tessera`
- `fnjs_lista_activ`
- `fnjs_matriculas`
- `fnjs_modificar`
- `fnjs_modificar_ctr`
- `fnjs_notas`
- `fnjs_peticion_activ`
- `fnjs_posibles_ca`
- `fnjs_solo_uno`
- `fnjs_tessera`
- `fnjs_update_div`
- `fnjs_upload_certificado`

## Capacidades Relacionadas

- `personas.personas_select.gestionar`

## Endpoints Relacionados

- `/src/personas/personas_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
