---
id: "notas.tessera_imprimir.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Tessera Imprimir"
entidades: ["TesseraImprimir"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/tessera_imprimir_data"]
pantallas: ["frontend/notas/controller/tessera_imprimir.php", "frontend/notas/controller/tessera_imprimir_mpdf.php"]
casos_uso: ["src\\notas\\application\\TesseraImprimirData"]
tags: ["data", "imprimir", "notas", "tessera", "tessera_imprimir"]
estado_revision: "generado"
---

# Gestionar Tessera Imprimir

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tessera_imprimir`.

## Objetivo Funcional

Gestiona TesseraImprimir. Datos imprimibles de tessera ya serializados (sin objetos dominio → JSON estable).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/tessera_imprimir_data`

## Pantallas Relacionadas

- `frontend/notas/controller/tessera_imprimir.php`
- `frontend/notas/controller/tessera_imprimir_mpdf.php`

## Casos De Uso Detectados

- `src\notas\application\TesseraImprimirData`

## Pistas Desde Endpoints

- Datos imprimibles de tessera ya serializados (sin objetos dominio → JSON estable).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
