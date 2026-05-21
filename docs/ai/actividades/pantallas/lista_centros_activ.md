---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Lista Centros Activ"
pantalla: "actividades.pantalla.lista_centros_activ"
preguntas: ["Que se puede hacer en Lista Centros Activ?", "Que campos tiene Lista Centros Activ?", "Que acciones hay en Lista Centros Activ?"]
capacidades: ["actividades.lista_centros_activ.gestionar"]
endpoints: ["/src/actividades/lista_centros_activ_datos"]
source: "docs/catalogo/actividades/pantallas/lista_centros_activ.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Lista Centros Activ

## Resumen

Fragmento HTML con la lista de centros y sus actividades en un periodo.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_ctr`
- `post.id_ctr_num`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividades.lista_centros_activ.gestionar`

## Endpoints Relacionados

- `/src/actividades/lista_centros_activ_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
