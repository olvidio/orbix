---
id: "pasarela.exportar_que_actividad_tipo_html.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Exportar Que Actividad Tipo Html"
entidades: ["ExportarQueActividadTipoHtml"]
acciones: ["ejecutar"]
endpoints: ["/src/pasarela/exportar_que_actividad_tipo_html"]
pantallas: ["frontend/pasarela/controller/exportar_que.php"]
casos_uso: ["src\\pasarela\\application\\ExportarQueActividadTipoHtml"]
tags: ["actividad", "exportar", "exportar_que_actividad_tipo_html", "html", "pasarela", "que", "tipo"]
estado_revision: "generado"
---

# Gestionar Exportar Que Actividad Tipo Html

Propuesta generada automaticamente a partir de endpoints con prefijo comun `exportar_que_actividad_tipo_html`.

## Objetivo Funcional

Gestiona ExportarQueActividadTipoHtml. HTML del selector de tipo de actividad para la pantalla «exportar qué». Replica la configuración que antes hacía {.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/pasarela/exportar_que_actividad_tipo_html`

## Pantallas Relacionadas

- `frontend/pasarela/controller/exportar_que.php`

## Casos De Uso Detectados

- `src\pasarela\application\ExportarQueActividadTipoHtml`

## Pistas Desde Endpoints

- HTML del selector de tipo de actividad para la pantalla «exportar qué». Replica la configuración que antes hacía {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
