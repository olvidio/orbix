---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Centros Form Num"
pantalla: "ubis.pantalla.centros_form_num"
preguntas: ["Que se puede hacer en Centros Form Num?", "Que campos tiene Centros Form Num?", "Que acciones hay en Centros Form Num?"]
capacidades: ["ubis.centros.gestionar", "ubis.centros_form_num.gestionar"]
endpoints: ["/src/ubis/centros_form_num", "/src/ubis/centros_update"]
source: "docs/catalogo/ubis/pantallas/centros_form_num.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Centros Form Num

## Resumen

Formulario modal para editar buzón, pi y cartas de un centro DL.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.n_buzon`
- `form.num_cartas`
- `form.num_pi`
- `get.id_ubi`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Capacidades Relacionadas

- `ubis.centros.gestionar`
- `ubis.centros_form_num.gestionar`

## Endpoints Relacionados

- `/src/ubis/centros_form_num`
- `/src/ubis/centros_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
