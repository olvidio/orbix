---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Centros Que"
pantalla: "ubis.pantalla.centros_que"
preguntas: ["Que se puede hacer en Centros Que?", "Que campos tiene Centros Que?", "Que acciones hay en Centros Que?"]
capacidades: ["ubis.centros.gestionar"]
endpoints: ["/src/ubis/centros_update"]
source: "docs/catalogo/ubis/pantallas/centros_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Centros Que

## Resumen

Esta página sirve para asignar una dirección a un determinado ubi.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_ubi`
- `form.que`
- `html.buscar`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Capacidades Relacionadas

- `ubis.centros.gestionar`

## Endpoints Relacionados

- `/src/ubis/centros_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
