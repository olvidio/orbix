---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cartaspresentacion"
titulo: "Cartas Presentacion Buscar"
pantalla: "cartaspresentacion.pantalla.cartas_presentacion_buscar"
preguntas: ["Que se puede hacer en Cartas Presentacion Buscar?", "Que campos tiene Cartas Presentacion Buscar?", "Que acciones hay en Cartas Presentacion Buscar?"]
capacidades: ["cartaspresentacion.cartas_presentacion_buscar.gestionar"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_buscar_data"]
source: "docs/catalogo/cartaspresentacion/pantallas/cartas_presentacion_buscar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Cartas Presentacion Buscar

## Resumen

Formulario de búsqueda de cartas de presentación por población, región, país y delegación.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.btn_ok`
- `html.poblacion`
- `html.region`
- `html.pais`
- `html.dl`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`

## Capacidades Relacionadas

- `cartaspresentacion.cartas_presentacion_buscar.gestionar`

## Endpoints Relacionados

- `/src/cartaspresentacion/cartas_presentacion_buscar_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
