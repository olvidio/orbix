---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Modificar Plantilla"
pantalla: "misas.pantalla.modificar_plantilla"
preguntas: ["Que se puede hacer en Modificar Plantilla?", "Que campos tiene Modificar Plantilla?", "Que acciones hay en Modificar Plantilla?"]
capacidades: ["misas.modificar_plantilla.gestionar"]
endpoints: ["/src/misas/modificar_plantilla_data"]
source: "docs/catalogo/misas/pantallas/modificar_plantilla.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Modificar Plantilla

## Resumen

Editor de plantilla semanal (-1): centros/tareas/horarios por zona. Grid plantilla + `anadir_ctr_tarea`, `quitar_horario`, `importar_plantilla`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_zona`
- `form.importar_de_plantilla`
- `form.orden`
- `form.tipo_plantilla`
- `form.tipo_plantilla_destino`
- `form.tipo_plantilla_origen`
- `html.importar`

## Acciones Detectadas

- `button:importar`
- `fnjs_importar_de_plantilla_zona`
- `fnjs_ver_plantilla_zona`

## Capacidades Relacionadas

- `misas.modificar_plantilla.gestionar`

## Endpoints Relacionados

- `/src/misas/modificar_plantilla_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
