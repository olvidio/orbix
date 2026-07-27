---
id: "encargossacd.pantalla.propuestas_lista_enc"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Propuestas Lista Enc"
controller: "frontend/encargossacd/controller/propuestas_lista_enc.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/encargossacd/propuestas_lista_enc_data"]
capacidades: []
campos: ["post.filtro_ctr"]
acciones: []
estado_revision: "revisado"
---

# Propuestas Lista Enc

Listado HTML de propuestas por encargos/centros. Sin plantilla: imprime `html` o el mensaje
`error` del endpoint.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/propuestas_lista_enc.php`

## Vistas Relacionadas

Sin vista propia.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/propuestas_lista_enc_data`

## Capacidades Relacionadas

Ninguna ficha de capacidad aún.

## Campos Detectados

- `post.filtro_ctr` (el menú pasa también `sel=nagd`, ignorado)

## Acciones Detectadas

Ninguna.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (acceso desde Encargos > propuestas → «listado propuestas por encargos»)
- **Pills2:** sin entrada de menú en el índice
