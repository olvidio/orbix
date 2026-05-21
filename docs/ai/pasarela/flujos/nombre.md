---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Nombre"
flujo: "pasarela.nombre.gestionar.flujo"
preguntas: ["Como consultar el listado en Nombre?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.nombre_ajax"]
endpoints: ["/src/pasarela/nombre_lista"]
source: "docs/catalogo/pasarela/flujos/nombre.md"
estado_revision: "generado"
---

# Ayuda IA - Nombre

Usa este documento para responder preguntas de usuario sobre como trabajar con `Nombre`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Nombre?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/nombre_lista`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.nombre_ajax`

## Objetivo

Gestiona NombreLista. Devuelve el listado del parámetro nombre listo para serializar. Estructura: {excepciones: [{id_tipo_activ, etiqueta, valor}]}. (El parámetro nombre no tiene valor por defecto.).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
