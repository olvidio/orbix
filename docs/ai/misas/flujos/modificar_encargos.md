---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Modificar Encargos"
flujo: "misas.modificar_encargos.gestionar.flujo"
preguntas: ["Como obtener datos en Modificar Encargos?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_encargos"]
endpoints: ["/src/misas/modificar_encargos_data"]
source: "docs/catalogo/misas/flujos/modificar_encargos.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar Encargos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar Encargos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Modificar Encargos?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.modificar_encargos`

## Objetivo

Gestiona ModificarEncargos. Devuelve los datos para pintar la pantalla modificar_encargos: el desplegable de zonas (filtrado segun el rol del usuario) y la lista de criterios de orden aceptados por el grid. Replica la logica de apps/misas/controller/modificar_encargos.php: si el rol es p-sacd y NO es jefe de calendario, se limitan las zonas a las del id_pau del propio usuario. Devuelve: - error : texto vacio si todo ok, mensaje si el usuario no tiene permiso para ver la pantalla. - a_opciones_zona: array id_zona => nombre_zona. - a_orden : array criterio => label.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
