---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Textos cabecera/pie equipajes"
pantalla: "inventario.pantalla.cabecera_pie_txt"
preguntas: ["Que se puede hacer en Textos cabecera/pie equipajes?", "Que campos tiene Textos cabecera/pie equipajes?", "Que acciones hay en Textos cabecera/pie equipajes?"]
capacidades: ["inventario.cabecera_pie_txt.gestionar"]
endpoints: ["/src/inventario/cabecera_pie_txt"]
source: "docs/catalogo/inventario/pantallas/cabecera_pie_txt.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Textos cabecera/pie equipajes

## Resumen

Edita textos globales de impresión de equipajes.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.cabecera`
- `form.cabeceraB`
- `form.firma`
- `form.pie`
- `html.cabecera`
- `html.cabeceraB`
- `html.firma`
- `html.pie`

## Acciones Detectadas

- `fnjs_guardar`
- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `inventario.cabecera_pie_txt.gestionar`

## Endpoints Relacionados

- `/src/inventario/cabecera_pie_txt`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
