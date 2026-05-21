---
id: "misas.ver_encargos_centros.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Ver Encargos Centros"
entidades: ["VerEncargosCentros"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_encargos_centros_data"]
pantallas: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\VerEncargosCentrosData"]
tags: ["centros", "data", "encargos", "misas", "ver", "ver_encargos_centros"]
estado_revision: "generado"
---

# Gestionar Ver Encargos Centros

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_encargos_centros`.

## Objetivo Funcional

Gestiona VerEncargosCentros. Devuelve los datos del SlickGrid de EncargoCtr (encargos visibles para cada centro de una zona) + los desplegables estaticos del modal de edicion (zonas posibles para filtrar encargos, centros de la zona). El desplegable dinamico de encargos (que cambia al seleccionar zona en el modal) no se incluye aqui: el frontend lo pide por separado a DesplegableEncargosData cuando el usuario lo necesita.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/ver_encargos_centros_data`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_encargos_centros.php`

## Casos De Uso Detectados

- `src\misas\application\VerEncargosCentrosData`

## Pistas Desde Endpoints

- Devuelve los datos del SlickGrid de `EncargoCtr` (encargos visibles para cada centro de una zona) + los desplegables estaticos del modal de edicion (zonas posibles para filtrar encargos, centros de la zona). El desplegable dinamico de encargos (que cambia al seleccionar zona en el modal) no se incluye aqui: el frontend lo pide por separado a `DesplegableEncargosData` cuando el usuario lo necesita.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
