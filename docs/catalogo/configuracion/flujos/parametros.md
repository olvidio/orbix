---
id: "configuracion.parametros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Configurar parámetros del esquema"
capacidad: "configuracion.parametros.gestionar"
pantallas_principales: ["configuracion.pantalla.parametros"]
fragmentos: []
acciones: ["listar", "crear_actualizar"]
endpoints: ["/src/configuracion/parametros_lista", "/src/configuracion/parametros_update"]
estado_revision: "revisado"
---

# Flujo - Configurar parámetros del esquema

## Objetivo De Usuario

Consultar y modificar los parámetros globales del esquema (curso escolar, certificados,
idioma, ámbito territorial, gestión de calendario, etc.).

## Punto De Entrada

`frontend/configuracion/controller/parametros.php`

## Escenarios

### Cargar parámetros

1. Abrir menú config esquema / Esquema.
2. `parametros_lista` devuelve valores actuales y catálogo de idiomas.
3. Si hay error en la carga, la pantalla termina con el mensaje (`exit($data['error'])`).

### Guardar un parámetro

1. Editar el bloque deseado (cada formulario lleva `parametro` oculto vía HashFront).
2. Periodos STGR/CRT: campos `ini_dia`, `ini_mes`, `fin_dia`, `fin_mes` (no `valor`).
3. Resto: campo `valor` (texto, radio o desplegable).
4. «Guardar» → `parametros_update` → aviso «se ha guardado correctamente».

## Errores Conocidos

Sin mensajes `_()` documentados en el endpoint; fallos de carga inicial se muestran en pantalla.

## Ruta de menú

- **Legacy:** sistema > Configuración > config esquema
- **Pills2:** ADMIN LOCAL > Esquema; sistema > Configuración > config esquema
