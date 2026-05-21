---
id: "notas.asig_faltan_personas_select.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Asig Faltan Personas Select"
entidades: ["AsigFaltanPersonasSelectTabla"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asig_faltan_personas_select_data"]
pantallas: ["frontend/notas/controller/asig_faltan_personas_select.php"]
casos_uso: ["src\\notas\\application\\AsigFaltanPersonasSelectTablaData"]
tags: ["asig", "asig_faltan_personas_select", "data", "faltan", "notas", "personas", "select"]
estado_revision: "generado"
---

# Gestionar Asig Faltan Personas Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asig_faltan_personas_select`.

## Objetivo Funcional

Gestiona AsigFaltanPersonasSelectTabla. Tabla de asig_faltan_personas_select.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/asig_faltan_personas_select_data`

## Pantallas Relacionadas

- `frontend/notas/controller/asig_faltan_personas_select.php`

## Casos De Uso Detectados

- `src\notas\application\AsigFaltanPersonasSelectTablaData`

## Pistas Desde Endpoints

- Tabla de `asig_faltan_personas_select`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
