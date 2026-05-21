---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Asignaturas Pendientes Resumen"
flujo: "notas.asignaturas_pendientes_resumen.gestionar.flujo"
preguntas: ["Como obtener datos en Asignaturas Pendientes Resumen?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.asignaturas_pendientes_resumen"]
endpoints: ["/src/notas/asignaturas_pendientes_resumen_data"]
source: "docs/catalogo/notas/flujos/asignaturas_pendientes_resumen.md"
estado_revision: "generado"
---

# Ayuda IA - Asignaturas Pendientes Resumen

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignaturas Pendientes Resumen`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Asignaturas Pendientes Resumen?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.asignaturas_pendientes_resumen`

## Objetivo

Gestiona AsignaturasPendientesResumen. Resumen: número de alumnos con cada asignatura pendiente, desglosado por tramo (nb, nc1, nc2, n total, ab, ac1, ac2, a total). Sucesor de la lógica embebida en frontend/notas/controller/asignaturas_pendientes_resumen.php.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
