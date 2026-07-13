---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Posibles Propietarios"
flujo: "actividadplazas.posibles_propietarios.gestionar.flujo"
preguntas: ["Como obtener datos en Posibles Propietarios?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadplazas/posibles_propietarios_data"]
source: "docs/catalogo/actividadplazas/flujos/posibles_propietarios.md"
estado_revision: "generado"
---

# Ayuda IA - Posibles Propietarios

Usa este documento para responder preguntas de usuario sobre como trabajar con `Posibles Propietarios`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Posibles Propietarios?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Abrir el formulario de asistencia (persona↔actividad) desde el módulo asistentes.
2. Al cargar o cambiar persona/actividad, el frontend solicita `posibles_propietarios_data` con
3. El sistema devuelve el payload estándar de desplegable (`id`, `opciones`, `selected`, `blanco`,

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/posibles_propietarios_data`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Al editar la asistencia de una persona en una actividad (o viceversa), elegir qué delegación es propietaria de la plaza entre las opciones válidas para esa combinación persona+actividad.

## Errores Documentados

- `faltan parametros id_nom / id_activ`
- `No se encuentra persona con id_nom <id>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
