---
id: "encargossacd.listas_b.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Listas B"
entidades: ["ListasB"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_b_data"]
pantallas: ["frontend/encargossacd/controller/listas_b.php"]
casos_uso: ["src\\encargossacd\\application\\ListasBData"]
tags: ["b", "data", "encargossacd", "listas", "listas_b"]
estado_revision: "generado"
---

# Gestionar Listas B

Propuesta generada automaticamente a partir de endpoints con prefijo comun `listas_b`.

## Objetivo Funcional

Gestiona ListasB. Genera el listado de atencion SACD "b" (cr 9/05, Anexo2, 9.4 b). Sustituye la logica de frontend/encargossacd/controller/listas_b.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/listas_b_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/listas_b.php`

## Casos De Uso Detectados

- `src\encargossacd\application\ListasBData`

## Pistas Desde Endpoints

- Genera el listado de atencion SACD "b" (cr 9/05, Anexo2, 9.4 b). Sustituye la logica de `frontend/encargossacd/controller/listas_b.php`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
