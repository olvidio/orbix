---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Consultar y purgar cambios"
flujo: "cambios.avisos_generar.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["cambios.pantalla.avisos_generar"]
endpoints: ["/src/cambios/avisos_generar_lista_data", "/src/cambios/cambio_usuario_eliminar", "/src/cambios/cambio_usuario_eliminar_hasta_fecha"]
source: "docs/catalogo/cambios/flujos/avisos_generar.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar y purgar cambios

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar y purgar cambios`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.avisos_generar`

## Objetivo

Ver los cambios registrados pendientes de avisar y eliminar los que ya no interesan (por fila o por fecha límite).

## Errores Documentados

- `debe indicar la fecha`
- `Hay un error, no se ha eliminado`
- `Hay un error al eliminar los cambios hasta la fecha indicada`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
