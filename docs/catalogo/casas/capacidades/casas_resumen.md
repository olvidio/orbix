---
id: "casas.casas_resumen.gestionar"
tipo: "capacidad"
modulo: "casas"
nombre: "Gestionar Casas Resumen"
entidades: ["CasasResumen"]
acciones: ["obtener_datos"]
endpoints: ["/src/casas/casas_resumen_data"]
pantallas: ["frontend/casas/controller/casas_resumen_lista.php"]
casos_uso: ["src\\casas\\application\\CasasResumenData"]
tags: ["casas", "casas_resumen", "data", "resumen"]
estado_revision: "generado"
---

# Gestionar Casas Resumen

Propuesta generada automaticamente a partir de endpoints con prefijo comun `casas_resumen`.

## Objetivo Funcional

Gestiona CasasResumen. Use case: resumen económico de casas (dias ocupados, asistentes previstos/reales, ingresos, gastos, aportaciones, superávit). Sucesor de apps/casas/controller/casas_resumen_ajax.php. Dos modos: - que='' → un único periodo (año/trimestre/rango) por casa. - que!='' → estadística por año (5 años) por casa.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/casas/casas_resumen_data`

## Pantallas Relacionadas

- `frontend/casas/controller/casas_resumen_lista.php`

## Casos De Uso Detectados

- `src\casas\application\CasasResumenData`

## Pistas Desde Endpoints

- Use case: resumen económico de casas (dias ocupados, asistentes previstos/reales, ingresos, gastos, aportaciones, superávit). Sucesor de `apps/casas/controller/casas_resumen_ajax.php`. Dos modos: - `que=''` → un único periodo (año/trimestre/rango) por casa. - `que!=''` → estadística por año (5 años) por casa.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
