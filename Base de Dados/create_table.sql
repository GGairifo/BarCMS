DROP TABLE IF EXISTS smi.horario;
DROP TABLE IF EXISTS smi.multimedia;
DROP TABLE IF EXISTS smi.critica;
DROP TABLE IF EXISTS smi.notificacao;
DROP TABLE IF EXISTS smi.bar;
DROP TABLE IF EXISTS smi.utilizador;

CREATE TABLE smi.utilizador (
    utilizador_id INT AUTO_INCREMENT,
    user_name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    tipo_utilizador ENUM('convidado', 'utilizador', 'simpatizante', 'administrador') NOT NULL,
    token VARCHAR(255) NOT NULL,
    active tinyint NOT NULL,
    PRIMARY KEY (utilizador_id)
);

CREATE TABLE smi.bar (
    bar_id INT AUTO_INCREMENT,
    utilizador_id INT,
    nome VARCHAR(255) NOT NULL,
    localizacao VARCHAR(255) NOT NULL,
    descricao TEXT,
    contacto VARCHAR(20) NOT NULL,
    PRIMARY KEY (bar_id),
    FOREIGN KEY (utilizador_id) REFERENCES utilizador(utilizador_id)
);

CREATE TABLE smi.horario (
    id_horario INT AUTO_INCREMENT PRIMARY KEY,
    bar_id INT,
    dia_da_semana ENUM('segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'),
    hora_abre TIME,
    hora_fecho TIME,
    FOREIGN KEY (bar_id) REFERENCES bar(bar_id)
);

CREATE TABLE smi.notificacao (
    id_notificacao INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT,
    titulo VARCHAR(255),
    mensagem TEXT,
    data_hora DATETIME,
    FOREIGN KEY (utilizador_id) REFERENCES utilizador(utilizador_id)
);

CREATE TABLE smi.critica (
    id_critica INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT,
    bar_id INT,
    conteudo TEXT,
    data_de_publicacao DATETIME,
    classificacao INT,
    FOREIGN KEY (utilizador_id) REFERENCES utilizador(utilizador_id),
    FOREIGN KEY (bar_id) REFERENCES bar(bar_id)
);

CREATE TABLE smi.multimedia (
    id_mult INT AUTO_INCREMENT PRIMARY KEY,
    bar_id INT,
    tipo ENUM('video', 'imagem'),
    url VARCHAR(255),
    FOREIGN KEY (bar_id) REFERENCES bar(bar_id)
);