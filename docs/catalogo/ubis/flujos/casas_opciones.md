---
id: "ubis.casas_opciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Casas Opciones"
capacidad: "ubis.casas_opciones.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/casas_opciones_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Casas Opciones

Propuesta generada automaticamente desde la capacidad `ubis.casas_opciones.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CasasOpciones. Devuelve el payload (solo datos) para poblar el <select> de casas en frontend\shared\web\CasasQue. La vista/componente frontend es quien construye el HTML del desplegable; aquí solo se exponen las opciones. Sustituye el acceso directo desde CasasQue al repositorio CasaDlRepositoryInterface (separación frontend ↔ backend, ver refactor.md).

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

- `/src/ubis/casas_opciones_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
