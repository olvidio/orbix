---
id: "encargossacd.comprobaciones_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Comprobaciones Ctr"
capacidad: "encargossacd.comprobaciones_ctr.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.comprobaciones"]
acciones: ["ejecutar"]
endpoints: ["/src/encargossacd/comprobaciones_ctr"]
estado_revision: "generado"
---

# Flujo - Gestionar Comprobaciones Ctr

Propuesta generada automaticamente desde la capacidad `encargossacd.comprobaciones_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoComprobacionesCtr. Elimina encargos ligados a centros inactivos y sacd huérfanos (misma lógica que el antiguo frontend/encargossacd/controller/comprobaciones.php).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.comprobaciones`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/comprobaciones_ctr`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
