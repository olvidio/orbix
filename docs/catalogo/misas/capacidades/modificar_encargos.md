---
id: "misas.modificar_encargos.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Modificar Encargos"
entidades: ["ModificarEncargos"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_encargos_data"]
pantallas: ["frontend/misas/controller/modificar_encargos.php"]
casos_uso: ["src\\misas\\application\\ModificarEncargosData"]
tags: ["data", "encargos", "misas", "modificar", "modificar_encargos"]
estado_revision: "generado"
---

# Gestionar Modificar Encargos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `modificar_encargos`.

## Objetivo Funcional

Gestiona ModificarEncargos. Devuelve los datos para pintar la pantalla modificar_encargos: el desplegable de zonas (filtrado segun el rol del usuario) y la lista de criterios de orden aceptados por el grid. Replica la logica de apps/misas/controller/modificar_encargos.php: si el rol es p-sacd y NO es jefe de calendario, se limitan las zonas a las del id_pau del propio usuario. Devuelve: - error : texto vacio si todo ok, mensaje si el usuario no tiene permiso para ver la pantalla. - a_opciones_zona: array id_zona => nombre_zona. - a_orden : array criterio => label.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/modificar_encargos_data`

## Pantallas Relacionadas

- `frontend/misas/controller/modificar_encargos.php`

## Casos De Uso Detectados

- `src\misas\application\ModificarEncargosData`

## Pistas Desde Endpoints

- Devuelve los datos para pintar la pantalla `modificar_encargos`: el desplegable de zonas (filtrado segun el rol del usuario) y la lista de criterios de orden aceptados por el grid. Replica la logica de `apps/misas/controller/modificar_encargos.php`: si el rol es `p-sacd` y NO es jefe de calendario, se limitan las zonas a las del `id_pau` del propio usuario. Devuelve: - `error` : texto vacio si todo ok, mensaje si el usuario no tiene permiso para ver la pantalla. - `a_opciones_zona`: array id_zona => nombre_zona. - `a_orden` : array criterio => label.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
