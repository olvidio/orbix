---
id: "dossiers.perm_dossiers.gestionar"
tipo: "capacidad"
modulo: "dossiers"
nombre: "Gestionar Perm Dossiers"
entidades: ["PermDossiers"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/perm_dossiers_data"]
pantallas: ["frontend/dossiers/controller/perm_dossiers.php"]
casos_uso: ["src\\dossiers\\application\\PermDossiersListaData"]
tags: ["data", "dossiers", "perm", "perm_dossiers"]
estado_revision: "generado"
---

# Gestionar Perm Dossiers

Propuesta generada automaticamente a partir de endpoints con prefijo comun `perm_dossiers`.

## Objetivo Funcional

Gestiona PermDossiers. Listado de tipos de dossier para pantalla de permisos. pagina_link_spec se firma en perm_dossiers_data.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dossiers/perm_dossiers_data`

## Pantallas Relacionadas

- `frontend/dossiers/controller/perm_dossiers.php`

## Casos De Uso Detectados

- `src\dossiers\application\PermDossiersListaData`

## Pistas Desde Endpoints

- Listado de tipos de dossier para pantalla de permisos. `pagina_link_spec` se firma en `perm_dossiers_data.php`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
