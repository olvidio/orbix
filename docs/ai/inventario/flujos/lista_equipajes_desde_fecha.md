---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Cambiar los equipajes que se ven"
flujo: "inventario.lista_equipajes_desde_fecha.gestionar.flujo"
preguntas: ["Como cambiar los equipajes que se ven en Cambiar los equipajes que se ven?"]
pantallas_principales: ["inventario.pantalla.equipajes_desplegable", "inventario.pantalla.equipajes_movimientos_que", "inventario.pantalla.equipajes_ver"]
fragmentos: ["inventario.pantalla.equipajes_desplegable", "inventario.pantalla.equipajes_movimientos_que", "inventario.pantalla.equipajes_ver"]
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
source: "docs/catalogo/inventario/flujos/lista_equipajes_desde_fecha.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar los equipajes que se ven

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar los equipajes que se ven`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar los equipajes que se ven en Cambiar los equipajes que se ven?

## Donde Entrar

- Desplegable equipajes (`inventario.pantalla.equipajes_desplegable`)
- Movimientos maletas — filtro (`inventario.pantalla.equipajes_movimientos_que`)
- Gestionar equipajes (`inventario.pantalla.equipajes_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar los equipajes que se ven

1. Abrir **hacer equipajes** (o imprimir / eliminar).
2. Elegir el filtro: **desde hoy**, **todo el curso** o **todos**.
3. El desplegable **escoger un equipaje** se actualiza con los equipajes de ese periodo.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_equipajes_desde_fecha`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_desplegable`
- `inventario.pantalla.equipajes_movimientos_que`
- `inventario.pantalla.equipajes_ver`
- `inventario.pantalla.equipajes_desplegable`
- `inventario.pantalla.equipajes_movimientos_que`
- `inventario.pantalla.equipajes_ver`

## Objetivo

Cargar la lista de equipajes entre los que escoger, según el filtro de fechas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
