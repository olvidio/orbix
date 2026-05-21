---
id: "encargossacd.listas_c.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Listas C"
entidades: ["ListasC"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_c_data"]
pantallas: ["frontend/encargossacd/controller/listas_c.php"]
casos_uso: ["src\\encargossacd\\application\\ListasCData"]
tags: ["c", "data", "encargossacd", "listas", "listas_c"]
estado_revision: "generado"
---

# Gestionar Listas C

Propuesta generada automaticamente a partir de endpoints con prefijo comun `listas_c`.

## Objetivo Funcional

Gestiona ListasC. Genera el listado de atencion SACD "c" (cr 9/05, Anexo2, 9.4 c). Sustituye la logica de frontend/encargossacd/controller/listas_c.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/listas_c_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/listas_c.php`

## Casos De Uso Detectados

- `src\encargossacd\application\ListasCData`

## Pistas Desde Endpoints

- Genera el listado de atencion SACD "c" (cr 9/05, Anexo2, 9.4 c). Sustituye la logica de `frontend/encargossacd/controller/listas_c.php`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
