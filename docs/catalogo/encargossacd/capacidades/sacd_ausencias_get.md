---
id: "encargossacd.sacd_ausencias_get.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Sacd Ausencias Get"
entidades: ["SacdAusenciasGet"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/sacd_ausencias_get_data"]
pantallas: ["frontend/encargossacd/controller/sacd_ausencias_get.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasGetData"]
tags: ["ausencias", "data", "encargossacd", "get", "sacd", "sacd_ausencias_get"]
estado_revision: "generado"
---

# Gestionar Sacd Ausencias Get

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacd_ausencias_get`.

## Objetivo Funcional

Gestiona SacdAusenciasGet. Datos para la ficha de ausencias de un SACD (frontend/encargossacd/controller/sacd_ausencias_get.php). Devuelve la lista de tipos de ausencia disponibles (encargos con prefijo 7/4) y las filas asociadas al SACD. Con historial=1 incluye todas las ausencias; sin historial solo muestra las que aun tienen vigencia.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/sacd_ausencias_get_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/sacd_ausencias_get.php`

## Casos De Uso Detectados

- `src\encargossacd\application\SacdAusenciasGetData`

## Pistas Desde Endpoints

- Datos para la ficha de ausencias de un SACD (`frontend/encargossacd/controller/sacd_ausencias_get.php`). Devuelve la lista de tipos de ausencia disponibles (encargos con prefijo 7/4) y las filas asociadas al SACD. Con `historial=1` incluye todas las ausencias; sin historial solo muestra las que aun tienen vigencia.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
