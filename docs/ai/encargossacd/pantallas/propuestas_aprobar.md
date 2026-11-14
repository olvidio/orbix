---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Propuestas Aprobar"
pantalla: "encargossacd.pantalla.propuestas_aprobar"
preguntas: ["Que se puede hacer en Propuestas Aprobar?", "Que campos tiene Propuestas Aprobar?", "Que acciones hay en Propuestas Aprobar?"]
capacidades: []
endpoints: ["/src/encargossacd/propuestas_aprobar"]
source: "docs/catalogo/encargossacd/pantallas/propuestas_aprobar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Propuestas Aprobar

## Resumen

Acción, no pantalla: reenvía a `/src/encargossacd/propuestas_aprobar` la cápsula `ctx_aprobar` que llega en el enlace del menú de propuestas (tras la confirmación) e imprime el texto de resultado («Hecho!») en `#main`. Abierta sin esa cápsula no aprueba nada («Operación no autorizada»).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- No hay campos detectados.

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- No hay capacidades relacionadas.

## Endpoints Relacionados

- `/src/encargossacd/propuestas_aprobar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
