---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Listados calendario (casas/oficinas)"
pantalla: "actividades.pantalla.calendario_listas"
preguntas: ["Que se puede hacer en Listados calendario (casas/oficinas)?", "Que campos tiene Listados calendario (casas/oficinas)?", "Que acciones hay en Listados calendario (casas/oficinas)?"]
capacidades: ["actividades.calendario_listas.gestionar"]
endpoints: ["/src/actividades/calendario_listas_datos"]
source: "docs/catalogo/actividades/pantallas/calendario_listas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Listados calendario (casas/oficinas)

## Resumen

Fragmento **HTML por AJAX** con listados del *nuevo calendario*: actividades por oficina, casas comunes, todas las casas, variantes SV/SF, lista de centros encargados (`que=lista_cdc`), etc. El parámetro `que` determina el informe; `ver_ctr=si` incluye columna de centros encargados cuando aplica.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.que`
- `post.ver_ctr`
- `post.year`
- `post.yeardefault`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividades.calendario_listas.gestionar`

## Endpoints Relacionados

- `/src/actividades/calendario_listas_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
