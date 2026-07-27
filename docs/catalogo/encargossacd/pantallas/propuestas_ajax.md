---
id: "encargossacd.pantalla.propuestas_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Propuestas Ajax"
controller: "frontend/encargossacd/controller/propuestas_ajax.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/encargossacd/propuestas_ajax"]
capacidades: []
campos: ["post.que", "post.filtro_ctr", "post.tipo", "post.id_item", "post.id_enc", "post.id_sacd", "post.dedic_m", "post.dedic_t", "post.dedic_v"]
acciones: []
estado_revision: "revisado"
---

# Propuestas Ajax

Proxy JSON hacia `/src/encargossacd/propuestas_ajax`. Reenvía `$_POST` y responde al JS legacy
con el payload interno en la raíz (`success`, `lista`, `html`, …).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/propuestas_ajax.php`

## Vistas Relacionadas

Sin vista propia.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/propuestas_ajax`

## Capacidades Relacionadas

Ninguna ficha de capacidad aún.

## Campos Detectados

- `post.que` y campos según rama (ver ficha API)

## Acciones Detectadas

Ninguna (solo proxy).

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
