---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cambios"
titulo: "Configurar aviso"
pantalla: "cambios.pantalla.usuario_avisos_pref"
preguntas: ["Que se puede hacer en Configurar aviso?", "Que campos tiene Configurar aviso?", "Que acciones hay en Configurar aviso?"]
capacidades: ["cambios.usuario_avisos_pref.gestionar"]
endpoints: ["/src/cambios/usuario_avisos_pref_form_data", "/src/cambios/cambio_usuario_objeto_pref_guardar", "/src/cambios/cambio_usuario_propiedad_pref_guardar_todas", "/src/cambios/cambio_usuario_propiedad_pref_preview"]
source: "docs/catalogo/cambios/pantallas/usuario_avisos_pref.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Configurar aviso

## Resumen

Formulario completo para definir una preferencia de aviso: objeto vigilado, tipo de actividad, fase de referencia, tipo de aviso, casas y propiedades con condiciones.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.dl_propia`
- `html.id_tipo_activ`
- `html.salida`
- `post.id_item_usuario_objeto`
- `post.id_usuario`
- `post.quien`
- `post.salida`
- `post.sel`

## Acciones Detectadas

- `fnjs_actualizar_fases`
- `fnjs_actualizar_propiedades`
- `fnjs_cerrar`
- `fnjs_grabar_todo`
- `fnjs_guardar_cond`
- `fnjs_mas_casas`
- `fnjs_modificar`
- `fnjs_update_div`

## Capacidades Relacionadas

- `cambios.usuario_avisos_pref.gestionar`

## Endpoints Relacionados

- `/src/cambios/usuario_avisos_pref_form_data`
- `/src/cambios/cambio_usuario_objeto_pref_guardar`
- `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas`
- `/src/cambios/cambio_usuario_propiedad_pref_preview`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
