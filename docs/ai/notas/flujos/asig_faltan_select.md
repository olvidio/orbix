---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Buscar alumnos por número de asignaturas pendientes"
flujo: "notas.asig_faltan_select.gestionar.flujo"
preguntas: ["Como buscar alumnos por número de asignaturas pendientes en Buscar alumnos por número de asignaturas pendientes?"]
pantallas_principales: ["notas.pantalla.asig_faltan_select"]
fragmentos: ["notas.pantalla.asig_faltan_select"]
endpoints: ["/src/notas/asig_faltan_select_data"]
source: "docs/catalogo/notas/flujos/asig_faltan_select.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar alumnos por número de asignaturas pendientes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar alumnos por número de asignaturas pendientes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar alumnos por número de asignaturas pendientes en Buscar alumnos por número de asignaturas pendientes?

## Donde Entrar

- Asig Faltan Select (`notas.pantalla.asig_faltan_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar alumnos por número de asignaturas pendientes

1. Entrar en **buscar asig. pendientes**.
2. En **Búsqueda de personas por número de asignaturas** marcar **numerarios** y/o **agregados** y elegir el ciclo con el botón **bienio** o **cuadrienio** (en cuadrienio, además, **año I** y/o **año II-IV**).
3. Escribir el **número de asignaturas que faltan** (no se incluyen las cursadas sin examinar). Marcar **incluir lista de asignaturas** si se quiere ver cuáles son.
4. Pulsar **Buscar** (o **Borrar** para limpiar). El resultado se titula, por ejemplo, «lista de numerarios a los que faltan 2 o menos asignaturas para finalizar el cuadrienio»; si no hay nadie, «no hay ninguna fila».

Referencias tecnicas para verificar la respuesta:
- `/src/notas/asig_faltan_select_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.asig_faltan_select`
- `notas.pantalla.asig_faltan_select`

## Objetivo

Ver los alumnos a los que les faltan como mucho N asignaturas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
