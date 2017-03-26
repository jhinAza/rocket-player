DROP DATABASE IF EXISTS webplayer;

CREATE DATABASE webplayer;

CREATE TABLE IF NOT EXISTS users
  (
     id           INT auto_increment,
     username     VARCHAR(20) NOT NULL,
     password     VARCHAR(256),
     creationdate DATE NOT NULL,
     active       BOOLEAN NOT NULL,
     CONSTRAINT badusersprimarykey PRIMARY KEY (id)
  );

CREATE TABLE IF NOT EXISTS videos
  (
     id           INT auto_increment,
     file         VARCHAR(100) NOT NULL,
     creationdate DATE NOT NULL,
     user         INT,
     public       BOOLEAN,
     active       BOOLEAN,
     CONSTRAINT badvideosprimarykey PRIMARY KEY (id),
     CONSTRAINT badvideosusersforeignkey FOREIGN KEY (user) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS resources
  (
     id           INT auto_increment,
     file         VARCHAR(100),
     creationdate DATE,
     user         INT,
     type         ENUM('subtitles', "transcription", "signal-language", "additional-audio"),
     CONSTRAINT badresourcesprimarykey PRIMARY KEY (id),
     CONSTRAINT badresourcesusersforeignkey FOREIGN KEY (user) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS comments
  (
     id            INT auto_increment,
     creationdate  DATE,
     parentcomment INT,
     user          INT,
     video         INT,
     CONSTRAINT badcommentprimarykey PRIMARY KEY (id),
     CONSTRAINT badcommentsusersforeignkey FOREIGN KEY (user) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badcommentscommentsforeignkey FOREIGN KEY (user) REFERENCES comments (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badcommentsvideosforeignkey FOREIGN KEY (user) REFERENCES videos (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS tags
  (
     id     INT auto_increment,
     nombre VARCHAR(50),
     CONSTRAINT badtagsprimarykey PRIMARY KEY (id)
  );

CREATE TABLE IF NOT EXISTS video_tags
  (
     video INT,
     tags  INT,
     CONSTRAINT badvideo_tagsvideoforeignkey FOREIGN KEY (video) REFERENCES videos (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badvideo_tagstagsforeignkey FOREIGN KEY (tags) REFERENCES tags (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS votos_video
  (
     video INT,
     user  INT,
     voto  ENUM("1", "-1"),
     CONSTRAINT badvotos_videovideoforeignkey FOREIGN KEY (video) REFERENCES videos (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badvotos_videouserforeignkey FOREIGN KEY (user) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS votos_comentario
  (
     comments INT,
     user     INT,
     voto     ENUM("1", "-1"),
     CONSTRAINT badvotos_comentariovideoforeignkey FOREIGN KEY (comments)
     REFERENCES comments (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badvotos_comentariouserforeignkey FOREIGN KEY (user) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS resources_video
  (
     video    INT,
     resource INT,
     CONSTRAINT badresources_videovideoforeignkey FOREIGN KEY (video) REFERENCES videos (id) ON UPDATE CASCADE ON DELETE CASCADE, 
     CONSTRAINT badresources_videoresourcesforeignkey FOREIGN KEY (resource)
     REFERENCES resources (id) ON UPDATE CASCADE ON DELETE CASCADE
  );
