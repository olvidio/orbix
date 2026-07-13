---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Centros Form Labor"
pantalla: "ubis.pantalla.centros_form_labor"
preguntas: ["Que se puede hacer en Centros Form Labor?", "Que campos tiene Centros Form Labor?", "Que acciones hay en Centros Form Labor?"]
capacidades: ["ubis.centros.gestionar", "ubis.centros_form_labor.gestionar"]
endpoints: ["/src/ubis/centros_form_labor", "/src/ubis/centros_update"]
source: "docs/catalogo/ubis/pantallas/centros_form_labor.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Centros Form Labor

## Resumen

Formulario modal para editar tipo de centro y tipo de labor de un centro DL.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.tipo_ctr`
- `form.tipo_labor`
- `get.id_ubi`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Capacidades Relacionadas

- `ubis.centros.gestionar`
- `ubis.centros_form_labor.gestionar`

## Endpoints Relacionados

- `/src/ubis/centros_form_labor`
- `/src/ubis/centros_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
