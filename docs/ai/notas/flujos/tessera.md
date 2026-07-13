---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Tessera"
flujo: "notas.tessera.gestionar.flujo"
preguntas: ["Como copiar en Tessera?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.tessera_copiar_select"]
endpoints: ["/src/notas/tessera_copiar"]
source: "docs/catalogo/notas/flujos/tessera.md"
estado_revision: "generado"
---

# Ayuda IA - Tessera

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tessera`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como copiar en Tessera?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Copiar

1. Abrir el listado en el contexto origen/destino correspondiente.
2. Pulsar la accion de copiar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que los datos copiados aparecen en el listado.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/tessera_copiar`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.tessera_copiar_select`

## Objetivo

Flujo tessera: ver, imprimir y copiar entre personas.

## Errores Documentados

- `No se han recibido las personas de origen y destino`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
