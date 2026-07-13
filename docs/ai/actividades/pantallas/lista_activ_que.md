---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Filtros listados SR/SG"
pantalla: "actividades.pantalla.lista_activ_que"
preguntas: ["Que se puede hacer en Filtros listados SR/SG?", "Que campos tiene Filtros listados SR/SG?", "Que acciones hay en Filtros listados SR/SG?"]
capacidades: ["actividades.lista_activ.gestionar"]
endpoints: ["/src/actividades/lista_activ_datos"]
source: "docs/catalogo/actividades/pantallas/lista_activ_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Filtros listados SR/SG

## Resumen

Formulario de **filtros para listados especiales** de SR/SG. Según `que`:

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.asist`
- `form.c_activ`
- `form.empiezamax`
- `form.empiezamin`
- `form.seccion`
- `form.status`
- `form.tit_list_grupo`
- `post.que`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividades.lista_activ.gestionar`

## Endpoints Relacionados

- `/src/actividades/lista_activ_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
