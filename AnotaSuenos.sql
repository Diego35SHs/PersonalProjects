Create Database Anotasuenos;
Use Anotasuenos;

/* Dropear esta tabla y volverla a crear. */
Create table Login(
cod_usu int(9) auto_increment,
nom_usu varchar(50) Unique,
pas_usu varchar(255),
cor_usu varchar(100) Unique,
fen_usu date,
fec_usu datetime not null,
fot_usu longblob,
niv_usu int(9),
Constraint PkCodusuLogin Primary Key(cod_usu)
);
Select * From Login;

Create Table Sueno(
id_sue int(9) auto_increment unique,
sueno varchar(500),
sue_pri int, /* 1 -> True -- 2 -> False*/
sue_m18 int, /* 1 -> True -- 2 -> False*/
fec_sue datetime,
cod_usu int(9),
Constraint PkId_sueSueno Primary Key(id_sue),
Constraint FkCod_usuSueno Foreign Key(cod_usu) References Login(cod_usu)
);

Create Table Comentario(
id_com int(9) auto_increment unique,
id_sue int(9),
id_usu int(9),
comentario varchar(500),
Constraint PkId_comComentario Primary Key(id_com),
Constraint FKId_sueComentario Foreign Key(id_sue) References Sueno(id_sue)
);

Insert into Comentario(id_sue,id_usu,comentario) Values(52,1,'AGGA');

Create Table LikeDislike(
id_dlk int(9) auto_increment unique,
id_sue int(9),
id_usu int(9),
Constraint PkId_dlkLikeDislike Primary Key(id_dlk),
Constraint FkId_sueLikeDislike Foreign Key(id_sue) References Sueno(id_sue),
Constraint FkId_usuLikeDislike Foreign Key(id_usu) References Login(id_usu)
);

Insert into LikeDislike(id_sue,id_usu) Values(52,2);

Drop Table LikeDislike;
Select * From Sueno;
Select * From Login;
SELECT sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM sueno WHERE sue_pri = 0 AND sue_m18 = 0;


