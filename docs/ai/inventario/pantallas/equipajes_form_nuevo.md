---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Fragmento selección actividades"
pantalla: "inventario.pantalla.equipajes_form_nuevo"
preguntas: ["Que se puede hacer en Fragmento selección actividades?", "Que campos tiene Fragmento selección actividades?", "Que acciones hay en Fragmento selección actividades?"]
capacidades: ["inventario.equipajes_lista_activ_sel.gestionar"]
endpoints: ["/src/inventario/equipajes_lista_activ_sel"]
source: "docs/catalogo/inventario/pantallas/equipajes_form_nuevo.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Fragmento selección actividades

## Resumen

AJAX selección actividades al crear equipaje.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.nom_equipaje`
- `html.nom_equipaje`
- `post.id_cdc`
- `post.nom_equip`
- `post.sel`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Capacidades Relacionadas

- `inventario.equipajes_lista_activ_sel.gestionar`

## Endpoints Relacionados

- `/src/inventario/equipajes_lista_activ_sel`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
