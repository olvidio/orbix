---
id: "misas.update_iniciales.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Update Iniciales"
entidades: ["UpdateIniciales"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/update_iniciales"]
pantallas: ["frontend/misas/controller/ver_iniciales_zona.php"]
casos_uso: ["src\\misas\\application\\UpdateIniciales"]
tags: ["iniciales", "misas", "update", "update_iniciales"]
estado_revision: "generado"
---

# Gestionar Update Iniciales

Propuesta generada automaticamente a partir de endpoints con prefijo comun `update_iniciales`.

## Objetivo Funcional

Gestiona UpdateIniciales. Inserta o actualiza la fila de iniciales/color para un sacerdote. Devuelve texto vacio si todo fue bien; en otro caso, el mensaje de error del repositorio. El controlador HTTP es quien serializa la respuesta con ContestarJson::enviar(...).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/misas/update_iniciales`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_iniciales_zona.php`

## Casos De Uso Detectados

- `src\misas\application\UpdateIniciales`

## Pistas Desde Endpoints

- Inserta o actualiza la fila de iniciales/color para un sacerdote. Devuelve texto vacio si todo fue bien; en otro caso, el mensaje de error del repositorio. El controlador HTTP es quien serializa la respuesta con `ContestarJson::enviar(...)`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
