--	======================================
--	PURPOSE: This TVF recursively adds the
--	  parent category into a dot-syntax
--	  naming (e.g. Root.Child.GrandChild)
--	======================================
IF OBJECT_ID('ImageDB.tvfECategory') IS NOT NULL DROP FUNCTION ImageDB.tvfECategory;
GO

CREATE FUNCTION ImageDB.tvfECategory ()
RETURNS @ECategory TABLE (
	ECategoryID INT,
	Name VARCHAR(255),
	[Description] NVARCHAR(MAX),
	CategoryEndpoint VARCHAR(255),
	ParentECategoryID INT,
	Tags VARCHAR(255),
	UUID UNIQUEIDENTIFIER
)
AS
BEGIN
	WITH cte AS (
		SELECT
			c.ECategoryID,
			c.Name,
			c.[Description],
			CAST(c.Name AS VARCHAR(MAX)) AS CategoryEndpoint,
			c.ParentECategoryID,
			c.Tags,
			c.UUID
		FROM
			ImageDB.ECategory c
		WHERE
			c.ParentECategoryID IS NULL

		UNION ALL

		SELECT
			c.ECategoryID,
			c.Name,
			c.[Description],
			CAST(CONCAT(cte.CategoryEndpoint, '.', c.Name) AS VARCHAR(MAX)) AS CategoryEndpoint,
			c.ParentECategoryID,
			c.Tags,
			c.UUID
		FROM
			cte
			INNER JOIN ImageDB.ECategory c
				ON cte.ECategoryID = c.ParentECategoryID
	)

	INSERT INTO @ECategory
	SELECT
		*
	FROM
		cte;

	RETURN;
END
GO

--	=======================================
--	PURPOSE: To call ImageDB.tvfECategory
--	  without having to use parameters
--	=======================================
--	CREATE VIEW ImageDB.vwECategory AS
--	SELECT
--		*
--	FROM
--		ImageDB.tvfECategory()


IF OBJECT_ID('ImageDB.MergeImage') IS NOT NULL DROP PROCEDURE ImageDB.MergeImage;
GO

CREATE PROCEDURE ImageDB.MergeImage
	@FilePath VARCHAR(255),
	@FileName VARCHAR(255),
	@FileExtension VARCHAR(255),
	@Width NVARCHAR(255) = NULL,
	@Height NVARCHAR(255) = NULL,
	@Tags NVARCHAR(255) = NULL,
	@UUID NVARCHAR(255) = NULL
AS
BEGIN
	SET NOCOUNT ON;

    MERGE INTO ImageDB.[Image] AS t
	USING (
		SELECT
			@FilePath,
			@FileName,
			@FileExtension,
			@UUID
	) as s (FilePath, [FileName], FileExtension, UUID)
		ON (
			t.FilePath = s.FilePath
			AND t.[FileName] = s.[FileName]
			AND t.FileExtension = s.FileExtension
		)
		OR
		CAST(t.UUID AS NVARCHAR(255)) = s.UUID
	WHEN MATCHED THEN
		UPDATE SET
			t.Tags = CASE
				WHEN @Tags = 'NULL' THEN NULL
				WHEN t.Tags IS NULL AND @Tags IS NULL THEN NULL
				WHEN t.Tags IS NULL AND @Tags IS NOT NULL THEN CAST(@Tags AS NVARCHAR(255))
				WHEN t.Tags IS NOT NULL AND @Tags IS NULL THEN CAST(t.Tags AS NVARCHAR(255))
				WHEN t.Tags IS NOT NULL AND @Tags IS NOT NULL THEN CAST(@Tags AS NVARCHAR(255))
				ELSE NULL
			END
	WHEN NOT MATCHED THEN
		INSERT (
			FilePath,
			[FileName],
			FileExtension,
			Width,
			Height,
			Tags
		) VALUES (
			@FilePath,
			@FileName,
			@FileExtension,
			@Width,
			@Height,
			CAST(CASE
				WHEN @Tags = 'NULL' THEN NULL
				ELSE @Tags
			END AS NVARCHAR(255))
		)
	OUTPUT
		$action,
		Inserted.*;
END
GO

USE FuzzyKnights
GO

IF OBJECT_ID('ImageDB.MergeEAnimation') IS NOT NULL DROP PROCEDURE ImageDB.MergeEAnimation;
IF OBJECT_ID('ImageDB.MergeESequence') IS NOT NULL DROP PROCEDURE ImageDB.MergeESequence;
IF OBJECT_ID('ImageDB.MergeETrack') IS NOT NULL DROP PROCEDURE ImageDB.MergeETrack;
IF OBJECT_ID('ImageDB.MergeECategory') IS NOT NULL DROP PROCEDURE ImageDB.MergeECategory;
IF OBJECT_ID('ImageDB.UpdateTags') IS NOT NULL DROP PROCEDURE ImageDB.UpdateTags;
GO

CREATE PROCEDURE ImageDB.MergeEAnimation
	@Name VARCHAR(255),
	@Description NVARCHAR(MAX)
AS
BEGIN
	SET NOCOUNT ON;

    MERGE INTO ImageDB.EAnimation AS t
	USING (
		SELECT
			@Name
	) as s (Name)
		ON (
			t.Name = s.Name
		)
	WHEN MATCHED THEN
		UPDATE SET
			[Description] = @Description
	WHEN NOT MATCHED THEN
		INSERT (Name, [Description])
		VALUES
			(@Name, @Description)
	OUTPUT
		$action,
		Inserted.*;
			
END
GO

CREATE PROCEDURE ImageDB.MergeESequence
	@Name VARCHAR(255),
	@Description NVARCHAR(MAX)
AS
BEGIN
	SET NOCOUNT ON;

    MERGE INTO ImageDB.ESequence AS t
	USING (
		SELECT
			@Name
	) as s (Name)
		ON (
			t.Name = s.Name
		)
	WHEN MATCHED THEN
		UPDATE SET
			[Description] = @Description
	WHEN NOT MATCHED THEN
		INSERT (Name, [Description])
		VALUES
			(@Name, @Description)
	OUTPUT
		$action,
		Inserted.*;
			
END
GO

CREATE PROCEDURE ImageDB.MergeECategory
	@Name VARCHAR(255),
	@Description NVARCHAR(MAX),
	@ParentECategoryID INT = NULL
AS
BEGIN
	SET NOCOUNT ON;

    MERGE INTO ImageDB.ECategory AS t
	USING (
		SELECT
			@Name
	) as s (Name)
		ON (
			t.Name = s.Name
		)
	WHEN MATCHED THEN
		UPDATE SET
			[Description] = @Description,
			ParentECategoryID = CASE
				WHEN @ParentECategoryID = 'NULL' THEN NULL
				WHEN ParentECategoryID IS NULL AND @ParentECategoryID IS NULL THEN NULL
				WHEN ParentECategoryID IS NULL AND @ParentECategoryID IS NOT NULL THEN @ParentECategoryID
				WHEN ParentECategoryID IS NOT NULL AND @ParentECategoryID IS NULL THEN ParentECategoryID
				WHEN ParentECategoryID IS NOT NULL AND @ParentECategoryID IS NOT NULL THEN @ParentECategoryID
			END
	WHEN NOT MATCHED THEN
		INSERT (Name, [Description], ParentECategoryID)
		VALUES
			(@Name, @Description, @ParentECategoryID)
	OUTPUT
		$action,
		Inserted.*;
			
END
GO

CREATE PROCEDURE ImageDB.MergeETrack
	@Name VARCHAR(255),
	@Description NVARCHAR(MAX),
	@ESequenceID INT
AS
BEGIN
	SET NOCOUNT ON;

    MERGE INTO ImageDB.ETrack AS t
	USING (
		SELECT
			@Name
	) as s (Name)
		ON (
			t.Name = s.Name
		)
	WHEN MATCHED THEN
		UPDATE SET
			[Description] = @Description,
			ESequenceID = @ESequenceID
	WHEN NOT MATCHED THEN
		INSERT (Name, [Description], ESequenceID)
		VALUES
			(@Name, @Description, @ESequenceID)
	OUTPUT
		$action,
		Inserted.*;
			
END
GO

CREATE PROCEDURE ImageDB.UpdateTags
	@TableName VARCHAR(255),
	@UUID VARCHAR(255),
	@Tags VARCHAR(255) = NULL,
	@Flag TINYINT = 0
AS
BEGIN
	CREATE TABLE #TagTable (UUID VARCHAR(255), Tags VARCHAR(255));

	INSERT INTO #TagTable (UUID, Tags)
	VALUES
		(@UUID, @Tags);

	DECLARE @SQL NVARCHAR(MAX) = '
		UPDATE s
		SET
			s.Tags = CASE
				WHEN t.Tags = ''NULL'' THEN NULL
				WHEN s.Tags IS NULL AND t.Tags IS NULL THEN NULL
				WHEN s.Tags IS NULL AND t.Tags IS NOT NULL THEN CAST(t.Tags AS NVARCHAR(255))
				WHEN s.Tags IS NOT NULL AND t.Tags IS NULL THEN CAST(s.Tags AS NVARCHAR(255))
				WHEN s.Tags IS NOT NULL AND t.Tags IS NOT NULL THEN CAST(t.Tags AS NVARCHAR(255))
			END
		FROM
			ImageDB.[' + @TableName + '] s
			INNER JOIN #TagTable t
				ON CAST(s.UUID AS VARCHAR(255)) = t.UUID';

	EXEC(@SQL);

	DROP TABLE #TagTable;
END
GO


IF OBJECT_ID('ImageDB.CreateModelTVFs') IS NOT NULL DROP PROCEDURE ImageDB.CreateModelTVFs;
GO

CREATE PROCEDURE ImageDB.CreateModelTVFs
AS
BEGIN
	SET NOCOUNT ON;

	DECLARE @Table TABLE (i INT IDENTITY(1,1), Name VARCHAR(255), PK VARCHAR(255));

	INSERT INTO @Table (Name, PK)
	SELECT
		t.TABLE_NAME,
		c.COLUMN_NAME
	FROM
		INFORMATION_SCHEMA.TABLES t
		INNER JOIN INFORMATION_SCHEMA.COLUMNS c
			ON c.TABLE_SCHEMA = t.TABLE_SCHEMA
			AND c.TABLE_NAME = t.TABLE_NAME
			AND c.ORDINAL_POSITION = 1
	WHERE
		t.TABLE_SCHEMA = 'ImageDB'

	DECLARE @i INT = 1;

	WHILE @i <= (SELECT COUNT(*) FROM @Table)
		BEGIN
			DECLARE @TableName VARCHAR(255) = (SELECT Name FROM @Table WHERE i = @i);
			DECLARE @PK VARCHAR(255) = (SELECT PK FROM @Table WHERE i = @i);
			DECLARE @SQL NVARCHAR(MAX) = 'IF OBJECT_ID(''ImageDB.Get' + @TableName + ''') IS NOT NULL DROP FUNCTION ImageDB.[Get' + @TableName + '];';

			EXEC(@SQL);

SET @SQL = 'CREATE FUNCTION ImageDB.[Get' + @TableName + ']
(	
	@Input VARCHAR(255)
)
RETURNS TABLE 
AS
RETURN 
(
	SELECT
		*
	FROM
		ImageDB.[' + @TableName + '] t WITH (NOLOCK)
	WHERE
		(
			CAST(t.[' + @PK + '] AS VARCHAR(255)) = @Input
		)
		OR
		(
			CAST(t.UUID AS VARCHAR(255)) = @Input
		)
);
';

			EXEC(@SQL);

			SET @i = @i + 1;
		END
END
GO






CREATE PROCEDURE ImageDB.[CRUD:Camera]
	@Flag TINYINT = 0,
	@CameraID INT = NULL,	@Name VARCHAR(255) = NULL,	@X REAL = NULL,	@Y REAL = NULL,	@Z REAL = NULL,	@Pitch REAL = NULL,	@Yaw REAL = NULL,	@Roll REAL = NULL,	@Tags NVARCHAR(510) = NULL,	@UUID UNIQUEIDENTIFIER = NULL
AS
BEGIN

IF @Flag = 1
	BEGIN
		SELECT
			*
		FROM
			ImageDB.[Camera]
		WHERE			(
				(
					CameraID IS NOT NULL
					AND @CameraID IS NOT NULL
					AND CameraID = @CameraID
				)
				OR
				(
					CameraID IS NULL
					AND @CameraID IS NULL
				)
			)			AND			(
				(
					Name IS NOT NULL
					AND @Name IS NOT NULL
					AND Name = @Name
				)
				OR
				(
					Name IS NULL
					AND @Name IS NULL
				)
			)			AND			(
				(
					X IS NOT NULL
					AND @X IS NOT NULL
					AND X = @X
				)
				OR
				(
					X IS NULL
					AND @X IS NULL
				)
			)			AND			(
				(
					Y IS NOT NULL
					AND @Y IS NOT NULL
					AND Y = @Y
				)
				OR
				(
					Y IS NULL
					AND @Y IS NULL
				)
			)			AND			(
				(
					Z IS NOT NULL
					AND @Z IS NOT NULL
					AND Z = @Z
				)
				OR
				(
					Z IS NULL
					AND @Z IS NULL
				)
			)			AND			(
				(
					Pitch IS NOT NULL
					AND @Pitch IS NOT NULL
					AND Pitch = @Pitch
				)
				OR
				(
					Pitch IS NULL
					AND @Pitch IS NULL
				)
			)			AND			(
				(
					Yaw IS NOT NULL
					AND @Yaw IS NOT NULL
					AND Yaw = @Yaw
				)
				OR
				(
					Yaw IS NULL
					AND @Yaw IS NULL
				)
			)			AND			(
				(
					Roll IS NOT NULL
					AND @Roll IS NOT NULL
					AND Roll = @Roll
				)
				OR
				(
					Roll IS NULL
					AND @Roll IS NULL
				)
			)			AND			(
				(
					Tags IS NOT NULL
					AND @Tags IS NOT NULL
					AND Tags = @Tags
				)
				OR
				(
					Tags IS NULL
					AND @Tags IS NULL
				)
			)			AND			(
				(
					UUID IS NOT NULL
					AND @UUID IS NOT NULL
					AND UUID = @UUID
				)
				OR
				(
					UUID IS NULL
					AND @UUID IS NULL
				)
			)	END
IF @Flag = 2
	BEGIN
		UPDATE ImageDB.[Camera]
		SET
			Name = CASE
				WHEN @Name = 'NULL' THEN NULL
				WHEN Name IS NULL AND @Name IS NULL THEN NULL
				WHEN Name IS NULL AND @Name IS NOT NULL THEN CAST(@Name AS VARCHAR(255))
				WHEN Name IS NOT NULL AND @Name IS NULL THEN CAST(Name AS VARCHAR(255))
				WHEN Name IS NOT NULL AND @Name IS NOT NULL THEN CAST(@Name AS VARCHAR(255))
				ELSE NULL
			END,			X = CASE
				WHEN @X = 'NULL' THEN NULL
				WHEN X IS NULL AND @X IS NULL THEN NULL
				WHEN X IS NULL AND @X IS NOT NULL THEN CAST(@X AS REAL)
				WHEN X IS NOT NULL AND @X IS NULL THEN CAST(X AS REAL)
				WHEN X IS NOT NULL AND @X IS NOT NULL THEN CAST(@X AS REAL)
				ELSE NULL
			END,			Y = CASE
				WHEN @Y = 'NULL' THEN NULL
				WHEN Y IS NULL AND @Y IS NULL THEN NULL
				WHEN Y IS NULL AND @Y IS NOT NULL THEN CAST(@Y AS REAL)
				WHEN Y IS NOT NULL AND @Y IS NULL THEN CAST(Y AS REAL)
				WHEN Y IS NOT NULL AND @Y IS NOT NULL THEN CAST(@Y AS REAL)
				ELSE NULL
			END,			Z = CASE
				WHEN @Z = 'NULL' THEN NULL
				WHEN Z IS NULL AND @Z IS NULL THEN NULL
				WHEN Z IS NULL AND @Z IS NOT NULL THEN CAST(@Z AS REAL)
				WHEN Z IS NOT NULL AND @Z IS NULL THEN CAST(Z AS REAL)
				WHEN Z IS NOT NULL AND @Z IS NOT NULL THEN CAST(@Z AS REAL)
				ELSE NULL
			END,			Pitch = CASE
				WHEN @Pitch = 'NULL' THEN NULL
				WHEN Pitch IS NULL AND @Pitch IS NULL THEN NULL
				WHEN Pitch IS NULL AND @Pitch IS NOT NULL THEN CAST(@Pitch AS REAL)
				WHEN Pitch IS NOT NULL AND @Pitch IS NULL THEN CAST(Pitch AS REAL)
				WHEN Pitch IS NOT NULL AND @Pitch IS NOT NULL THEN CAST(@Pitch AS REAL)
				ELSE NULL
			END,			Yaw = CASE
				WHEN @Yaw = 'NULL' THEN NULL
				WHEN Yaw IS NULL AND @Yaw IS NULL THEN NULL
				WHEN Yaw IS NULL AND @Yaw IS NOT NULL THEN CAST(@Yaw AS REAL)
				WHEN Yaw IS NOT NULL AND @Yaw IS NULL THEN CAST(Yaw AS REAL)
				WHEN Yaw IS NOT NULL AND @Yaw IS NOT NULL THEN CAST(@Yaw AS REAL)
				ELSE NULL
			END,			Roll = CASE
				WHEN @Roll = 'NULL' THEN NULL
				WHEN Roll IS NULL AND @Roll IS NULL THEN NULL
				WHEN Roll IS NULL AND @Roll IS NOT NULL THEN CAST(@Roll AS REAL)
				WHEN Roll IS NOT NULL AND @Roll IS NULL THEN CAST(Roll AS REAL)
				WHEN Roll IS NOT NULL AND @Roll IS NOT NULL THEN CAST(@Roll AS REAL)
				ELSE NULL
			END,			Tags = CASE
				WHEN @Tags = 'NULL' THEN NULL
				WHEN Tags IS NULL AND @Tags IS NULL THEN NULL
				WHEN Tags IS NULL AND @Tags IS NOT NULL THEN CAST(@Tags AS NVARCHAR(510))
				WHEN Tags IS NOT NULL AND @Tags IS NULL THEN CAST(Tags AS NVARCHAR(510))
				WHEN Tags IS NOT NULL AND @Tags IS NOT NULL THEN CAST(@Tags AS NVARCHAR(510))
				ELSE NULL
			END,			UUID = CASE
				WHEN @UUID = 'NULL' THEN NULL
				WHEN UUID IS NULL AND @UUID IS NULL THEN NULL
				WHEN UUID IS NULL AND @UUID IS NOT NULL THEN CAST(@UUID AS UNIQUEIDENTIFIER)
				WHEN UUID IS NOT NULL AND @UUID IS NULL THEN CAST(UUID AS UNIQUEIDENTIFIER)
				WHEN UUID IS NOT NULL AND @UUID IS NOT NULL THEN CAST(@UUID AS UNIQUEIDENTIFIER)
				ELSE NULL
			END	END
IF @Flag = 0
	BEGIN
		INSERT INTO ImageDB.[Camera] (
			Name,			X,			Y,			Z,			Pitch,			Yaw,			Roll,			Tags,			UUID		)
		VALUES (
			@Name,			@X,			@Y,			@Z,			@Pitch,			@Yaw,			@Roll,			@Tags,			@UUID		);
END
IF @Flag = 3
	BEGIN
		DELETE FROM ImageDB.[Camera]
		WHERE			(
				(
					CameraID IS NOT NULL
					AND @CameraID IS NOT NULL
					AND CameraID = @CameraID
				)
				OR
				(
					CameraID IS NULL
					AND @CameraID IS NULL
				)
			)			AND			(
				(
					Name IS NOT NULL
					AND @Name IS NOT NULL
					AND Name = @Name
				)
				OR
				(
					Name IS NULL
					AND @Name IS NULL
				)
			)			AND			(
				(
					X IS NOT NULL
					AND @X IS NOT NULL
					AND X = @X
				)
				OR
				(
					X IS NULL
					AND @X IS NULL
				)
			)			AND			(
				(
					Y IS NOT NULL
					AND @Y IS NOT NULL
					AND Y = @Y
				)
				OR
				(
					Y IS NULL
					AND @Y IS NULL
				)
			)			AND			(
				(
					Z IS NOT NULL
					AND @Z IS NOT NULL
					AND Z = @Z
				)
				OR
				(
					Z IS NULL
					AND @Z IS NULL
				)
			)			AND			(
				(
					Pitch IS NOT NULL
					AND @Pitch IS NOT NULL
					AND Pitch = @Pitch
				)
				OR
				(
					Pitch IS NULL
					AND @Pitch IS NULL
				)
			)			AND			(
				(
					Yaw IS NOT NULL
					AND @Yaw IS NOT NULL
					AND Yaw = @Yaw
				)
				OR
				(
					Yaw IS NULL
					AND @Yaw IS NULL
				)
			)			AND			(
				(
					Roll IS NOT NULL
					AND @Roll IS NOT NULL
					AND Roll = @Roll
				)
				OR
				(
					Roll IS NULL
					AND @Roll IS NULL
				)
			)			AND			(
				(
					Tags IS NOT NULL
					AND @Tags IS NOT NULL
					AND Tags = @Tags
				)
				OR
				(
					Tags IS NULL
					AND @Tags IS NULL
				)
			)			AND			(
				(
					UUID IS NOT NULL
					AND @UUID IS NOT NULL
					AND UUID = @UUID
				)
				OR
				(
					UUID IS NULL
					AND @UUID IS NULL
				)
			)	ENDEND