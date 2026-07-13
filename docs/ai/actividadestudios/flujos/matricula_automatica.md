---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Matricula Automatica"
flujo: "actividadestudios.matricula_automatica.gestionar.flujo"
preguntas: ["Como ejecutar en Matricula Automatica?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matricular"]
endpoints: ["/src/actividadestudios/matricula_automatica"]
source: "docs/catalogo/actividadestudios/flujos/matricula_automatica.md"
estado_revision: "generado"
---

# Ayuda IA - Matricula Automatica

Usa este documento para responder preguntas de usuario sobre como trabajar con `Matricula Automatica`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Matricula Automatica?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Abrir **matricular a todos** desde el menú (o desde búsqueda de persona con selección).
2. El sistema recibe `id_pau`/`sel` (persona concreta) o procesa todas las personas activas.
3. Para cada persona, borra matrículas previas si el plan no está confirmado y recalcula.
4. Se muestra el mensaje resumen en `matricular.phtml`.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matricula_automatica`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.matricular`

## Objetivo

El usuario ejecuta la matriculación automática de una o todas las personas activas: el sistema determina la actividad de estudios vigente (`ca-n`, `cv-agd`), recalcula asignaturas matriculables respetando aprobadas y topes de opcionales, y crea las matrículas. Sustituye `apps/actividadestudios/controller/matricular.php`.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
