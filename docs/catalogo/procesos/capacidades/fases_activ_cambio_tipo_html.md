---
id: "procesos.fases_activ_cambio_tipo_html.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Fases Activ Cambio Tipo Html"
entidades: ["FasesActivCambioTipoActividadHtml"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/fases_activ_cambio_tipo_html"]
pantallas: ["frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioTipoActividadHtmlData"]
tags: ["activ", "cambio", "fases", "fases_activ_cambio_tipo_html", "html", "procesos", "tipo"]
estado_revision: "generado"
---

# Gestionar Fases Activ Cambio Tipo Html

Propuesta generada automaticamente a partir de endpoints con prefijo comun `fases_activ_cambio_tipo_html`.

## Objetivo Funcional

Gestiona FasesActivCambioTipoActividadHtml. Payload para {.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/fases_activ_cambio_tipo_html`

## Pantallas Relacionadas

- `frontend/procesos/controller/fases_activ_cambio.php`

## Casos De Uso Detectados

- `src\procesos\application\FasesActivCambioTipoActividadHtmlData`

## Pistas Desde Endpoints

- Payload para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
