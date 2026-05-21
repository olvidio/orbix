---
id: "misas.ver_iniciales_zona.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Ver Iniciales Zona"
entidades: ["VerInicialesZona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_iniciales_zona_data"]
pantallas: ["frontend/misas/controller/ver_iniciales_zona.php"]
casos_uso: ["src\\misas\\application\\VerInicialesZonaData"]
tags: ["data", "iniciales", "misas", "ver", "ver_iniciales_zona", "zona"]
estado_revision: "generado"
---

# Gestionar Ver Iniciales Zona

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_iniciales_zona`.

## Objetivo Funcional

Gestiona VerInicialesZona. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/ver_iniciales_zona_data`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_iniciales_zona.php`

## Casos De Uso Detectados

- `src\misas\application\VerInicialesZonaData`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
