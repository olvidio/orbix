---
id: "ubiscamas.cama.gestionar"
tipo: "capacidad"
modulo: "ubiscamas"
nombre: "Gestionar Cama"
entidades: ["Cama"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/ubiscamas/cama_delete", "/src/ubiscamas/cama_form_data", "/src/ubiscamas/cama_update"]
pantallas: ["frontend/ubiscamas/controller/cama_form.php", "frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php"]
casos_uso: ["src\\ubiscamas\\application\\CamaFormData"]
tags: ["cama", "data", "delete", "form", "ubiscamas", "update"]
estado_revision: "generado"
---

# Gestionar Cama

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cama`.

## Objetivo Funcional

Gestiona Cama. Datos para frontend/ubiscamas/controller/cama_form.php. La composición de HashFront ocurre en {. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `crear_actualizar`
- `eliminar`
- `ver_formulario`

## Endpoints

- `/src/ubiscamas/cama_delete`
- `/src/ubiscamas/cama_form_data`
- `/src/ubiscamas/cama_update`

## Pantallas Relacionadas

- `frontend/ubiscamas/controller/cama_form.php`
- `frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php`

## Casos De Uso Detectados

- `src\ubiscamas\application\CamaFormData`

## Pistas Desde Endpoints

- Datos para `frontend/ubiscamas/controller/cama_form.php`. La composición de `HashFront` ocurre en {
- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
