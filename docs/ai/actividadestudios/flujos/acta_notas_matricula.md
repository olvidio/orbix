---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Guardar las notas en borrador"
flujo: "actividadestudios.acta_notas_matricula.gestionar.flujo"
preguntas: ["Como guardar las notas en borrador en Guardar las notas en borrador?"]
pantallas_principales: ["actividadestudios.pantalla.acta_notas"]
fragmentos: ["actividadestudios.pantalla.acta_notas"]
endpoints: ["/src/actividadestudios/acta_notas_matricula_guardar"]
source: "docs/catalogo/actividadestudios/flujos/acta_notas_matricula.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar las notas en borrador

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar las notas en borrador`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar las notas en borrador en Guardar las notas en borrador?

## Donde Entrar

- Acta Notas (`actividadestudios.pantalla.acta_notas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar las notas en borrador

1. En el acta de notas, cambiar la nota, la nota máxima, el preceptor o la situación del acta de un alumno.
2. Al salir de la casilla de la nota (o al guardar) se graba el cambio en su matrícula.
3. Si algún dato no es válido, sale un aviso con el motivo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/acta_notas_matricula_guardar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.acta_notas`
- `actividadestudios.pantalla.acta_notas`

## Objetivo

Editar las notas, la nota máxima, el preceptor o la situación de acta de los alumnos matriculados, y guardarlo como borrador en sus matrículas.

## Errores Documentados

- `Hay una nota mayor que el máximo`
- `hay un error, no se ha guardado`
- `no se puede definir cursada con preceptor`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
