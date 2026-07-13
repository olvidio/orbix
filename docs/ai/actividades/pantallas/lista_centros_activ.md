---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Listado actividades por centro"
pantalla: "actividades.pantalla.lista_centros_activ"
preguntas: ["Que se puede hacer en Listado actividades por centro?", "Que campos tiene Listado actividades por centro?", "Que acciones hay en Listado actividades por centro?"]
capacidades: ["actividades.lista_centros_activ.gestionar"]
endpoints: ["/src/actividades/lista_centros_activ_datos"]
source: "docs/catalogo/actividades/pantallas/lista_centros_activ.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Listado actividades por centro

## Resumen

Fragmento **HTML devuelto por AJAX** (`AjaxJsonSupport::html`) con el listado de centros seleccionados y sus actividades en el periodo indicado. Se invoca desde `actividades_centro_que` cuando `tipo_lista` es `crt` o `cv`; el HTML se inyecta en `#exportar`.

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
