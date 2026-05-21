<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

/**
 * Definiciones de ALTER COLUMN … SET DEFAULT usadas en {@see RenombrarEsquema}
 * y en {@see VerificarEstadoRenombrarEsquema}.
 *
 * @package src\devel_db_admin\application
 */
final class RenombrarEsquemaDefaultsCatalog
{
    /**
     * @return list<array{tabla: string, campo: string, valor: string}>
     */
    public static function comun(string $esquema, string $region, string $dl): array
    {
        return [
            ['tabla' => 'a_actividad_proceso_sf', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'a_actividad_proceso_sv', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'a_actividades_dl', 'campo' => 'id_activ', 'valor' => "public.bigglobal('$esquema'::text, 'a_actividades_dl'::text)"],
            ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'valor' => "'$dl'::character varying"],
            ['tabla' => 'a_fases', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'a_tareas', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'a_tareas_proceso', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'a_tipos_actividad', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'a_tipos_proceso', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'av_cambios_anotados_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'av_cambios_anotados_dl_sf', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'av_cambios_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'av_cambios_usuario', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'cd_cargos_activ_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'cp_sacd', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'cu_centros_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'cu_centros_dlf', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'da_ctr_encargados', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'da_ingresos_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'du_gastos_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'du_grupos_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'du_grupos_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'du_periodos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'du_tarifas', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'u_cdc_dl', 'campo' => 'id_ubi', 'valor' => "public.idglobal('$esquema'::text, 'u_cdc_dl'::text)"],
            ['tabla' => 'u_cdc_dl', 'campo' => 'dl', 'valor' => "'$dl'::character varying"],
            ['tabla' => 'u_cdc_dl', 'campo' => 'region', 'valor' => "'$region'::character varying"],
            ['tabla' => 'u_dir_cdc_dl', 'campo' => 'id_direccion', 'valor' => "public.idglobal('$esquema'::text, 'u_dir_cdc_dl'::text)"],
            ['tabla' => 'x_config_schema', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'xa_tipo_activ_tarifa', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
            ['tabla' => 'xa_tipo_tarifa', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquema'::text)"],
        ];
    }

    /**
     * @return list<array{tabla: string, campo: string, valor: string}>
     */
    public static function sv(string $esquemav, string $region, string $dl): array
    {
        return [
            ['tabla' => 'd_asignaturas_activ_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_congresos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_docencia_stgr', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_dossiers_abiertos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_matriculas_activ_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_profesor_ampliacion', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_profesor_director', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_profesor_juramento', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_profesor_latin', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_profesor_stgr', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_publicaciones', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_teleco_ctr_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_teleco_personas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_titulo_est', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_traslados', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_ultima_asistencia', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'da_plazas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'dap_plazas_peticion_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'du_presentacion_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'e_actas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'e_actas_tribunal_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'e_notas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'p_agregados', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'p_agregados', 'campo' => 'id_nom', 'valor' => "public.idglobal('$esquemav'::text, 'p_agregados'::text)"],
            ['tabla' => 'p_agregados', 'campo' => 'dl', 'valor' => "'$dl'::character varying"],
            ['tabla' => 'p_de_paso_out', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'p_de_paso_out', 'campo' => 'dl', 'valor' => "'$dl'::character varying"],
            ['tabla' => 'p_numerarios', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'p_numerarios', 'campo' => 'id_nom', 'valor' => "public.idglobal('$esquemav'::text, 'p_numerarios'::text)"],
            ['tabla' => 'p_numerarios', 'campo' => 'dl', 'valor' => "'$dl'::character varying"],
            ['tabla' => 'p_sssc', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'p_sssc', 'campo' => 'id_nom', 'valor' => "public.idglobal('$esquemav'::text, 'p_sssc'::text)"],
            ['tabla' => 'p_sssc', 'campo' => 'dl', 'valor' => "'$dl'::character varying"],
            ['tabla' => 'p_supernumerarios', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'p_supernumerarios', 'campo' => 'id_nom', 'valor' => "public.idglobal('$esquemav'::text, 'p_supernumerarios'::text)"],
            ['tabla' => 'p_supernumerarios', 'campo' => 'dl', 'valor' => "'$dl'::character varying"],
            ['tabla' => 'personas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'u_centros_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'u_centros_dl', 'campo' => 'id_ubi', 'valor' => "public.idglobal('$esquemav'::text, 'u_centros_dl'::text)"],
            ['tabla' => 'u_centros_dl', 'campo' => 'dl', 'valor' => "'$dl'::character varying"],
            ['tabla' => 'u_centros_dl', 'campo' => 'region', 'valor' => "'$region'::character varying"],
            ['tabla' => 'u_cross_ctr_dl_dir', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'u_dir_ctr_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'u_dir_ctr_dl', 'campo' => 'id_direccion', 'valor' => "public.idglobal('$esquemav'::text, 'u_dir_ctr_dl'::text)"],
        ];
    }

    /**
     * @return list<array{tabla: string, campo: string, valor: string}>
     */
    public static function svE(string $esquemav, string $dl): array
    {
        return [
            ['tabla' => 'a_sacd_textos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'aux_cross_usuarios_grupos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'aux_grupmenu', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'aux_grupmenu_rol', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'aux_grupo_permmenu', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'aux_grupos_y_usuarios', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'aux_menus', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'aux_usuarios', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'aux_usuarios_ctr_perm', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'aux_usuarios_perm', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'av_cambios_usuario_objeto_pref', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'av_cambios_usuario_objeto_pref', 'campo' => 'dl_org', 'valor' => "'$dl'::character varying"],
            ['tabla' => 'av_cambios_usuario_propiedades_pref', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_asistentes_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_asistentes_out', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'd_cargos_activ_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargo_datos_cgi', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargo_horario', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargo_horario_excepcion', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargo_sacd_horario', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargo_sacd_horario_excepcion', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargo_sacd_observ', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargo_textos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargo_tipo', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'encargos_sacd', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'm0_mods_installed_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'propuesta_encargo_sacd_horario', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'propuesta_encargos_sacd', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'web_preferencias', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'zonas', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'zonas_grupos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
            ['tabla' => 'zonas_sacd', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ];
    }
}
