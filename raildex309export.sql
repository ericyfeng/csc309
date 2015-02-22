--remove old data
SET client_encoding = 'LATIN1';
set search_path=public;
drop table if exists admin cascade;
drop table if exists users cascade;
drop table if exists interests cascade;
drop table if exists personalinterests cascade;
drop table if exists project cascade;
drop table if exists community cascade;
drop table if exists communityendorsement cascade;
drop table if exists communitymember cascade;
drop table if exists initiator cascade;
drop table if exists funder cascade;

----------------------------------------------------------------------
-------------admin table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE admin 
(
    adminusername character varying(40) NOT NULL,
    password character varying(40) NOT NULL
);
ALTER TABLE ONLY admin ADD CONSTRAINT admin_pkey PRIMARY KEY (adminusername);

----------------------------------------------------------------------
-------------users table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE users 
(
    email character varying(40) NOT NULL,
    fname character varying(40) NOT NULL,
    lname character varying(40) NOT NULL,
    password character varying(40) NOT NULL,
    reputation integer NOT NULL,
    profession character varying(40)
);
ALTER TABLE ONLY users ADD CONSTRAINT users_pkey PRIMARY KEY (email);

----------------------------------------------------------------------
-------------interests table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE interests 
(
    interestid integer NOT NULL,
    description character varying(100) NOT NULL
);
CREATE SEQUENCE interests_interestid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY interests ALTER COLUMN interestid SET DEFAULT nextval('interests_interestid_seq'::regclass);
SELECT pg_catalog.setval('interests_interestid_seq', 6, false);
ALTER TABLE ONLY interests ADD CONSTRAINT interests_pkey PRIMARY KEY (interestid);

----------------------------------------------------------------------
-------------personalintersts table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE personalinterests 
(
    email character varying(40) NOT NULL,
    interestid integer NOT NULL
);
ALTER TABLE ONLY personalinterests ADD CONSTRAINT personalinterests_pkey PRIMARY KEY (email, interestid);
ALTER TABLE ONLY personalinterests
    ADD CONSTRAINT personalinterests_email_fkey FOREIGN KEY (email) REFERENCES users(email);
ALTER TABLE ONLY personalinterests
    ADD CONSTRAINT personalinterests_interestid_fkey FOREIGN KEY (interestid) REFERENCES interests(interestid);

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
    location character varying(40) NOT NULL,
    popularity integer NOT NULL,
    rating integer NOT NULL
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
SELECT pg_catalog.setval('community_commid_seq', 2, false);
ALTER TABLE ONLY community ADD CONSTRAINT community_pkey PRIMARY KEY (commid);

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
    ADD CONSTRAINT communityendorsement_projid_fkey FOREIGN KEY (projid) REFERENCES project(projid);

----------------------------------------------------------------------
-------------communitymember table--------------------------------------------
----------------------------------------------------------------------
CREATE TABLE communitymember 
(
    email character varying(40) NOT NULL,
    commid integer NOT NULL
);
ALTER TABLE ONLY communitymember ADD CONSTRAINT communitymember_pkey PRIMARY KEY (email, commid);
ALTER TABLE ONLY communitymember
    ADD CONSTRAINT communitymember_commid_fkey FOREIGN KEY (commid) REFERENCES community(commid);
ALTER TABLE ONLY communitymember
    ADD CONSTRAINT communitymember_email_fkey FOREIGN KEY (email) REFERENCES users(email);

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
    ADD CONSTRAINT initiator_projid_fkey FOREIGN KEY (projid) REFERENCES project(projid);

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
    ADD CONSTRAINT funder_projid_fkey FOREIGN KEY (projid) REFERENCES project(projid);

-----------------------------------------------------------------
-----------------------sample data info--------------------------
--* 3 Main users at the top
--* 2 Interests/main user
--* Everyone belongs to "raildex tv characters" community
--* The community endorses both projects
--* Each project has 1 initator
--* Each project has 2 funders from random other characters seen on the show

--add users as well as other characters from the show as donors
COPY users (email, fname, lname, password, reputation) FROM stdin;
right_hand@raildex.tv	Touma	Kamijou	unlucky	100
libprohibited@raildex.tv	Index	Prohibited	feedme	100
zapper@raildex.tv	Mikoto	Misaka	railgun	100
creepo@raildex.tv	Touya	Kamijou	dad	100
whitehat@raildex.tv	Something	Uiharu	flowers	100
legends@raildex.tv	Ruiko	Saten	superstition	100
\.

COPY interests (interestid, description) FROM stdin;
1	Magic
2	Cat care
3	Gourmet Eating
4	Hacking
5	Geckota
\.

COPY personalinterests (email, interestid) FROM stdin;
right_hand@raildex.tv	1
right_hand@raildex.tv	2
libprohibited@raildex.tv	2
libprohibited@raildex.tv	3
zapper@raildex.tv	4
zapper@raildex.tv	5
\.

COPY project (projid, goalamount, curramount, startdate, enddate, description, location, popularity, rating) FROM stdin;
1	500	20	2015-2-21	2015-06-21	Get a bunk bed for Toumas room	Toumas Appartment	100	100
2	50000	1000	2015-2-21	2016-2-21	Make gigabit wifi available citywide	Academy City	100	100
\.

COPY community (commid, description) FROM stdin;
1	Index and Railgun TV characters
\.

COPY communitymember (email, commid) FROM stdin;
right_hand@raildex.tv	1
libprohibited@raildex.tv	1
zapper@raildex.tv	1
creepo@raildex.tv	1
whitehat@raildex.tv	1
legends@raildex.tv	1
\.

COPY communityendorsement (commid, projid) FROM stdin;
1	1
1	2
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
