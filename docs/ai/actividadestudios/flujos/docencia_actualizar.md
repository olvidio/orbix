---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Docencia Actualizar"
flujo: "actividadestudios.docencia_actualizar.gestionar.flujo"
preguntas: ["Como ejecutar en Docencia Actualizar?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.actualizar_docencia"]
endpoints: ["/src/actividadestudios/docencia_actualizar"]
source: "docs/catalogo/actividadestudios/flujos/docencia_actualizar.md"
estado_revision: "generado"
---

# Ayuda IA - Docencia Actualizar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Docencia Actualizar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Docencia Actualizar?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Abrir **actualizar docencia** desde el menú.
2. Elegir año y periodo (o fechas personalizadas) y pulsar **buscar**.
3. El sistema calcula la docencia de actividades terminadas en el rango y la persiste.
4. Se muestra el mensaje de resultado en la misma pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/docencia_actualizar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.actualizar_docencia`

## Objetivo

El usuario elige un periodo de actividades terminadas y ejecuta la actualización: el sistema recorre las asignaturas con profesor asignado y graba/actualiza registros en `d_docencia_stgr` (`ProfesorDocenciaStgr`). Sustituye la rama «continuar» del legacy `apps/actividadestudios/controller/actualizar_docencia.php`.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
