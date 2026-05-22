-- Tablas padre en publicación lógica: identidad de replicación para UPDATE (sv-e).
ALTER TABLE global.encargo_textos REPLICA IDENTITY FULL;
ALTER TABLE global.a_sacd_textos REPLICA IDENTITY FULL;
