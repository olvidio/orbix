---
id: "notas.acta_imprimir_presentacion.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Acta Imprimir Presentacion"
entidades: ["ActaImprimirPresentacion"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_imprimir_presentacion_data"]
pantallas: ["frontend/notas/controller/acta_imprimir.php", "frontend/notas/controller/acta_imprimir_mpdf.php"]
casos_uso: ["src\\notas\\application\\ActaImprimirPresentacionData"]
tags: ["acta", "acta_imprimir_presentacion", "data", "imprimir", "notas", "presentacion"]
estado_revision: "generado"
---

# Gestionar Acta Imprimir Presentacion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `acta_imprimir_presentacion`.

## Objetivo Funcional

Gestiona ActaImprimirPresentacion. Datos compartidos por acta_imprimir y el HTML de acta_imprimir_mpdf.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/acta_imprimir_presentacion_data`

## Pantallas Relacionadas

- `frontend/notas/controller/acta_imprimir.php`
- `frontend/notas/controller/acta_imprimir_mpdf.php`

## Casos De Uso Detectados

- `src\notas\application\ActaImprimirPresentacionData`

## Pistas Desde Endpoints

- Datos compartidos por `acta_imprimir` y el HTML de `acta_imprimir_mpdf`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
