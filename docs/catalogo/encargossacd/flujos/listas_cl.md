---
id: "encargossacd.listas_cl.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Listas Cl"
capacidad: "encargossacd.listas_cl.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_cl"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_cl_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Listas Cl

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_cl.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListasCl. Listado de cl para cr, restringido a los centros de la sss+. Sustituye la logica de frontend/encargossacd/controller/listas_cl.php (era una plantilla con SQL crudo). Devuelve el HTML completo listo para volcarlo al cliente; el frontend se limita a pasar sf y a echo del resultado.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_cl`

## Escenarios Inferidos

### Obtener Datos

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

- `/src/encargossacd/listas_cl_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
