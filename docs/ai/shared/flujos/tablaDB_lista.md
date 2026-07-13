---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Listar y mantener tabla genérica"
flujo: "shared.tablaDB_lista.gestionar.flujo"
preguntas: []
pantallas_principales: ["shared.pantalla.tablaDB_lista_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
endpoints: ["/src/shared/tablaDB_buscar_datos", "/src/shared/tablaDB_lista_datos", "/src/shared/tablaDB_update"]
source: "docs/catalogo/shared/flujos/tablaDB_lista.md"
estado_revision: "generado"
---

# Ayuda IA - Listar y mantener tabla genérica

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listar y mantener tabla genérica`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Mantenimiento genérico de tablas (listado) (`shared.pantalla.tablaDB_lista_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_lista_ver`
- `shared.pantalla.tablaDB_formulario_ver`

## Objetivo

Consultar y mantener registros de tablas de configuración enlazadas desde el menú (asignaturas, ubis, inventario, procesos, etc.) mediante el shell común `tablaDB`.

## Errores Documentados

- `Errores de tablaDB_update (ver ficha API).`
- `Sin errores propios en builders de lista.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
