---
id: "notas.acta_pdf.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Acta Pdf"
entidades: ["ActaPdf"]
acciones: ["eliminar"]
endpoints: ["/src/notas/acta_pdf_eliminar"]
pantallas: []
casos_uso: ["src\\notas\\application\\ActaPdfEliminar"]
tags: ["acta", "acta_pdf", "eliminar", "notas", "pdf"]
estado_revision: "generado"
---

# Gestionar Acta Pdf

Propuesta generada automaticamente a partir de endpoints con prefijo comun `acta_pdf`.

## Objetivo Funcional

Gestiona ActaPdf. Elimina el PDF firmado asociado a un Acta (sin borrar el acta).

## Acciones Detectadas

- `eliminar`

## Endpoints

- `/src/notas/acta_pdf_eliminar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\notas\application\ActaPdfEliminar`

## Pistas Desde Endpoints

- Elimina el PDF firmado asociado a un `Acta` (sin borrar el acta).

## Errores Conocidos

- `No se encuentra el acta`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
