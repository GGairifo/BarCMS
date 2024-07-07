-- Inserting sample data into utilizador table
INSERT INTO smi.utilizador (user_name, password, email, tipo_utilizador, token, active)
VALUES
    ('user1', 'password1', 'user1@example.com', 'utilizador', 'token1', 1),
    ('user2', 'password2', 'user2@example.com', 'administrador', 'token2', 1),
    ('user3', 'password3', 'user3@example.com', 'simpatizante', 'token3', 1);

-- Inserting sample data into bar table
INSERT INTO smi.bar (utilizador_id, nome, localizacao, descricao, contacto)
VALUES
    (1, 'Bar A', 'Rua A, Lisboa', 'Um bar acolhedor', '123-456-7890'),
    (2, 'Bar B', 'Avenida B, Porto', 'Um bar com boa música', '987-654-3210'),
    (1, 'Bar C', 'Praça C, Coimbra', 'Bar com ambiente descontraído', '456-789-0123');

-- Inserting sample data into horario table
INSERT INTO smi.horario (bar_id, dia_da_semana, hora_abre, hora_fecho)
VALUES
    (1, 'segunda', '18:00:00', '02:00:00'),
    (1, 'terca', '18:00:00', '02:00:00'),
    (1, 'quarta', '18:00:00', '02:00:00'),
    (2, 'quarta', '20:00:00', '03:00:00'),
    (2, 'quinta', '20:00:00', '03:00:00'),
    (3, 'sabado', '16:00:00', '01:00:00');

-- Inserting sample data into notificacao table
INSERT INTO smi.notificacao (utilizador_id, titulo, mensagem, data_hora)
VALUES
    (1, 'Promoção Especial', 'Hoje temos uma promoção em todos os cocktails!', NOW()),
    (2, 'Novo Evento', 'Amanhã teremos música ao vivo no Bar B!', NOW());

-- Inserting sample data into critica table
INSERT INTO smi.critica (utilizador_id, bar_id, conteudo, data_de_publicacao, classificacao)
VALUES
    (3, 1, 'Gostei muito do ambiente, mas os preços poderiam ser mais acessíveis.', NOW(), 4),
    (2, 2, 'Excelente atendimento e ambiente agradável!', NOW(), 5);

-- Inserting sample data into multimedia table
INSERT INTO smi.multimedia (bar_id, tipo, url)
VALUES
    (1, 'imagem', 'bar1.jpg');
    
