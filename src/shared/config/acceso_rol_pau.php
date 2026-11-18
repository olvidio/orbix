<?php

/**
 * Qué pueden pedir desde el navegador los usuarios con un rol limitado a una persona, casa o centro (pau de
 * `aux_roles`), lo lee {@see \src\shared\infrastructure\ui\http\AccesoRolPau}. Revisión de seguridad del
 * 2026-11-18, punto 2.
 *
 * Rutas sin el prefijo de la instalación (`frontend/...php`, `src/<modulo>/<accion>`); `*` al final vale
 * por cualquier resto. Las llamadas internas de una pantalla (`PostRequest`) no se comprueban: solo lo que
 * pide el navegador, directamente o por `src_ajax.php`.
 *
 * Origen: las pantallas que abren en sus menús (spec E2E `roles-pau-menus`, con el registro de
 * `AccesoRolPau`) y lo que esas pantallas y sus vistas piden o enlazan. Lo que falte sale en
 * `log/acceso_pau_denegado.log`.
 *
 * @return array{comunes: list<string>, por_pau: array<string, list<string>>}
 */
return [
    'comunes' => [
        'index.php',
        'udm-style.php',
        // Ayuda y navegación
        'frontend/shared/controller/ayuda_index.php',
        'frontend/shared/controller/ayuda_preguntar.php',
        'frontend/shared/controller/manual.php',
        'frontend/shared/controller/nav_atras.php',
        'frontend/shared/controller/nav_state.php',
        // Preferencias, contraseña, correo y 2FA del propio usuario
        'frontend/usuarios/controller/preferencias.php',
        'frontend/usuarios/controller/usuario_form_2fa.php',
        'frontend/usuarios/controller/usuario_form_mail.php',
        'frontend/usuarios/controller/usuario_form_pwd.php',
        'frontend/usuarios/controller/usuario_reset_2fa.php',
        'frontend/usuarios/controller/ayuda_2fa_reset.php',
        'src/usuarios/usuario_preferencias',
        'src/usuarios/usuario_2fa_info',
        'src/usuarios/usuario_2fa_update',
        'src/usuarios/usuario_2fa_verify',
        'src/usuarios/usuario_check_pwd',
        'src/usuarios/usuario_guardar_mail_form_data',
        'src/usuarios/usuario_guardar_pwd',
        'src/usuarios/usuario_guardar_pwd_form_data',
        'src/shared/locales_posibles',
        // Avisos de cambios
        'frontend/cambios/controller/avisos_generar.php',
        'frontend/cambios/controller/usuario_avisos_pref.php',
        'frontend/cambios/controller/usuario_form_avisos.php',
        'src/cambios/avisos_generar_lista_data',
        'src/cambios/cambio_usuario_objeto_pref_eliminar',
        'src/cambios/usuario_avisos_pref_form_data',
        'src/cambios/usuario_form_avisos_data',
        // App móvil
        'src/menus/grupmenu_coleccion',
    ],
    'por_pau' => [
        // Centro: plan de misas del centro
        'ctr' => [
            'frontend/misas/controller/buscar_plan_ctr.php',
            'frontend/misas/controller/ver_plan_ctr.php',
            'src/misas/buscar_plan_ctr_data',
            'src/misas/ver_plan_ctr_data',
        ],
        // Sacerdote: su plan de misas, el de los centros y la comunicación de sus actividades
        'sacd' => [
            'frontend/misas/controller/buscar_plan_ctr.php',
            'frontend/misas/controller/ver_plan_ctr.php',
            'frontend/misas/controller/buscar_plan_sacd.php',
            'frontend/misas/controller/ver_plan_sacd.php',
            'src/misas/buscar_plan_ctr_data',
            'src/misas/ver_plan_ctr_data',
            'src/misas/buscar_plan_sacd_data',
            'src/misas/ver_plan_sacd_data',
            'frontend/actividadessacd/controller/com_sacd_activ_periodo.php',
            'frontend/actividadessacd/controller/com_sacd_txt.php',
            'src/actividadessacd/com_sacd_activ_periodo_page_data',
            'src/actividadessacd/comunicacion_activ_sacd_data',
            'src/actividadessacd/comunicacion_activ_sacd_enviar',
            'src/actividadessacd/locales_desplegable_data',
            'src/actividadessacd/texto_comunicacion_data',
            'src/actividadessacd/texto_comunicacion_guardar',
        ],
        // Casa: su casa, sus tarifas, su planning y las actividades en ella
        'cdc' => [
            'frontend/casas/controller/*',
            'src/casas/*',
            'frontend/actividadtarifas/controller/*',
            'src/actividadtarifas/*',
            'frontend/planning/controller/*',
            'src/planning/*',
            'frontend/actividades/controller/actividad_mutacion_ajax.php',
            'frontend/actividades/controller/actividad_select_ubi.php',
            'frontend/actividades/controller/calendario_listas.php',
            'frontend/actividades/controller/planning_casa_modificar.php',
            'frontend/actividades/controller/planning_casa_nueva.php',
            'src/actividades/actividad_cambiar_tipo',
            'src/actividades/actividad_editar',
            'src/actividades/actividad_nuevo',
            'src/actividades/actividad_select_ubi_desplegable',
            'src/actividades/actividad_status_labels_datos',
            'src/actividades/actividad_tipo_get',
            'src/actividades/actividad_ver_datos',
            'src/actividades/calendario_listas_datos',
            'frontend/ubis/controller/ubis_lista.php',
            'src/ubis/ubis_lista_data',
        ],
    ],
];
