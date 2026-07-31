---
id: "encargossacd.pantalla.propuestas_lista_sacd"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "encargossacd"
nombre: "Propuestas Lista Sacd"
controller: "frontend/encargossacd/controller/propuestas_lista_sacd.php"
vistas: ["frontend/encargossacd/view/propuestas_lista_sacd.phtml"]
fragmentos_frontend: []
endpoints: ["/src/encargossacd/propuestas_lista_sacd_data"]
capacidades: []
campos: ["post.sel"]
acciones: []
estado_revision: "revisado"
---

# Propuestas Lista Sacd

Listado imprimible de propuestas por SACD (una página/tabla por persona). Consume
`array_modo` del endpoint.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/encargossacd/controller/propuestas_lista_sacd.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/propuestas_lista_sacd.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/propuestas_lista_sacd_data`

## Capacidades Relacionadas

Ninguna ficha de capacidad aún.

## Campos Detectados

- `post.sel` (`nagd` desde menú; también soporta `sssc` en API)

## Acciones Detectadas

Ninguna.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (acceso desde Encargos > propuestas → «listado propuestas por sacd»)
- **Pills2:** sin entrada de menú en el índice
