---
id: "cartaspresentacion.cartas_presentacion_shell.gestionar"
tipo: "capacidad"
modulo: "cartaspresentacion"
nombre: "Gestionar Cartas Presentacion Shell"
entidades: ["CartasPresentacionShell"]
acciones: ["obtener_datos"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_shell_data"]
pantallas: ["frontend/cartaspresentacion/controller/cartas_presentacion.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionShellData"]
tags: ["cartas", "cartas_presentacion_shell", "cartaspresentacion", "data", "presentacion", "shell"]
estado_revision: "generado"
---

# Gestionar Cartas Presentacion Shell

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cartas_presentacion_shell`.

## Objetivo Funcional

Gestiona CartasPresentacionShell. Datos para la shell cartas_presentacion.php: delegación y paths relativos. URLs absolutas y fragment Hash: {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/cartaspresentacion/cartas_presentacion_shell_data`

## Pantallas Relacionadas

- `frontend/cartaspresentacion/controller/cartas_presentacion.php`

## Casos De Uso Detectados

- `src\cartaspresentacion\application\CartasPresentacionShellData`

## Pistas Desde Endpoints

- Datos para la shell `cartas_presentacion.php`: delegación y paths relativos. URLs absolutas y fragment Hash: {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
