---
id: "cambios.pantalla.usuario_avisos_pref_fases"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Desplegable de fases"
controller: "frontend/cambios/controller/usuario_avisos_pref_fases.php"
vistas: ["frontend/cambios/view/usuario_avisos_pref_fases.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_fases_data"]
capacidades: ["cambios.cambio_usuario_objeto_pref_fases.gestionar"]
campos: ["post.dl_propia", "post.id_tipo_activ", "post.objeto"]
acciones: []
estado_revision: "revisado"
---

# Desplegable de fases

Fragmento AJAX que devuelve HTML del desplegable de fase/estado de referencia al cambiar objeto o tipo
de actividad en `usuario_avisos_pref`.

## Endpoints Usados

- `/src/cambios/cambio_usuario_objeto_pref_fases_data`

## Ruta de menú

sin entrada de menú en el índice
