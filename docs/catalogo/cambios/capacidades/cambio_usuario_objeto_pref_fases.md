---
id: "cambios.cambio_usuario_objeto_pref_fases.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Cambio Usuario Objeto Pref Fases"
entidades: ["CambioUsuarioObjetoPrefFases"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_fases_data"]
pantallas: ["frontend/cambios/controller/usuario_avisos_pref_fases.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefFasesData"]
tags: ["cambio", "cambio_usuario_objeto_pref_fases", "cambios", "data", "fases", "objeto", "pref", "usuario"]
estado_revision: "generado"
---

# Gestionar Cambio Usuario Objeto Pref Fases

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cambio_usuario_objeto_pref_fases`.

## Objetivo Funcional

Gestiona CambioUsuarioObjetoPrefFases. Endpoint JSON: lista de fases para el tipo de actividad indicado.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/cambios/cambio_usuario_objeto_pref_fases_data`

## Pantallas Relacionadas

- `frontend/cambios/controller/usuario_avisos_pref_fases.php`

## Casos De Uso Detectados

- `src\cambios\application\CambioUsuarioObjetoPrefFasesData`

## Pistas Desde Endpoints

- Endpoint JSON: lista de fases para el tipo de actividad indicado.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
