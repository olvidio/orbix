---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "menus"
titulo: "Restaurar menús ref→DL"
pantalla: "menus.pantalla.menus_importar_de_ficheros_a_ref"
preguntas: ["Que se puede hacer en Restaurar menús ref→DL?", "Que campos tiene Restaurar menús ref→DL?", "Que acciones hay en Restaurar menús ref→DL?"]
capacidades: []
endpoints: ["/src/menus/menus_importar_de_ficheros_a_ref"]
source: "docs/catalogo/menus/pantallas/menus_importar_de_ficheros_a_ref.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Restaurar menús ref→DL

## Resumen

Página HTML del endpoint `/src/menus/menus_importar_de_ficheros_a_ref`: confirma y ejecuta la copia masiva de menús de referencia (public) hacia `aux_*` del/los esquema(s). **No lee ficheros SQL** (ese flujo es `menus_exportar_ref_a_ficheros?accion=importar`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `get.seguro`
- `get.todos`
- `post.seguro`
- `post.todos`

## Acciones Detectadas

- `fnjs_update_div`

## Capacidades Relacionadas

- No hay capacidades relacionadas.

## Endpoints Relacionados

- `/src/menus/menus_importar_de_ficheros_a_ref`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
