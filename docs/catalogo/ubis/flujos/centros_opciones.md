---
id: "ubis.centros_opciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Centros Opciones"
capacidad: "ubis.centros_opciones.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/centros_opciones_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Centros Opciones

Propuesta generada automaticamente desde la capacidad `ubis.centros_opciones.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CentrosOpciones. Devuelve el payload (solo datos) para poblar el <select> de centros en frontend\shared\web\CentrosQue. Sustituye el acceso directo desde CentrosQue al repositorio CentroDlRepositoryInterface (separación frontend ↔ backend, ver refactor.md).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

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

- `/src/ubis/centros_opciones_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
