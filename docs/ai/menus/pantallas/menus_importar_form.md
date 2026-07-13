---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "menus"
titulo: "Menus Importar Form"
pantalla: "menus.pantalla.menus_importar_form"
preguntas: ["Que se puede hacer en Menus Importar Form?", "Que campos tiene Menus Importar Form?", "Que acciones hay en Menus Importar Form?"]
capacidades: ["menus.lista_templates.gestionar", "menus.menus_importar.gestionar"]
endpoints: ["/src/menus/lista_templates", "/src/menus/menus_importar"]
source: "docs/catalogo/menus/pantallas/menus_importar_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Menus Importar Form

## Resumen

Selecciona plantilla (`lista_templates`) e importa al esquema activo (destructivo).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_template_menu`
- `html.btn_ok`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_importar`

## Capacidades Relacionadas

- `menus.lista_templates.gestionar`
- `menus.menus_importar.gestionar`

## Endpoints Relacionados

- `/src/menus/lista_templates`
- `/src/menus/menus_importar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
