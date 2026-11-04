---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "los grupos de casas"
flujo: "casas.grupo.gestionar.flujo"
preguntas: ["Como gestionar los grupos de casas en los grupos de casas?"]
pantallas_principales: ["casas.pantalla.grupo"]
fragmentos: ["casas.pantalla.grupo_form", "casas.pantalla.grupo_lista"]
endpoints: ["/src/casas/grupo_eliminar", "/src/casas/grupo_form_data", "/src/casas/grupo_lista_data", "/src/casas/grupo_update"]
source: "docs/catalogo/casas/flujos/grupo.md"
estado_revision: "generado"
---

# Ayuda IA - los grupos de casas

Usa este documento para responder preguntas de usuario sobre como trabajar con `los grupos de casas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como gestionar los grupos de casas en los grupos de casas?

## Donde Entrar

- Grupo (`casas.pantalla.grupo`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Gestionar los grupos de casas

1. Entrar en **grupos**: aparece la lista de **grupos de casas**.
2. Para crear uno, pulsar nuevo; elegir la **Casa Madre** y la **Casa dependiente** (deben ser dos casas distintas) y pulsar **guardar**.
3. Para cambiarlo, abrir la relación, modificar y **guardar**; para quitarlo, eliminarla.
4. Aviso: en la estadística económica las casas de un mismo grupo se recalculan conjuntamente.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_form_data`
- `/src/casas/grupo_lista_data`
- `/src/casas/grupo_update`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.grupo`
- `casas.pantalla.grupo_form`
- `casas.pantalla.grupo_lista`

## Objetivo

Agrupar casas: indicar qué casa es la madre y cuáles dependen de ella.

## Errores Documentados

- `debe indicar las dos casas`
- `No puede ser la misma casa`
- `no se encuentra el grupo`
- `debe indicar el grupo a eliminar`
- `Hay un error, no se ha guardado.`
- `Hay un error, no se ha eliminado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
