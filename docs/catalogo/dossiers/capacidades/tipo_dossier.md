---
id: "dossiers.tipo_dossier.gestionar"
tipo: "capacidad"
modulo: "dossiers"
nombre: "Gestionar Tipo Dossier"
entidades: ["TipoDossier"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/dossiers/tipo_dossier_eliminar", "/src/dossiers/tipo_dossier_guardar"]
pantallas: []
casos_uso: ["src\\dossiers\\application\\TipoDossierEliminar", "src\\dossiers\\application\\TipoDossierGuardar"]
tags: ["dossier", "dossiers", "eliminar", "guardar", "tipo", "tipo_dossier"]
estado_revision: "generado"
---

# Gestionar Tipo Dossier

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_dossier`.

## Objetivo Funcional

Gestiona TipoDossier. Elimina un TipoDossier. Guarda los cambios a un TipoDossier.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/dossiers/tipo_dossier_eliminar`
- `/src/dossiers/tipo_dossier_guardar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\dossiers\application\TipoDossierEliminar`
- `src\dossiers\application\TipoDossierGuardar`

## Pistas Desde Endpoints

- Elimina un `TipoDossier`.
- Guarda los cambios a un `TipoDossier`.

## Errores Conocidos

- `Hay un error, no se ha eliminado.`
- `Hay un error, no se ha guardado.`
- `falta id_tipo_dossier`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
