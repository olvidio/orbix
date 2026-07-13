---
id: "actividadessacd.comunicacion_activ_sacd_enviar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Comunicacion Activ Sacd Enviar"
capacidad: "actividadessacd.comunicacion_activ_sacd_enviar.gestionar"
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_activ_periodo"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/comunicacion_activ_sacd_enviar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Comunicacion Activ Sacd Enviar

Envío de correos de comunicación de actividades a los sacd.

## Objetivo De Usuario

El usuario pulsa **enviar mail**: el sistema encola los correos de comunicación (uno por sacd con
copia al jefe de calendario, y otro para el ctr del sacd si tiene email). Requiere un periodo válido
y el jefe de calendario configurado.

## Punto De Entrada

Pantalla `com_sacd_activ_periodo` (`frontend/actividadessacd/controller/com_sacd_activ_periodo.php`):
la función `fnjs_enviar_mails` llama a este endpoint al pulsar **enviar mail**.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.com_sacd_activ_periodo`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. Tener un listado generado (flujo de búsqueda previo).
2. Pulsar **enviar mail**.
3. El sistema encola los correos y muestra el resultado.

Endpoints asociados:
- `/src/actividadessacd/comunicacion_activ_sacd_enviar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/comunicacion_activ_sacd_enviar`

## Errores Conocidos

- ``falta determinar un periodo``

## Ruta de menú

Se accede desde la pantalla `com_sacd_activ_periodo`:

- **Legacy:** dre > actividades > comunic. sacd · exterior > sacd > atención actividades
- **Pills2:** ATENCIÓN SACD > Actividades > Comunicación a los sacd

Con `propuesta=true`: dre > propuestas > lista activ. sacd.
