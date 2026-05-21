---
id: "notas.acta_pdf_subir.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Acta Pdf Subir"
entidades: ["ActaPdfSubir"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/acta_pdf_subir"]
pantallas: []
casos_uso: ["src\\notas\\application\\ActaPdfSubir"]
tags: ["acta", "acta_pdf_subir", "notas", "pdf", "subir"]
estado_revision: "generado"
---

# Gestionar Acta Pdf Subir

Propuesta generada automaticamente a partir de endpoints con prefijo comun `acta_pdf_subir`.

## Objetivo Funcional

Gestiona ActaPdfSubir. Sube (persiste) el contenido binario de un PDF firmado en el campo pdf del acta identificada por acta_num. El contenido se lee del array $files que tiene la misma forma que $_FILES (clave acta_pdf generada por bootstrap-fileinput en acta_ver.phtml).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/notas/acta_pdf_subir`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\notas\application\ActaPdfSubir`

## Pistas Desde Endpoints

- Sube (persiste) el contenido binario de un PDF firmado en el campo `pdf` del acta identificada por `acta_num`. El contenido se lee del array `$files` que tiene la misma forma que `$_FILES` (clave `acta_pdf` generada por bootstrap-fileinput en `acta_ver.phtml`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
