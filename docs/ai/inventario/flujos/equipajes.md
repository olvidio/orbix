---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Ciclo completo de un equipaje"
flujo: "inventario.equipajes.gestionar.flujo"
preguntas: ["Como ciclo completo de un equipaje en Ciclo completo de un equipaje?"]
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
endpoints: ["/src/inventario/equipajes_eliminar"]
source: "docs/catalogo/inventario/flujos/equipajes.md"
estado_revision: "generado"
---

# Ayuda IA - Ciclo completo de un equipaje

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ciclo completo de un equipaje`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ciclo completo de un equipaje en Ciclo completo de un equipaje?

## Donde Entrar

- Gestionar equipajes (`inventario.pantalla.equipajes_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ciclo completo de un equipaje

1. Crear con **nuevo equipaje**.
2. Componer las valijas en **hacer equipajes**.
3. Imprimir con **imprimir equipajes**.
4. Al terminar, eliminar el equipaje desde **hacer equipajes** (se eliminan también sus valijas).

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_eliminar`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_ver`

## Objetivo

Ciclo de vida de los equipajes: crear, componer las valijas, imprimir y eliminar.

## Errores Documentados

- `falta id_equipaje`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
