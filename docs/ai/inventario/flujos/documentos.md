---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Modificar varios documentos a la vez"
flujo: "inventario.documentos.gestionar.flujo"
preguntas: ["Como modificar varios documentos a la vez en Modificar varios documentos a la vez?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/inventario/documentos_guardar"]
source: "docs/catalogo/inventario/flujos/documentos.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar varios documentos a la vez

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar varios documentos a la vez`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar varios documentos a la vez en Modificar varios documentos a la vez?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar varios documentos a la vez

1. Abrir **Inventario > documentos > por sigla**.
2. Elegir el **tipo de documento** y pulsar **Buscar**.
3. En el listado, marcar los documentos que se quieren cambiar y pulsar **Modificar por bloques**. Si falta el tipo o no hay documentos marcados avisa «Debe seleccionar un tipo de documento» o «Debe seleccionar algún documento».
4. En «campos a modificar en los documentos seleccionados», **marcar la casilla «modificar» de cada campo que se quiere cambiar** y escribir su valor: **fecha recibido**, **fecha asignado**, **eliminado** (sí/no), **fecha eliminado**, **número inicial**, **número final**. Los campos sin marcar no se tocan; un campo marcado y vacío borra el valor.
5. Pulsar **guardar** (o **cancel** para salir sin cambios). Si no se marca ningún campo avisa «Debe marcar al menos un campo a modificar». Al guardar bien aparece «guardado» y el listado se actualiza.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/documentos_guardar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Cambiar de golpe fechas, numeración o estado de varios documentos a la vez.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
