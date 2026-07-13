---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Imprimir equipaje"
pantalla: "inventario.pantalla.equipajes_imprimir"
preguntas: ["Que se puede hacer en Imprimir equipaje?", "Que campos tiene Imprimir equipaje?", "Que acciones hay en Imprimir equipaje?"]
capacidades: ["inventario.cabecera_pie_txt.gestionar", "inventario.equipajes_doc_casa.gestionar", "inventario.equipajes_egm.gestionar", "inventario.equipajes_lista_activ_equipaje.gestionar"]
endpoints: ["/src/inventario/cabecera_pie_txt", "/src/inventario/equipajes_doc_casa", "/src/inventario/equipajes_egm", "/src/inventario/equipajes_lista_activ_equipaje"]
source: "docs/catalogo/inventario/pantallas/equipajes_imprimir.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Imprimir equipaje

## Resumen

Compone impresión: cabecera, actividades, docs por casa, EGM.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_equipaje`

## Acciones Detectadas

- `fnjs_left_side_hide`
- `fnjs_mod_texto_equipaje`

## Capacidades Relacionadas

- `inventario.cabecera_pie_txt.gestionar`
- `inventario.equipajes_doc_casa.gestionar`
- `inventario.equipajes_egm.gestionar`
- `inventario.equipajes_lista_activ_equipaje.gestionar`

## Endpoints Relacionados

- `/src/inventario/cabecera_pie_txt`
- `/src/inventario/equipajes_doc_casa`
- `/src/inventario/equipajes_egm`
- `/src/inventario/equipajes_lista_activ_equipaje`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
