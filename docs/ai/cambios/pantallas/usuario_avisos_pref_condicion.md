---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cambios"
titulo: "Modal de condición"
pantalla: "cambios.pantalla.usuario_avisos_pref_condicion"
preguntas: ["Que se puede hacer en Modal de condición?", "Que campos tiene Modal de condición?", "Que acciones hay en Modal de condición?"]
capacidades: ["cambios.cambio_usuario_propiedad_pref_item.gestionar"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_item_data"]
source: "docs/catalogo/cambios/pantallas/usuario_avisos_pref_condicion.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Modal de condición

## Resumen

Fragmento AJAX con el formulario para definir operador, valor y alcance (old/new) de una propiedad vigilada. Incluye selector de casas si la propiedad es `id_ubi`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.objeto`
- `form.operador`
- `form.propiedad`
- `form.salida`
- `form.valor`
- `post.id_item`
- `post.objeto`
- `post.propiedad`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar_cond`
- `fnjs_mas_casas`

## Capacidades Relacionadas

- `cambios.cambio_usuario_propiedad_pref_item.gestionar`

## Endpoints Relacionados

- `/src/cambios/cambio_usuario_propiedad_pref_item_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
