---
id: "notas.acta_ver.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Acta Ver"
entidades: ["ActaVer"]
acciones: ["ver_formulario"]
endpoints: ["/src/notas/acta_ver_form_data"]
pantallas: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ActaVerFormData"]
tags: ["acta", "acta_ver", "data", "form", "notas", "ver"]
estado_revision: "generado"
---

# Gestionar Acta Ver

Propuesta generada automaticamente a partir de endpoints con prefijo comun `acta_ver`.

## Objetivo Funcional

Gestiona ActaVer. Estado del formulario acta_ver (sin HashFront ni vistas).

## Acciones Detectadas

- `ver_formulario`

## Endpoints

- `/src/notas/acta_ver_form_data`

## Pantallas Relacionadas

- `frontend/notas/controller/acta_ver.php`

## Casos De Uso Detectados

- `src\notas\application\ActaVerFormData`

## Pistas Desde Endpoints

- Estado del formulario `acta_ver` (sin HashFront ni vistas).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
