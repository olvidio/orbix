---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Consultar estadística económica por años"
flujo: "casas.casa_ec.consultar.flujo"
preguntas: ["Como consultar la estadística de una casa en Consultar estadística económica por años?", "Como consultar varias casas en Consultar estadística económica por años?"]
pantallas_principales: ["casas.pantalla.casa_ec"]
fragmentos: ["casas.pantalla.casas_resumen_lista"]
endpoints: []
source: "docs/catalogo/casas/flujos/casa_ec.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar estadística económica por años

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar estadística económica por años`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar la estadística de una casa en Consultar estadística económica por años?
- Como consultar varias casas en Consultar estadística económica por años?

## Donde Entrar

- Casa Ec (`casas.pantalla.casa_ec`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar la estadística de una casa

1. Abrir **estadística por años** desde el menú.
2. Elegir la casa en el desplegable del recuadro **RESUMEN ECONÓMICO**.
3. Pulsar **buscar**.
4. Leer la tabla que aparece debajo: una fila por año, con días ocupados, nº días x nº asistentes (previsto y real), ingresos previstos y reales acumulados, gastos reales, aportación real dl y superávit. Las columnas **%** indican el porcentaje respecto al total.
5. Solo se cuentan las actividades que no están borradas: los estados son proyecto, actual, terminada y borrable (1 a 4), y se excluye «borrable».
6. Si una casa pertenece a un grupo de casas, el superávit se calcula para el conjunto del grupo, no para cada casa por separado.
7. Pulsar **mostrar detalles** en la fila de un año para ver las actividades de esa casa (ocupación sv/sf e ingresos sv/sf). **ocultar detalles** lo pliega.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Consultar varias casas

1. Abrir **estadística por años** y elegir una casa.
2. Al elegir una casa aparece debajo otro desplegable para elegir otra; repite hasta tener todas las casas que interesen.
3. Pulsar **buscar**: la tabla muestra cada casa con sus años y los totales por año.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casa_ec`
- `casas.pantalla.casas_resumen_lista`

## Objetivo

Ver, para las casas elegidas, cómo han evolucionado la ocupación, los ingresos, los gastos y el superávit en los últimos años, y desplegar el detalle por actividad.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
