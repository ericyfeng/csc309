--remove old data
SET client_encoding = 'LATIN1';
set search_path=public;
drop table if exists users cascade;
drop table if exists personalinterests cascade;
drop table if exists project cascade;
drop table if exists community cascade;
drop table if exists communityendorsement cascade;
drop table if exists initiator cascade;
drop table if exists funder cascade;
drop table if exists rating cascade;
drop table if exists session cascade;
drop table if exists userrating cascade;
drop table if exists comment cascade;
drop table if exists location cascade;
drop table if exists commcomment cascade;
drop table if exists loccomment cascade; 
drop sequence project_projid_seq;
drop sequence community_commid_seq;
drop sequence funder_fundid_seq;
drop sequence rating_rid_seq;
drop sequence userrating_urid_seq;
drop sequence comment_cid_seq;
drop sequence location_locid_seq;
drop sequence commcomment_ccid_seq;
drop sequence loccomment_lcid_seq;

----------------------------------------------------------------------
-------------community table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE community 
(
    commid integer NOT NULL,
    description character varying(100) NOT NULL
);
CREATE SEQUENCE community_commid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY community ALTER COLUMN commid SET DEFAULT nextval('community_commid_seq'::regclass);
SELECT pg_catalog.setval('community_commid_seq', 7, false);
ALTER TABLE ONLY community ADD CONSTRAINT community_pkey PRIMARY KEY (commid);

----------------------------------------------------------------------
-------------location table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE location (
    locid integer NOT NULL,
    locname character varying(100)
);
CREATE SEQUENCE location_locid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY location ALTER COLUMN locid SET DEFAULT nextval('location_locid_seq'::regclass);
SELECT pg_catalog.setval('location_locid_seq', 4, false);
ALTER TABLE ONLY location
    ADD CONSTRAINT location_pkey PRIMARY KEY (locid);

----------------------------------------------------------------------
-------------users table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE users 
(
    email character varying(40) NOT NULL,
    fname character varying(40) NOT NULL,
    lname character varying(40) NOT NULL,
    password character varying(40) NOT NULL,
    reputation double precision NOT NULL,
    profession character varying(40),
	admin integer NOT NULL
);
ALTER TABLE ONLY users ADD CONSTRAINT users_pkey PRIMARY KEY (email);

----------------------------------------------------------------------
-------------location comment table-----------------------------------
----------------------------------------------------------------------
CREATE TABLE loccomment (
    lcid integer NOT NULL,
    locid integer,
    email character varying(40),
    comment character varying(160)
);
CREATE SEQUENCE loccomment_lcid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY loccomment ALTER COLUMN lcid SET DEFAULT nextval('loccomment_lcid_seq'::regclass);
SELECT pg_catalog.setval('loccomment_lcid_seq', 3, false);
ALTER TABLE ONLY loccomment
    ADD CONSTRAINT loccomment_pkey PRIMARY KEY (lcid);
ALTER TABLE ONLY loccomment
    ADD CONSTRAINT loccomment_email_fkey FOREIGN KEY (email) REFERENCES users(email);
ALTER TABLE ONLY loccomment
    ADD CONSTRAINT loccomment_locid_fkey FOREIGN KEY (locid) REFERENCES location(locid);

----------------------------------------------------------------------
-------------community comment table----------------------------------
----------------------------------------------------------------------
CREATE TABLE commcomment (
    ccid integer NOT NULL,
    commid integer,
    email character varying(40),
    comment character varying(160)
);
CREATE SEQUENCE commcomment_ccid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY commcomment ALTER COLUMN ccid SET DEFAULT nextval('commcomment_ccid_seq'::regclass);
SELECT pg_catalog.setval('commcomment_ccid_seq', 3, false);
ALTER TABLE ONLY commcomment
    ADD CONSTRAINT commcomment_pkey PRIMARY KEY (ccid);
ALTER TABLE ONLY commcomment
    ADD CONSTRAINT commcomment_commid_fkey FOREIGN KEY (commid) REFERENCES community(commid);
ALTER TABLE ONLY commcomment
    ADD CONSTRAINT commcomment_email_fkey FOREIGN KEY (email) REFERENCES users(email);

----------------------------------------------------------------------
-------------project table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE project 
(
    projid integer NOT NULL,
    goalamount integer NOT NULL,
    curramount integer NOT NULL,
    startdate date NOT NULL,
    enddate date NOT NULL,
    description character varying(100),
    locid integer NOT NULL,
    popularity integer NOT NULL,
    rating double precision NOT NULL,
	longdesc character varying(1000),
	video character varying (160),
	picture character varying (500)
);
CREATE SEQUENCE project_projid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY project ALTER COLUMN projid SET DEFAULT nextval('project_projid_seq'::regclass);
SELECT pg_catalog.setval('project_projid_seq', 3, false);
ALTER TABLE ONLY project ADD CONSTRAINT project_pkey PRIMARY KEY (projid);
ALTER TABLE ONLY project
    ADD CONSTRAINT project_locid_fkey FOREIGN KEY (locid) REFERENCES location(locid);

----------------------------------------------------------------------
-------------personalintersts table-----------------------------------
----------------------------------------------------------------------
CREATE TABLE personalinterests 
(
    email character varying(40) NOT NULL,
    commid integer NOT NULL
);
ALTER TABLE ONLY personalinterests ADD CONSTRAINT personalinterests_pkey PRIMARY KEY (email, commid);
ALTER TABLE ONLY personalinterests
    ADD CONSTRAINT personalinterests_email_fkey FOREIGN KEY (email) REFERENCES users(email);
ALTER TABLE ONLY personalinterests
    ADD CONSTRAINT personalinterests_commid_fkey FOREIGN KEY (commid) REFERENCES community(commid);

----------------------------------------------------------------------
-------------communityendorsement table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE communityendorsement 
(
    commid integer NOT NULL,
    projid integer NOT NULL
);
ALTER TABLE ONLY communityendorsement ADD CONSTRAINT communityendorsement_pkey PRIMARY KEY (projid, commid);
ALTER TABLE ONLY communityendorsement
    ADD CONSTRAINT communityendorsement_commid_fkey FOREIGN KEY (commid) REFERENCES community(commid);
ALTER TABLE ONLY communityendorsement
    ADD CONSTRAINT communityendorsement_projid_fkey FOREIGN KEY (projid) REFERENCES project(projid) ON DELETE CASCADE;

----------------------------------------------------------------------
-------------initiator table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE initiator 
(
    projid integer NOT NULL,
    email character varying(40) NOT NULL
);
ALTER TABLE ONLY initiator ADD CONSTRAINT initiator_pkey PRIMARY KEY (projid, email);
ALTER TABLE ONLY initiator
    ADD CONSTRAINT initiator_email_fkey FOREIGN KEY (email) REFERENCES users(email);
ALTER TABLE ONLY initiator
    ADD CONSTRAINT initiator_projid_fkey FOREIGN KEY (projid) REFERENCES project(projid) ON DELETE CASCADE;

----------------------------------------------------------------------
-------------funder table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE funder 
(
    fundid integer NOT NULL,
    email character varying(40) NOT NULL,
    projid integer NOT NULL,
    datestamp date NOT NULL,
    amount integer NOT NULL
);
CREATE SEQUENCE funder_fundid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY funder ALTER COLUMN fundid SET DEFAULT nextval('funder_fundid_seq'::regclass);
SELECT pg_catalog.setval('funder_fundid_seq', 5, false);
ALTER TABLE ONLY funder ADD CONSTRAINT funder_pkey PRIMARY KEY (fundid);
ALTER TABLE ONLY funder
    ADD CONSTRAINT funder_email_fkey FOREIGN KEY (email) REFERENCES users(email);
ALTER TABLE ONLY funder
    ADD CONSTRAINT funder_projid_fkey FOREIGN KEY (projid) REFERENCES project(projid) ON DELETE CASCADE;

----------------------------------------------------------------------
-------------ratings table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE rating (
    rid integer NOT NULL,
    projid integer NOT NULL,
    email character varying(40),
	rating integer NOT NULL
);
CREATE SEQUENCE rating_rid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY rating ALTER COLUMN rid SET DEFAULT nextval('rating_rid_seq'::regclass);
SELECT pg_catalog.setval('rating_rid_seq', 2, false);
ALTER TABLE ONLY rating
    ADD CONSTRAINT rating_pkey PRIMARY KEY (rid);
ALTER TABLE ONLY rating
    ADD CONSTRAINT rating_email_fkey FOREIGN KEY (email) REFERENCES users(email);
ALTER TABLE ONLY rating
    ADD CONSTRAINT rating_projid_fkey FOREIGN KEY (projid) REFERENCES project(projid) ON DELETE CASCADE;

----------------------------------------------------------------------
-------------session information table--------------------------------
----------------------------------------------------------------------
CREATE TABLE session (
    email character varying(40) NOT NULL,
    sessionid integer,
    expiration timestamp without time zone
);
ALTER TABLE ONLY session
    ADD CONSTRAINT session_pkey PRIMARY KEY (email);
ALTER TABLE ONLY session
    ADD CONSTRAINT session_email_fkey FOREIGN KEY (email) REFERENCES users(email);

----------------------------------------------------------------------
---------------------user rating table--------------------------------
----------------------------------------------------------------------
CREATE TABLE userrating (
    urid integer NOT NULL,
    rater character varying(40) NOT NULL,
    ratee character varying(40) NOT NULL,
    urating integer NOT NULL
);
CREATE SEQUENCE userrating_urid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY userrating ALTER COLUMN urid SET DEFAULT nextval('userrating_urid_seq'::regclass);
SELECT pg_catalog.setval('userrating_urid_seq', 1, false);
ALTER TABLE ONLY userrating
    ADD CONSTRAINT userrating_pkey PRIMARY KEY (urid);
ALTER TABLE ONLY userrating
    ADD CONSTRAINT userrating_ratee_fkey FOREIGN KEY (ratee) REFERENCES users(email);
ALTER TABLE ONLY userrating
    ADD CONSTRAINT userrating_rater_fkey FOREIGN KEY (rater) REFERENCES users(email);

----------------------------------------------------------------------
------------------------comments table--------------------------------
----------------------------------------------------------------------
CREATE TABLE comment (
    cid integer NOT NULL,
    projid integer NOT NULL,
    email character varying(40) NOT NULL,
    comment character varying(300) NOT NULL
);
CREATE SEQUENCE comment_cid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY comment ALTER COLUMN cid SET DEFAULT nextval('comment_cid_seq'::regclass);
SELECT pg_catalog.setval('comment_cid_seq', 4, false);
ALTER TABLE ONLY comment
    ADD CONSTRAINT comment_pkey PRIMARY KEY (cid);
ALTER TABLE ONLY comment
    ADD CONSTRAINT comment_email_fkey FOREIGN KEY (email) REFERENCES users(email);
ALTER TABLE ONLY comment
    ADD CONSTRAINT comment_projid_fkey FOREIGN KEY (projid) REFERENCES project(projid) ON DELETE CASCADE;

-----------------------------------------------------------------
-----------------------sample data info--------------   ------------
--* 3 Main users at the top
--* 2 Interests/main user
--* Everyone belongs to "raildex tv characters" community
--* The community endorses both projects
--* Each project has 1 initator
--* Each project has 2 funders from random other characters seen on the show

--add users as well as other characters from the show as donors
COPY users (email, fname, lname, password, reputation, admin) FROM stdin;
right_hand@raildex.tv	Touma	Kamijou	unlucky	0	0
libprohibited@raildex.tv	Index	Prohibited	feedme	0	0
zapper@raildex.tv	Mikoto	Misaka	railgun	0	0
creepo@raildex.tv	Touya	Kamijou	dad	0	0
whitehat@raildex.tv	Something	Uiharu	flowers	0	0
legends@raildex.tv	Ruiko	Saten	superstition	0	0
root@admin.com	Absolute	Authority	toor	1000	1
\.

COPY location (locid, locname) FROM stdin;
1	Academy City
2	Ontario
3	Quebec
\.

COPY community (commid, description) FROM stdin;
1	Magic
2	Cat care
3	Gourmet Eating
4	Technology
5	Education
6	Interior Design
\.

COPY personalinterests (email, commid) FROM stdin;
right_hand@raildex.tv	1
right_hand@raildex.tv	2
libprohibited@raildex.tv	2
libprohibited@raildex.tv	3
zapper@raildex.tv	4
\.

COPY project (projid, goalamount, curramount, startdate, enddate, description, locid, popularity, rating, longdesc, video, picture) FROM stdin;
1	500	70	2015-2-21	2015-06-21	Get a bunk bed for Toumas room	1	100	0	Please help me so that I wont have to sleep in the bathtub every night. For obvious reasons Index doesnt want to sleep in the same bed with me so I was hoping to get a bunk bed where I can sleep on the bottom row. That way Index wont have to worry about personal safety and I wont have to wake up sore every morning.	https://www.youtube.com/embed/uqEwvVIeFr4	http://www.ikea.com/ms/media/roomsettings/20141/bedroom/20141_bero09a/20141_bero09a_01_PE374761.jpg
2	50000	10000	2015-2-21	2016-2-21	Make gigabit wifi available citywide	1	100	0	Imagine the convenience of having internet access wherever you go. All the worlds information at your fingertips. No more need to go to internet cafes or telephone booths. This will be especially andy for those who like to say up late and dont want to always be seen alone in a phone both.	https://www.youtube.com/embed/vX7wobS48YA	http://images.atelier.net/sites/default/files/imagecache/scale_crop_587_310/articles/414945/atelier-wifi-signal.png
\.


COPY communityendorsement (commid, projid) FROM stdin;
6	1
4	2
\.

COPY initiator (projid, email) FROM stdin;
1	right_hand@raildex.tv
2	zapper@raildex.tv
\.

--the large donation for project 2 was from the extra money cards
COPY funder (fundid, email, projid, datestamp, amount) FROM stdin;
1	libprohibited@raildex.tv	1	2015-2-26	5
2	creepo@raildex.tv	1	2015-2-22	15
3	whitehat@raildex.tv	2	2015-2-25	20
4	legends@raildex.tv	2	2015-3-1	980
\.

COPY rating (rid, projid, email, rating) FROM stdin;
1	2	whitehat@raildex.tv	10
\.

COPY session (email, expiration) FROM stdin;
right_hand@raildex.tv	2020-03-06 19:03:17.433082-05
libprohibited@raildex.tv	2000-03-06 19:03:17.433082-05
zapper@raildex.tv	2000-03-06 19:03:17.433082-05
creepo@raildex.tv	2000-03-06 19:03:17.433082-05
whitehat@raildex.tv	2000-03-06 19:03:17.433082-05
legends@raildex.tv	2000-03-06 19:03:17.433082-05
root@admin.com	2099-03-06 19:03:17.433082-05
\.

copy comment (cid, projid, email, comment) from stdin;
1	1	creepo@raildex.tv	It is nice to see my child being so considerate.
2	2	whitehat@raildex.tv	I would really appreciate not having to sync offline data all the time.
3	2	legends@raildex.tv	I wont have to waste LTE data anymore looking up more urban legends.
\.

copy commcomment (ccid, commid, email, comment) from stdin;
1	1	libprohibited@raildex.tv	I know all the secrets there is to magic.
2	1	right_hand@raildex.tv	Luckily for me I can negate anything funny you try on me :-p
\.

copy loccomment (lcid, locid, email, comment) from stdin;
1	1	right_hand@raildex.tv	So much bad shit happens here. Who the hell really runs this place?
2	1	zapper@raildex.tv	I know right? There are some pretty sick minds behind the scenes.
\.
