---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Activacion"
flujo: "pasarela.activacion.gestionar.flujo"
preguntas: ["Como consultar el listado en Activacion?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.activacion_ajax"]
endpoints: ["/src/pasarela/activacion_lista"]
source: "docs/catalogo/pasarela/flujos/activacion.md"
estado_revision: "generado"
---

# Ayuda IA - Activacion

Usa este documento para responder preguntas de usuario sobre como trabajar con `Activacion`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Activacion?

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
- `/src/pasarela/activacion_lista`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.activacion_ajax`

## Objetivo

Gestiona ActivacionLista. Devuelve el listado del parámetro fecha_activacion listo para serializar: - default: valor por defecto. - excepciones: array de filas {id_tipo_activ, etiqueta, valor}. El frontend renderiza la tabla a partir de estos datos; este caso de uso no genera HTML.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
