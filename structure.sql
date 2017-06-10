DROP DATABASE IF EXISTS {{dabatase}};

CREATE DATABASE {{dabatase}} CHARACTER SET utf16 COLLATE utf16_general_ci;

USE {{dabatase}};
CREATE TABLE IF NOT EXISTS users
  (
     id           INT auto_increment,
     username     VARCHAR(20) NOT NULL,
     useremail    VARCHAR(100) NOT NULL,
     userpassword VARCHAR(256),
     creationdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
     userimg      VARCHAR(100),
     active       BOOLEAN NOT NULL DEFAULT true,
     userrole     ENUM('user', 'admin') DEFAULT 'user',
     CONSTRAINT badusersprimarykey PRIMARY KEY (id)
  );

CREATE TABLE IF NOT EXISTS categories
  (
     id     INT auto_increment,
     nombre VARCHAR(50),
     CONSTRAINT badcategoriesprimarykey PRIMARY KEY (id)
  );

INSERT INTO categories (nombre) values
  ("Documental"), ("Series"), ("Peliculas"), ("Gameplay"), ("Otros")
;

CREATE TABLE IF NOT EXISTS videos
  (
     id           INT auto_increment,
     filename     VARCHAR(100) NOT NULL,
     videoname    VARCHAR(100) NOT NULL,
     description  VARCHAR(2000) NOT NULL,
     creationdate DATETIME NOT NULL,
     userid       INT,
     cat          INT,
     videoimg     VARCHAR(100),
     public       BOOLEAN,
     active       BOOLEAN,
     CONSTRAINT badvideosprimarykey PRIMARY KEY (id),
     CONSTRAINT badvideosusersforeignkey FOREIGN KEY (userid) REFERENCES users (
     id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badvideoscatforeignkey FOREIGN KEY (cat) REFERENCES categories (
     id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS resources
  (
     id           INT auto_increment,
     filename         VARCHAR(100),
     creationdate DATE,
     userid       INT,
     restype      ENUM('subtitles', "transcription", "signal-language"),
     lang         ENUM('spanish', 'english'),
     video        INT,
     CONSTRAINT badresourcesprimarykey PRIMARY KEY (id),
     CONSTRAINT badresourcesusersforeignkey FOREIGN KEY (userid) REFERENCES
     users (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badresourcesvideoforeignkey FOREIGN KEY (video) REFERENCES
     videos (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS comments
  (
     id            INT auto_increment,
     creationdate  DATE,
     parentcomment INT,
     userid        INT,
     video         INT,
     comments      VARCHAR(300),
     CONSTRAINT badcommentprimarykey PRIMARY KEY (id),
     CONSTRAINT badcommentsusersforeignkey FOREIGN KEY (userid) REFERENCES users
     (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badcommentscommentsforeignkey FOREIGN KEY (parentcomment) REFERENCES
     comments (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badcommentsvideosforeignkey FOREIGN KEY (video) REFERENCES
     videos (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS genres
  (
     id     INT auto_increment,
     nombre VARCHAR(50),
     CONSTRAINT badcategoriesprimarykey PRIMARY KEY (id)
  );

INSERT INTO genres (nombre) values
  ("Accion"), ("Shooter"), ("Rol"), ("Comedia"), ("Miedo"), ("Suspense"), ("Sci-fy")
;

CREATE TABLE IF NOT EXISTS video_genres
  (
     video INT,
     genres  INT,
     CONSTRAINT badvideo_tagsprimarykey PRIMARY KEY (video, genres),
     CONSTRAINT badvideo_tagsvideoforeignkey FOREIGN KEY (video) REFERENCES
     videos (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS votos_video
  (
     video  INT,
     userid INT,
     voto   ENUM("1", "-1"),
     CONSTRAINT badvotos_videoprimarykey PRIMARY KEY (video, userid),
     CONSTRAINT badvotos_videovideoforeignkey FOREIGN KEY (video) REFERENCES
     videos (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badvotos_videouserforeignkey FOREIGN KEY (userid) REFERENCES
     users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS votos_comentario
  (
     comments INT,
     userid   INT,
     voto     ENUM("1", "-1"),
     CONSTRAINT badvotos_comentarioprimarykey PRIMARY KEY (comments, userid),
     CONSTRAINT badvotos_comentariovideoforeignkey FOREIGN KEY (comments)
     REFERENCES comments (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badvotos_comentariouserforeignkey FOREIGN KEY (userid)
     REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS uuid
  (
     token CHAR(64),
     uid   INT,
     date  DATETIME DEFAULT CURRENT_TIMESTAMP,
     CONSTRAINT baduuidprimarykey PRIMARY KEY (uid),
     CONSTRAINT baduuiduidforeignkey FOREIGN KEY (uid) REFERENCES users (id) ON
     UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS following
  (
     follower INT,
     followed INT,
     CONSTRAINT badfollowingprimarykey PRIMARY KEY (follower, followed),
     CONSTRAINT badfollowingfollowerforeignkey FOREIGN KEY (follower) REFERENCES
     users (id) ON UPDATE CASCADE ON DELETE CASCADE,
     CONSTRAINT badfollowingfollowedforeignkey FOREIGN KEY (followed) REFERENCES
     users (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS history
  (
     user  INT,
     video INT,
     date  DATETIME DEFAULT CURRENT_TIMESTAMP,
     CONSTRAINT badhistoryprimarykey PRIMARY KEY (user, video),
     CONSTRAINT badhistoryuserforeignkey FOREIGN KEY (user) REFERENCES users (id
     ),
     CONSTRAINT badhistoryvideoforeignkey FOREIGN KEY (video) REFERENCES videos
     (id)
  );
