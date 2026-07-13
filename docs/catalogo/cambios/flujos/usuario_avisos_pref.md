---
id: "cambios.usuario_avisos_pref.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Configurar preferencia de aviso"
capacidad: "cambios.usuario_avisos_pref.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref", "cambios.pantalla.usuario_avisos_pref_fases", "cambios.pantalla.usuario_avisos_pref_propiedades", "cambios.pantalla.usuario_avisos_pref_condicion"]
acciones: ["cargar", "grabar"]
endpoints: ["/src/cambios/usuario_avisos_pref_form_data", "/src/cambios/cambio_usuario_objeto_pref_guardar", "/src/cambios/cambio_usuario_propiedad_pref_guardar_todas", "/src/cambios/cambio_usuario_propiedad_pref_preview"]
estado_revision: "revisado"
---

# Flujo - Configurar preferencia de aviso

## Objetivo De Usuario

Definir qué cambios debe recibir un usuario o grupo: objeto, ámbito (tipo/fase/casas) y propiedades con
condiciones opcionales.

## Punto De Entrada

Desde `usuario_form_avisos` (o gestión de grupos) con `salida=nuevo|modificar`.

## Escenarios

### Cargar formulario

1. `usuario_avisos_pref_form_data` devuelve opciones y preselección.
2. Al cambiar objeto/tipo, AJAX refresca fases y tabla de propiedades.

### Grabar

1. `fnjs_grabar_todo` → `cambio_usuario_objeto_pref_guardar`.
2. Si OK → `cambio_usuario_propiedad_pref_guardar_todas` con el POST de propiedades.
3. Condiciones intermedias: `cambio_usuario_propiedad_pref_preview` actualiza celdas sin persistir.

## Errores Conocidos

- `falta id_usuario`, `usuario/grupo no encontrado`, `preferencia no encontrada`
- `id_tipo_activ invalido`, `Hay un error, no se ha guardado`
- `faltan parametros` (propiedades)

## Ruta de menú

sin entrada de menú en el índice
