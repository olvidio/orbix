---
id: "encargossacd.listas_b.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Listas B"
capacidad: "encargossacd.listas_b.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_b"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_b_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Listas B

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_b.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListasB. Genera el listado de atencion SACD "b" (cr 9/05, Anexo2, 9.4 b). Sustituye la logica de frontend/encargossacd/controller/listas_b.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_b`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sf`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_b_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
