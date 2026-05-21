---
id: "misas.modificar_encargos_centros.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Modificar Encargos Centros"
entidades: ["ModificarEncargosCentros"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_encargos_centros_data"]
pantallas: ["frontend/misas/controller/modificar_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\ModificarEncargosCentrosData"]
tags: ["centros", "data", "encargos", "misas", "modificar", "modificar_encargos_centros"]
estado_revision: "generado"
---

# Gestionar Modificar Encargos Centros

Propuesta generada automaticamente a partir de endpoints con prefijo comun `modificar_encargos_centros`.

## Objetivo Funcional

Gestiona ModificarEncargosCentros. Devuelve el desplegable de zonas que el usuario actual puede ver, para pintar la pantalla modificar_encargos_centros. Replica la logica de permisos de apps/misas/controller/modificar_encargos_centros.php: si el rol es p-sacd y NO es jefe de calendario, se limitan las zonas a las del id_pau del propio usuario. Devuelve: - error : texto vacio si todo ok, mensaje si falta permiso. - a_opciones_zona: array id_zona => nombre_zona.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/modificar_encargos_centros_data`

## Pantallas Relacionadas

- `frontend/misas/controller/modificar_encargos_centros.php`

## Casos De Uso Detectados

- `src\misas\application\ModificarEncargosCentrosData`

## Pistas Desde Endpoints

- Devuelve el desplegable de zonas que el usuario actual puede ver, para pintar la pantalla `modificar_encargos_centros`. Replica la logica de permisos de `apps/misas/controller/modificar_encargos_centros.php`: si el rol es `p-sacd` y NO es jefe de calendario, se limitan las zonas a las del `id_pau` del propio usuario. Devuelve: - `error` : texto vacio si todo ok, mensaje si falta permiso. - `a_opciones_zona`: array id_zona => nombre_zona.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
