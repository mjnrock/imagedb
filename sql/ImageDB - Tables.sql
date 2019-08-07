USE FuzzyKnights
GO

--	================================= NOTES =================================
--	* "Image Stacks" (prev. Frames) are now configured by the Track/Sequence
--	  templates, --	  so the "sandwich" is made by the Scene/Camera process,
--	  not the Frame
--	* << NOTE >>
--	  |
--	=========================================================================

--	CREATE SCHEMA ImageDB
--	GO

IF OBJECT_ID('ImageDB.Scene') IS NOT NULL DROP TABLE ImageDB.Scene;
IF OBJECT_ID('ImageDB.Camera') IS NOT NULL DROP TABLE ImageDB.Camera;

IF OBJECT_ID('ImageDB.Animation') IS NOT NULL DROP TABLE ImageDB.Animation;

IF OBJECT_ID('ImageDB.Frame') IS NOT NULL DROP TABLE ImageDB.Frame;
IF OBJECT_ID('ImageDB.Track') IS NOT NULL DROP TABLE ImageDB.Track;
IF OBJECT_ID('ImageDB.[Sequence]') IS NOT NULL DROP TABLE ImageDB.[Sequence];

IF OBJECT_ID('ImageDB.ImageECategory') IS NOT NULL DROP TABLE ImageDB.ImageECategory;
IF OBJECT_ID('ImageDB.[Image]') IS NOT NULL DROP TABLE ImageDB.[Image];

IF OBJECT_ID('ImageDB.ECategory') IS NOT NULL DROP TABLE ImageDB.ECategory;
IF OBJECT_ID('ImageDB.ETrack') IS NOT NULL DROP TABLE ImageDB.ETrack;
IF OBJECT_ID('ImageDB.ESequence') IS NOT NULL DROP TABLE ImageDB.ESequence;
IF OBJECT_ID('ImageDB.EAnimation') IS NOT NULL DROP TABLE ImageDB.EAnimation;
GO

CREATE TABLE ImageDB.ESequence (
	ESequenceID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	
	Name VARCHAR(255) NOT NULL UNIQUE,
	[Description] NVARCHAR(MAX) NULL,
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);
INSERT INTO ImageDB.ESequence (Name)
VALUES
	('ENTITY'),
	('EFFECT');

CREATE TABLE ImageDB.ETrack (
	ETrackID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	ESequenceID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.ESequence (ESequenceID),
	
	Name VARCHAR(255) NOT NULL UNIQUE,
	[Description] NVARCHAR(MAX) NULL,
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);
DECLARE @ESequenceENTITY INT = (SELECT ESequenceID FROM ImageDB.ESequence WHERE Name = 'ENTITY');
INSERT INTO ImageDB.ETrack (ESequenceID, Name)
VALUES
	(@ESequenceENTITY, 'BODY'),
	(@ESequenceENTITY, 'EYES'),
	(@ESequenceENTITY, 'HAND_RIGHT'),
	(@ESequenceENTITY, 'HAND_LEFT'),
	(@ESequenceENTITY, 'HEAD');

--	This is to be used as the mapper to allow for templatizing the animations
--	(e.g. Raccoon, Body, Entity > Raccoon, etc.)
CREATE TABLE ImageDB.ECategory (
	ECategoryID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	
	Name VARCHAR(255) NULL,
	[Description] NVARCHAR(MAX) NULL,
	ParentECategoryID INT NULL FOREIGN KEY REFERENCES ImageDB.ECategory (ECategoryID),	-- Allow for hierarchy relationships, as necessary
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);
INSERT INTO ImageDB.ECategory (Name, ParentECategoryID)
VALUES
	('FuzzyKnights::Paco', NULL),
	('Entity', 1),
	('Raccoon', 2),
	('Rabbit', 2);	-- etc.

CREATE TABLE ImageDB.EAnimation (
	EAnimationID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	
	Name VARCHAR(255) NOT NULL UNIQUE,
	[Description] NVARCHAR(MAX) NULL,
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);
INSERT INTO ImageDB.EAnimation (Name)
VALUES
	('WEIGHTED_POOL'),
	('SEQUENTIAL');

	

CREATE TABLE ImageDB.[Sequence] (
	SequenceID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	ESequenceID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.ESequence (ESequenceID),
	
	Name VARCHAR(MAX) NOT NULL,
	[Description] NVARCHAR(MAX) NULL,
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);

CREATE TABLE ImageDB.Track (
	TrackID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	SequenceID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.[Sequence] (SequenceID),
	ETrackID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.ETrack (ETrackID),
	
	[Description] NVARCHAR(MAX) NULL,
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);

--	By adding "ECategory", there allows for a mapping table between Images and ECategory
--	so that these can be templates and the query can subtitute between various images
--	(e.g. Raccoon IDLE becomes just "IDLE", and any ESequence=ENTITY can be pulled)
CREATE TABLE ImageDB.Frame (
	FrameID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	TrackID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.Track (TrackID),
	ECategoryID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.ECategory (ECategoryID),

	Duration REAL NULL,
	Ordinality TINYINT NULL,
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);

CREATE TABLE ImageDB.Camera (
	CameraID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	
	Name VARCHAR(255) NULL,
	X REAL NULL DEFAULT 0,
	Y REAL NULL DEFAULT 0,
	Z REAL NULL DEFAULT 0,
	Pitch REAL NULL DEFAULT 0,
	Yaw REAL NULL DEFAULT 0,
	Roll REAL NULL DEFAULT 0,
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);
INSERT INTO ImageDB.Camera (Name, Yaw, Tags)
VALUES
	('NORTH', 0, '2D,yaw'),
	('NORTH_EAST', 45, '2D,yaw'),
	('EAST', 90, '2D,yaw'),
	('SOUTH_EAST', 135, '2D,yaw'),
	('SOUTH', 180, '2D,yaw'),
	('SOUTH_WEST', 225, '2D,yaw'),
	('WEST', 270, '2D,yaw'),
	('NORTH_WEST', 315, '2D,yaw');

CREATE TABLE ImageDB.Scene (
	SceneID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	SequenceID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.[Sequence] (SequenceID),
	CameraID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.Camera (CameraID),
	TrackID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.Track (TrackID),
	
	ZIndex TINYINT NULL,
	IsRequired BIT NULL DEFAULT 1,
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);



CREATE TABLE ImageDB.[Image] (
	ImageID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,

	[Base64] VARBINARY(MAX) NOT NULL,
	Width REAL NULL,
	Height REAL NULL,
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);

CREATE TABLE ImageDB.ImageECategory (
	MappingID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,	
	ECategoryID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.ECategory (ECategoryID),
	ImageID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.[Image] (ImageID),

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);

CREATE TABLE ImageDB.Animation (
	AnimationID INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	EAnimationID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.EAnimation (EAnimationID),
	SequenceID INT NOT NULL FOREIGN KEY REFERENCES ImageDB.[Sequence] (SequenceID),
	
	Name VARCHAR(255) NULL,
	[Description] NVARCHAR(MAX) NULL,
	Value REAL NULL,		-- WEIGHTED_POOL: The chance of proccing, SEQUENTIAL: The ordinality, etc.
	Tags NVARCHAR(255) NULL,

	UUID UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID(),
);