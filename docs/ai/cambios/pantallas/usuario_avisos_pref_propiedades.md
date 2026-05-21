---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cambios"
titulo: "Usuario Avisos Pref Propiedades"
pantalla: "cambios.pantalla.usuario_avisos_pref_propiedades"
preguntas: ["Que se puede hacer en Usuario Avisos Pref Propiedades?", "Que campos tiene Usuario Avisos Pref Propiedades?", "Que acciones hay en Usuario Avisos Pref Propiedades?"]
capacidades: ["cambios.cambio_usuario_objeto_pref_propiedades.gestionar"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
source: "docs/catalogo/cambios/pantallas/usuario_avisos_pref_propiedades.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Usuario Avisos Pref Propiedades

## Resumen

Controlador AJAX HTML: fragmento con la tabla de propiedades seleccionables para el `CambioUsuarioObjetoPref` indicado.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.<?= htmlspecialchars($Qobjeto, ENT_QUOTES, `
- `html.id_item_usuario_objeto_prop`
- `html.salida`
- `post.id_item_usuario_objeto`
- `post.objeto`

## Acciones Detectadas

- `fnjs_modificar`
- `fnjs_selectAll`

## Capacidades Relacionadas

- `cambios.cambio_usuario_objeto_pref_propiedades.gestionar`

## Endpoints Relacionados

- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
