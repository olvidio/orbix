---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Acta Notas"
flujo: "actividadestudios.acta_notas.gestionar.flujo"
preguntas: ["Como obtener datos en Acta Notas?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.acta_notas"]
endpoints: ["/src/actividadestudios/acta_notas_data"]
source: "docs/catalogo/actividadestudios/flujos/acta_notas.md"
estado_revision: "generado"
---

# Ayuda IA - Acta Notas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Acta Notas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Acta Notas?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En el dossier de asignaturas de una actividad (3005), seleccionar una asignatura.
2. Pulsar **actas** (`fnjs_actas`).
3. El sistema carga `acta_notas` y consulta `acta_notas_data` con las claves de actividad
4. Se muestra el acta con matriculados, desplegable de situaciones y permiso de edición.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/acta_notas_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.acta_notas`

## Objetivo

El usuario abre el acta de una asignatura impartida en una actividad: el sistema muestra el formulario del acta (cabecera vía `acta_ver`) y debajo la lista de matriculados con nota, nota máxima, preceptor y situación de acta, según permisos de la DL propietaria.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
