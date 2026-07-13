---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Ver Misas Zona"
pantalla: "misas.pantalla.ver_misas_zona"
preguntas: ["Que se puede hacer en Ver Misas Zona?", "Que campos tiene Ver Misas Zona?", "Que acciones hay en Ver Misas Zona?"]
capacidades: ["misas.ver_misas_zona.gestionar"]
endpoints: ["/src/misas/ver_misas_zona_data"]
source: "docs/catalogo/misas/pantallas/ver_misas_zona.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ver Misas Zona

## Resumen

Consulta de misas por zona y fechas (solo lectura). Sin entrada de menú en el índice; acceso vía enlaces internos.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_zona`
- `post.seleccion`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `misas.ver_misas_zona.gestionar`

## Endpoints Relacionados

- `/src/misas/ver_misas_zona_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
