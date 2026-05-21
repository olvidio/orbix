---
id: "asistentes.asistente.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Asistente"
entidades: ["Asistente"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/asistentes/asistente_eliminar", "/src/asistentes/asistente_guardar"]
pantallas: []
casos_uso: ["src\\asistentes\\application\\AsistenteEliminar", "src\\asistentes\\application\\AsistenteGuardar"]
tags: ["asistente", "asistentes", "eliminar", "guardar"]
estado_revision: "generado"
---

# Gestionar Asistente

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asistente`.

## Objetivo Funcional

Gestiona Asistente. Crea, edita o mueve un Asistente. Elimina un Asistente y sus matriculas.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/asistentes/asistente_eliminar`
- `/src/asistentes/asistente_guardar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\asistentes\application\AsistenteEliminar`
- `src\asistentes\application\AsistenteGuardar`

## Pistas Desde Endpoints

- Crea, edita o mueve un `Asistente`.
- Elimina un `Asistente` y sus matriculas.

## Errores Conocidos

- `falta id_activ_old`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `los datos de asistencia los modifica la dl del asistente`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
