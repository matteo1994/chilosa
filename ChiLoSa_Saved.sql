
 CREATE TABLE "Utente"
(
  "Nome" character varying NOT NULL, -- Nome Utente
  "Mail" character varying NOT NULL,
  "Data_di_Nascita" date,
  "Residenza" character varying,
  "Vip" boolean DEFAULT false,
  "Nome_Vero" character varying,
  "Cognome_Vero" character varying,
  "Password" character varying(16),
  "Immagine" character varying DEFAULT 'http://chilosa.altervista.org/profilo.jpg',
  CONSTRAINT "Utente_pkey" PRIMARY KEY ("Nome"),
  CONSTRAINT "Utente_Mail_Nome_Vero_Cognome_vero_Nome_key" UNIQUE ("Mail", "Nome_Vero", "Cognome_Vero", "Nome")
);


CREATE TABLE "Topic"
(
  "NomeTopic" character varying NOT NULL,
  "NomePadre" character varying,
  CONSTRAINT "Topic_pkey" PRIMARY KEY ("NomeTopic")
);


CREATE TABLE "Domande_Aperte"
(
  "Utente" character varying  NOT NULL, -- Utente che ha fatto la domanda
  "ID_Domanda" serial NOT NULL,
  "Immagine" character varying,
  "Testo" character varying NOT NULL,
  "Data" timestamp with time zone DEFAULT NOW(),
  "Descrizione" character varying,
  "Chiuso" boolean DEFAULT false,
  CONSTRAINT "Domande_Aperte_pkey" PRIMARY KEY ("ID_Domanda"),
  CONSTRAINT "Domande_Aperte_Utente_fkey" FOREIGN KEY ("Utente")
      REFERENCES "Utente" ("Nome") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Domande_Aperte_ID_Domanda_key" UNIQUE ("Utente","Data")
);


CREATE TABLE "Sondaggio"
(
  "Utente" character varying NOT NULL,
  "Data" timestamp with time zone NOT NULL DEFAULT NOW(),
  "ID_Sondaggio" serial NOT NULL,
  "Testo" character varying,
  "Chiuso" boolean DEFAULT false,
  "Immagine" character varying,
  "Descrizione" character varying,
  
  CONSTRAINT "Sondaggio_pkey" PRIMARY KEY ("ID_Sondaggio"),
  CONSTRAINT "Sondaggio_Utente_fkey" FOREIGN KEY ("Utente")
      REFERENCES "Utente" ("Nome") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Sondaggio_ID_Sondaggio_key" UNIQUE ("Utente", "Data")
);


CREATE TABLE "Interessi"
(
  "Utente" character varying NOT NULL,
  "Topic" character varying NOT NULL,
  CONSTRAINT "Interessi_pkey" PRIMARY KEY ("Utente", "Topic"),
  CONSTRAINT "Interessi_Topic_fkey" FOREIGN KEY ("Topic")
      REFERENCES "Topic" ("NomeTopic") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Interessi_Utente_fkey" FOREIGN KEY ("Utente")
      REFERENCES "Utente" ("Nome") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TABLE "Risposte_Aperte"
(
  "ID_Domanda" serial NOT NULL,
  "ID_Risposta" serial NOT NULL,
  "Data" timestamp with time zone NOT NULL DEFAULT NOW(),
  "Utente" character varying NOT NULL,
  "Testo" character varying,
  CONSTRAINT "Risposte_Aperte_pkey" PRIMARY KEY ("Utente", "Data"),
  CONSTRAINT "Risposte_Aperte_ID_Domanda_fkey" FOREIGN KEY ("ID_Domanda")
      REFERENCES "Domande_Aperte" ("ID_Domanda") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Risposte_Aperte_Utente_fkey" FOREIGN KEY ("Utente")
      REFERENCES "Utente" ("Nome") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Risposte_Aperte_ID_Risposta_key" UNIQUE ("ID_Risposta")
);  

CREATE TABLE "Risposte_Sondaggio"
(
  "ID_Sondaggio" serial NOT NULL,
  "Testo" character varying NOT NULL,
  "ID_Risposta" serial NOT NULL,
  "Data" timestamp with time zone DEFAULT NOW(),
  CONSTRAINT "Risposte_Sondaggio_pkey" PRIMARY KEY ("ID_Sondaggio", "ID_Risposta"),
  CONSTRAINT "Risposte_Sondaggio_ID_Sondaggio_fkey" FOREIGN KEY ("ID_Sondaggio")
      REFERENCES "Sondaggio" ("ID_Sondaggio") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Risposte_Sondaggio_ID_Risposta_key" UNIQUE ("ID_Risposta")
);

CREATE TABLE "Votanti"
(
  "Utente" character varying NOT NULL,
  "ID_Risposta" serial NOT NULL,
  "ID_Sondaggio" serial NOT NULL,
  "Anonimo" boolean,
  CONSTRAINT "Votanti_pkey" PRIMARY KEY ("Utente", "ID_Risposta"),
  CONSTRAINT "Votanti_key" UNIQUE("Utente", "ID_Sondaggio"),
  CONSTRAINT "Votanti_ID_Risposta_fkey" FOREIGN KEY ("ID_Risposta")
      REFERENCES "Risposte_Sondaggio" ("ID_Risposta") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Votanti_Utente_fkey" FOREIGN KEY ("Utente")
      REFERENCES "Utente" ("Nome") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Votanti_Sondaggio_fkey" FOREIGN KEY ("ID_Sondaggio")
	  REFERENCES "Sondaggio" ("ID_Sondaggio") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
); 

CREATE TABLE "Voto"
(
  "Utente" character varying NOT NULL,
  "ID_Risposta" serial NOT NULL,
  "Like" integer NOT NULL,
  "Dislike" integer NOT NULL,
  CONSTRAINT "Voto_pkey" PRIMARY KEY ("Utente", "ID_Risposta"),
  CONSTRAINT "Voto_ID_Risposta_fkey" FOREIGN KEY ("ID_Risposta")
      REFERENCES "Risposte_Aperte" ("ID_Risposta") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Voto_Utente_fkey" FOREIGN KEY ("Utente")
      REFERENCES "Utente" ("Nome") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Like_check" CHECK ("Like" = 1 OR "Like" = 0),
  CONSTRAINT "Dislike_check" CHECK ("Dislike" = 1 OR "Dislike" = 0),
  CONSTRAINT "Voto_Vincolo_check" CHECK ("Like" <> "Dislike")	
);

CREATE TABLE "Appartenenza_Domande"
(
  "Topic" character varying NOT NULL,
  "ID_Domanda" integer NOT NULL,
  CONSTRAINT "Appartenenza_pkey" PRIMARY KEY ("ID_Domanda", "Topic"),
  CONSTRAINT "Appartenenza_ID_Domanda_fkey" FOREIGN KEY ("ID_Domanda")
      REFERENCES "Domande_Aperte" ("ID_Domanda") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Appartenenza_Topic_fkey" FOREIGN KEY ("Topic")
      REFERENCES "Topic" ("NomeTopic") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
);  

CREATE TABLE "Appartenenza_Sondaggio"
(
  "Topic" character varying NOT NULL,
  "ID_Sondaggio" integer NOT NULL,
  CONSTRAINT "Appartenenza_Sondaggio pkey" PRIMARY KEY ("ID_Sondaggio", "Topic"),
  CONSTRAINT "Appartenenza_ID_Sondaggio_fkey" FOREIGN KEY ("ID_Sondaggio")
      REFERENCES "Sondaggio" ("ID_Sondaggio") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT "Appartenenza_Sondaggio_Topic_fkey" FOREIGN KEY ("Topic")
      REFERENCES "Topic" ("NomeTopic") MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
);
  
  
/* 
	Viste utili per le interrogazioni, rappresentano statistiche incrociate sulle varie tabelle di interesse
 */

CREATE VIEW "Risposte_Aperte_Stats" AS
SELECT "ID_Risposta", SUM("Like") AS "Likes", SUM("Dislike") AS "Dislikes"
FROM "Voto"
GROUP BY "ID_Risposta";

CREATE VIEW "Utente_Stats" AS
SELECT "Utente", COUNT(*) AS "Numero_Risposte", SUM(COALESCE("Likes",0)) AS "Likes", SUM(COALESCE("Dislikes",0)) AS "Dislikes"
FROM "Risposte_Aperte" NATURAL LEFT JOIN "Risposte_Aperte_Stats"
GROUP BY "Utente";

CREATE VIEW "Risposte_Sondaggio_Stats" AS
SELECT "ID_Risposta", COUNT(*) AS "Numero_Votanti"
FROM "Votanti"
GROUP BY "ID_Risposta";



/* 
	Query per il popolamento del DataBase
 */

 
INSERT INTO "Utente" ("Nome", "Mail", "Password", "Nome_Vero", "Cognome_Vero", "Residenza", "Data_di_Nascita", "Immagine")
VALUES ('Matteo', 'matteo.marras@studenti.unimi.it', '12345', 'Matteo', 'Marras', 'Oristano', '05/04/1994', 'http://image2.spreadshirtmedia.com/image-server/v1/compositions/1002344652/views/1,width=235,height=235,appearanceId=2/Programmer-Turn-Coffee-.jpg');

INSERT INTO "Utente" ("Nome", "Mail", "Password", "Nome_Vero", "Cognome_Vero", "Immagine")
VALUES ('Davide', 'davide.crespellaniporcella@studenti.unimi.it', '12345', 'Davide', 'Crespellani Porcella', 'http://devstickers.com/assets/img/pro/8bul.png');

INSERT INTO "Utente" ("Nome", "Mail", "Password", "Nome_Vero", "Cognome_Vero")
VALUES ('Mario', 'mario.rossi@milano.it', '12345', 'Mario', 'Rossi');

INSERT INTO "Utente" ("Nome", "Mail", "Password", "Nome_Vero", "Cognome_Vero")
VALUES ('Andrea', 'andrea.bianchi@milano.it', '12345', 'Andrea', 'Bianchi');

INSERT INTO "Utente" ("Nome", "Mail", "Password", "Nome_Vero", "Cognome_Vero", "Residenza","Immagine")
VALUES ('SuperMan', 'super.man@kripton.ufo', '12345', 'Clarck', 'Kent', 'Smallville', 'http://mugs.mugbug.co.uk/500/hmb.superman_badge.coaster.jpg');

INSERT INTO "Topic" ("NomeTopic") 
VALUES ('Scienze');

INSERT INTO "Topic" ("NomeTopic") 
VALUES ('Sport');

INSERT INTO "Topic" ("NomeTopic") 
VALUES ('Musica');

INSERT INTO "Topic" ("NomeTopic") 
VALUES ('Cucina');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Informatica','Scienze');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Fisica','Scienze');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Programmazione','Informatica');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('DataBase','Informatica');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Meccanica','Fisica');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Calcio','Sport');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Nuoto','Sport');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Basket','Sport');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Musica Rock','Musica');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Hard Rock','Musica Rock');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Hip Hop','Musica');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Rap','Hip Hop');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Cucina Orientale','Cucina');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Sushi','Cucina Orientale');

INSERT INTO "Topic" ("NomeTopic", "NomePadre") 
VALUES ('Cinese','Cucina Orientale');


INSERT INTO "Interessi" VALUES ('Matteo', 'Scienze');
INSERT INTO "Interessi" VALUES ('Matteo', 'Informatica');
INSERT INTO "Interessi" VALUES ('Matteo', 'Programmazione');
INSERT INTO "Interessi" VALUES ('Matteo', 'DataBase');
INSERT INTO "Interessi" VALUES ('Matteo', 'Fisica');
INSERT INTO "Interessi" VALUES ('Matteo', 'Meccanica');
INSERT INTO "Interessi" VALUES ('Matteo', 'Sport');
INSERT INTO "Interessi" VALUES ('Matteo', 'Calcio');
INSERT INTO "Interessi" VALUES ('Matteo', 'Nuoto');
INSERT INTO "Interessi" VALUES ('Matteo', 'Basket');
INSERT INTO "Interessi" VALUES ('Davide', 'Informatica');
INSERT INTO "Interessi" VALUES ('Davide', 'Programmazione');
INSERT INTO "Interessi" VALUES ('Davide', 'DataBase');
INSERT INTO "Interessi" VALUES ('Davide', 'Musica Rock');
INSERT INTO "Interessi" VALUES ('Davide', 'Hard Rock');
INSERT INTO "Interessi" VALUES ('Davide', 'Cucina Orientale');
INSERT INTO "Interessi" VALUES ('Davide', 'Sushi');
INSERT INTO "Interessi" VALUES ('Davide', 'Cinese');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Scienze');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Informatica');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Programmazione');
INSERT INTO "Interessi" VALUES ('SuperMan', 'DataBase');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Fisica');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Meccanica');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Sport');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Calcio');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Nuoto');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Basket');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Musica');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Musica Rock');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Hard Rock');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Hip Hop');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Rap');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Cucina');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Cucina Orientale');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Sushi');
INSERT INTO "Interessi" VALUES ('SuperMan', 'Cinese');
INSERT INTO "Interessi" VALUES ('Andrea', 'Sport');
INSERT INTO "Interessi" VALUES ('Andrea', 'Calcio');
INSERT INTO "Interessi" VALUES ('Andrea', 'Nuoto');
INSERT INTO "Interessi" VALUES ('Andrea', 'Basket');
INSERT INTO "Interessi" VALUES ('Andrea', 'Cucina');
INSERT INTO "Interessi" VALUES ('Andrea', 'Cucina Orientale');
INSERT INTO "Interessi" VALUES ('Andrea', 'Sushi');
INSERT INTO "Interessi" VALUES ('Andrea', 'Cinese');

INSERT INTO "Domande_Aperte" VALUES ('Matteo', 5, 'http://questiontechnews.it/wp-content/uploads/2013/08/php_xml.jpg', 'Qual''è il miglior linguaggio di programmazione per interagire con un Server?', '2015-06-20 14:50:11.826+02', 'Php', false);
INSERT INTO "Domande_Aperte" VALUES ('Matteo', 6, 'http://www.ilpallonaro.com/wp-content/uploads/2012/03/141909333.jpg', 'Quante partite ha giocato Alex Del Piero con la maglia bianconera?', '2015-06-20 14:51:43.755+02', 'Alex', false);
INSERT INTO "Domande_Aperte" VALUES ('Davide', 7, '', 'Cosa mi consigliate di ascoltare durante un lungo viaggio in treno?', '2015-06-20 14:57:44.172+02', '', false);
INSERT INTO "Domande_Aperte" VALUES ('Andrea', 8, '', 'Vorrei iniziare a fare sport, ho 21 anni cosa mi consigliate?', '2015-06-20 15:25:56.283+02', '', false);

INSERT INTO "Appartenenza_Domande" VALUES ('Programmazione', 5);
INSERT INTO "Appartenenza_Domande" VALUES ('Sport', 6);
INSERT INTO "Appartenenza_Domande" VALUES ('Calcio', 6);
INSERT INTO "Appartenenza_Domande" VALUES ('Musica Rock', 7);
INSERT INTO "Appartenenza_Domande" VALUES ('Hard Rock', 7);
INSERT INTO "Appartenenza_Domande" VALUES ('Sport', 8);

INSERT INTO "Sondaggio" VALUES ('Matteo', '2015-06-20 14:53:53.071+02', 2, 'Chi vincerà il campionato di calcio italiano nella stagione 2015/2016?', false, NULL, NULL);
INSERT INTO "Sondaggio" VALUES ('Davide', '2015-06-20 14:59:08.09+02', 3, 'Quali tra questi Maki preferite?', false, NULL, NULL);
INSERT INTO "Sondaggio" VALUES ('Andrea', '2015-06-20 15:28:22.551+02', 4, 'Quali tra questi ristoranti Orientali/Fusion di Milano è il migliore?', false, NULL, NULL);

INSERT INTO "Appartenenza_Sondaggio" VALUES ('Sport', 2);
INSERT INTO "Appartenenza_Sondaggio" VALUES ('Calcio', 2);
INSERT INTO "Appartenenza_Sondaggio" VALUES ('Cucina Orientale', 3);
INSERT INTO "Appartenenza_Sondaggio" VALUES ('Sushi', 3);
INSERT INTO "Appartenenza_Sondaggio" VALUES ('Cucina Orientale', 4);
INSERT INTO "Appartenenza_Sondaggio" VALUES ('Sushi', 4);
INSERT INTO "Appartenenza_Sondaggio" VALUES ('Cinese', 4);

INSERT INTO "Risposte_Aperte" VALUES (5, 22, '2015-06-20 15:00:12.631+02', 'Davide', 'Io uso PHP da anni e non mi trovo male');
INSERT INTO "Risposte_Aperte" VALUES (7, 23, '2015-06-20 15:06:22.7+02', 'SuperMan', 'Io ti consiglio Knockin On Heaven''s Door');
INSERT INTO "Risposte_Aperte" VALUES (6, 24, '2015-06-20 15:11:42.102+02', 'SuperMan', 'Ha giocato ben 513 partite segnando la bellezza di 208 gol!!!');
INSERT INTO "Risposte_Aperte" VALUES (5, 25, '2015-06-20 15:12:15.472+02', 'SuperMan', 'Io userei il phyton!');
INSERT INTO "Risposte_Aperte" VALUES (6, 26, '2015-06-20 15:25:15.891+02', 'Andrea', 'Io sapevo ne avesse fatto 413..');
INSERT INTO "Risposte_Aperte" VALUES (8, 27, '2015-06-20 15:29:46.439+02', 'SuperMan', 'Io ti consiglio uno sport di combattimento, tempra corpo e mente!');
INSERT INTO "Risposte_Aperte" VALUES (6, 28, '2015-06-20 15:30:26.766+02', 'SuperMan', 'Assolutamente, 513 presenze in Campionato Italiano');
INSERT INTO "Risposte_Aperte" VALUES (8, 29, '2015-06-20 15:31:27.419+02', 'SuperMan', 'Ti suggerirei di fare del Taekwondo');

INSERT INTO "Risposte_Sondaggio" VALUES (2, 'Juventus', 5, '2015-06-20 14:53:53.111+02');
INSERT INTO "Risposte_Sondaggio" VALUES (2, 'Milan', 6, '2015-06-20 14:53:53.111+02');
INSERT INTO "Risposte_Sondaggio" VALUES (2, 'Inter', 7, '2015-06-20 14:53:53.111+02');
INSERT INTO "Risposte_Sondaggio" VALUES (2, 'Roma', 8, '2015-06-20 14:53:53.111+02');
INSERT INTO "Risposte_Sondaggio" VALUES (2, 'Napoli', 9, '2015-06-20 14:53:53.111+02');
INSERT INTO "Risposte_Sondaggio" VALUES (3, 'Uramaki TigerRoll', 10, '2015-06-20 14:59:08.142+02');
INSERT INTO "Risposte_Sondaggio" VALUES (3, 'Uramaki RainbowRoll', 11, '2015-06-20 14:59:08.142+02');
INSERT INTO "Risposte_Sondaggio" VALUES (3, 'Uramaki California', 12, '2015-06-20 14:59:08.142+02');
INSERT INTO "Risposte_Sondaggio" VALUES (4, 'Toyama', 13, '2015-06-20 15:28:22.583+02');
INSERT INTO "Risposte_Sondaggio" VALUES (4, 'Osaka', 14, '2015-06-20 15:28:22.583+02');
INSERT INTO "Risposte_Sondaggio" VALUES (4, 'Nikko', 15, '2015-06-20 15:28:22.583+02');
INSERT INTO "Risposte_Sondaggio" VALUES (4, 'Sakura', 16, '2015-06-20 15:28:22.583+02');

INSERT INTO "Votanti" VALUES ('Matteo', 5, 2, false);
INSERT INTO "Votanti" VALUES ('Davide', 10, 3, true);
INSERT INTO "Votanti" VALUES ('SuperMan', 12, 3, false);
INSERT INTO "Votanti" VALUES ('SuperMan', 5, 2, false);
INSERT INTO "Votanti" VALUES ('Andrea', 10, 3, false);
INSERT INTO "Votanti" VALUES ('Andrea', 7, 2, false);
INSERT INTO "Votanti" VALUES ('Andrea', 13, 4, true);
INSERT INTO "Votanti" VALUES ('SuperMan', 16, 4, false);

INSERT INTO "Voto" VALUES ('SuperMan', 22, 1, 0);
INSERT INTO "Voto" VALUES ('SuperMan', 27, 1, 0);





