---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Propuestas Lista"
flujo: "encargossacd.propuestas_lista.gestionar.flujo"
preguntas: ["Como obtener lista en Propuestas Lista?", "Como asignar / cambiar sacd en Propuestas Lista?", "Como info / dedicación en Propuestas Lista?"]
pantallas_principales: ["encargossacd.pantalla.propuestas_lista"]
fragmentos: ["encargossacd.pantalla.propuestas_ajax"]
endpoints: ["/src/encargossacd/opciones_seccion_data", "/src/encargossacd/propuestas_ajax"]
source: "docs/catalogo/encargossacd/flujos/propuestas_lista.md"
estado_revision: "generado"
---

# Ayuda IA - Propuestas Lista

Usa este documento para responder preguntas de usuario sobre como trabajar con `Propuestas Lista`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener lista en Propuestas Lista?
- Como asignar / cambiar sacd en Propuestas Lista?
- Como info / dedicación en Propuestas Lista?

## Donde Entrar

- Propuestas Lista (`encargossacd.pantalla.propuestas_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener lista

1. Carga opciones de sección (`opciones_seccion_data`).
2. Al cambiar `filtro_ctr` o al recargar: `que=get_lista` vía FE ajax.
3. Inyecta HTML en `#lista` o alert del mensaje de error.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Asignar / cambiar sacd

1. Click en nombre → `lista_sacd` (desplegable).
2. Cambio en desplegable → `cmb_sacd` (alta/update/borrar colaborador vacío).
3. Actualiza fila o cierra popup.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Info / dedicación

1. `info` o `dedicacion` abren popup HTML.
2. Guardar horario: `dedicacion_update` y refresca lista.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.propuestas_lista`
- `encargossacd.pantalla.propuestas_ajax`

## Objetivo

Filtrar centros, ver encargos propuestos y cambiar SACD titular/suplente/colaborador o dedicación m/t/v antes de aprobar.

## Errores Documentados

- `Debe crear la tabla de propuestas`
- `No se puede guardar. Vuelva a cargar la vista`
- `Registro no encontrado`
- `Alert cliente: «Primero debe introducir un sacd» (id_sacd == 1)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
