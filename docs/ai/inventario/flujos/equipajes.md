---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Equipajes"
flujo: "inventario.equipajes.gestionar.flujo"
preguntas: ["Como eliminar en Equipajes?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/inventario/equipajes_eliminar"]
source: "docs/catalogo/inventario/flujos/equipajes.md"
estado_revision: "generado"
---

# Ayuda IA - Equipajes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Equipajes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Equipajes?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_eliminar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Ciclo de vida de equipajes: alta (`equipajes_nuevo`), composición de maletas (EGM/Whereis), impresión y eliminación.

## Errores Documentados

- `falta id_equipaje`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
