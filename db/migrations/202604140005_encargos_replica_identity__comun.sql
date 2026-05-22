-- global.encargos (tabla padre INHERITS): identidad de replicación para UPDATE en publicación lógica.
ALTER TABLE global.encargos REPLICA IDENTITY FULL;
