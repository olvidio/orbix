---
id: "encargossacd.encargo_select.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Encargo Select"
entidades: ["EncargoSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/encargo_select_data"]
pantallas: ["frontend/encargossacd/controller/encargo_select.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoSelectData"]
tags: ["data", "encargo", "encargo_select", "encargossacd", "select"]
estado_revision: "generado"
---

# Gestionar Encargo Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `encargo_select`.

## Objetivo Funcional

Gestiona EncargoSelect. Datos para la lista de encargos (encargo_select). El frontend construye la frontend\shared\web\Lista y los enlaces; aqui devolvemos unicamente los datos planos de cada fila.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/encargo_select_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/encargo_select.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoSelectData`

## Pistas Desde Endpoints

- Datos para la lista de encargos (`encargo_select`). El frontend construye la `frontend\shared\web\Lista` y los enlaces; aqui devolvemos unicamente los datos planos de cada fila.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
