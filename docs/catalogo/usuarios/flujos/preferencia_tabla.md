---
id: "usuarios.preferencia_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Preferencia Tabla"
capacidad: "usuarios.preferencia_tabla.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener"]
endpoints: ["/src/usuarios/preferencia_tabla_get"]
estado_revision: "generado"
---

# Flujo - Gestionar Preferencia Tabla

Propuesta generada automaticamente desde la capacidad `usuarios.preferencia_tabla.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PreferenciaTabla. Devuelve las preferencias de usuario necesarias para renderizar una tabla (HTML simple o SlickGrid) en el front. Entrada: - id_tabla (opcional): identificador del grid. Si viene vacío, no se devolverán preferencias específicas del grid (útil cuando sólo se necesita saber si el usuario prefiere HTML o SlickGrid). Salida: array asociativo con la forma: [ 'formato_tabla' => ''|'html'|'slickgrid', // prefs 'tabla_presentacion' 'slickgrid' => null|array, // prefs 'slickGrid_<id_tabla>_<idioma>' ] Para slickgrid se busca primero la preferencia del usuario actual; si no existe, se usa la del usuario 44 (default).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Obtener

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

- `/src/usuarios/preferencia_tabla_get`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
