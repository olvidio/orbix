---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "asistentes"
titulo: "Lista Est Ctr"
pantalla: "asistentes.pantalla.lista_est_ctr"
preguntas: ["Que se puede hacer en Lista Est Ctr?", "Que campos tiene Lista Est Ctr?", "Que acciones hay en Lista Est Ctr?"]
capacidades: ["asistentes.lista_est_ctr.gestionar"]
endpoints: ["/src/asistentes/lista_est_ctr_data"]
source: "docs/catalogo/asistentes/pantallas/lista_est_ctr.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Lista Est Ctr

## Resumen

Resultado del filtro `que_ctr_lista` (`lista=list_est`): estudios/asignaturas por centro.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_ubi`
- `post.n_agd`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `asistentes.lista_est_ctr.gestionar`

## Endpoints Relacionados

- `/src/asistentes/lista_est_ctr_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
