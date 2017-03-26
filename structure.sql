DROP DATABASE IF EXISTS webplayer;

CREATE DATABASE webplayer CHARACTER SET utf16 COLLATE utf16_general_ci;

USE webplayer;

CREATE TABLE IF NOT EXISTS users
  (
     id           INT auto_increment,
     username     VARCHAR(20) NOT NULL,
     userPassword VARCHAR(256),
     creationdate DATE NOT NULL,
     active       BOOLEAN NOT NULL,
     CONSTRAINT badusersprimarykey PRIMARY KEY (id)
  );

CREATE TABLE IF NOT EXISTS videos
  (
     id           INT auto_increment,
     filename     VARCHAR(100) NOT NULL,
     creationdate DATE NOT NULL,
     userID        INT,
     public       BOOLEAN,
     active       BOOLEAN,
     CONSTRAINT badvideosprimarykey PRIMARY KEY (id),
     CONSTRAINT badvideosusersforeignkey FOREIGN KEY (userID) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS resources
  (
     id           INT auto_increment,
     file         VARCHAR(100),
     creationdate DATE,
     userID        INT,
     type         ENUM('subtitles', "transcription", "signal-language", "additional-audio"),
     CONSTRAINT badresourcesprimarykey PRIMARY KEY (id),
     CONSTRAINT badresourcesusersforeignkey FOREIGN KEY (userID) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS comments
  (
     id            INT auto_increment,
     creationdate  DATE,
     parentcomment INT,
     userID         INT,
     video         INT,
     CONSTRAINT badcommentprimarykey PRIMARY KEY (id),
     CONSTRAINT badcommentsusersforeignkey FOREIGN KEY (userID) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badcommentscommentsforeignkey FOREIGN KEY (userID) REFERENCES comments (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badcommentsvideosforeignkey FOREIGN KEY (userID) REFERENCES videos (id) ON UPDATE CASCADE ON DELETE CASCADE
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
     userID INT,
     voto  ENUM("1", "-1"),
     CONSTRAINT badvotos_videovideoforeignkey FOREIGN KEY (video) REFERENCES videos (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badvotos_videouserforeignkey FOREIGN KEY (userID) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS votos_comentario
  (
     comments INT,
     userID    INT,
     voto     ENUM("1", "-1"),
     CONSTRAINT badvotos_comentariovideoforeignkey FOREIGN KEY (comments)
     REFERENCES comments (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badvotos_comentariouserforeignkey FOREIGN KEY (userID) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS resources_video
  (
     video    INT,
     resource INT,
     CONSTRAINT badresources_videovideoforeignkey FOREIGN KEY (video) REFERENCES videos (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badresources_videoresourcesforeignkey FOREIGN KEY (resource)
     REFERENCES resources (id) ON UPDATE CASCADE ON DELETE CASCADE
  );
