---
id: "encargossacd.listas_a.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Listas A"
entidades: ["ListasA"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_a_data"]
pantallas: ["frontend/encargossacd/controller/listas_a.php"]
casos_uso: ["src\\encargossacd\\application\\ListasAData"]
tags: ["a", "data", "encargossacd", "listas", "listas_a"]
estado_revision: "generado"
---

# Gestionar Listas A

Propuesta generada automaticamente a partir de endpoints con prefijo comun `listas_a`.

## Objetivo Funcional

Gestiona ListasA. Genera el listado de atencion SACD "a" (cr 9/05, Anexo2, 9.4 a). Sustituye la logica que habia en frontend/encargossacd/controller/listas_a.php. Devuelve el HTML completo junto con los textos de cabecera, listos para inyectarlos en la vista listas.phtml.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/listas_a_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/listas_a.php`

## Casos De Uso Detectados

- `src\encargossacd\application\ListasAData`

## Pistas Desde Endpoints

- Genera el listado de atencion SACD "a" (cr 9/05, Anexo2, 9.4 a). Sustituye la logica que habia en `frontend/encargossacd/controller/listas_a.php`. Devuelve el HTML completo junto con los textos de cabecera, listos para inyectarlos en la vista `listas.phtml`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
