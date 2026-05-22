-- Esquema global (sv-e)
---ALTER TABLE global.m0_mods_installed DROP COLUMN IF EXISTS param;
---ALTER TABLE global.m0_mods_installed RENAME COLUMN status TO active;
---ALTER TABLE global.av_cambios_usuario_objeto_pref RENAME COLUMN id_pau TO csv_id_pau;

-- Todos los esquemas activos sv-e (*v)
ALTER TABLE *.aux_usuarios RENAME COLUMN id_pau TO csv_id_pau;
