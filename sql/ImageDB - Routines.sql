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









ALTER PROCEDURE ImageDB.[CRUD:Camera] (
	@Action TINYINT = 1,
	@Payload NVARCHAR(MAX) = NULL,
	@Condition NVARCHAR(MAX) = NULL
)
AS
BEGIN
	SET NOCOUNT ON;

	DECLARE @DataTypes TABLE ([Column] VARCHAR(255), DataType VARCHAR(255), Ordinal INT);
	DECLARE @TableName NVARCHAR(261) = N'Camera';

	INSERT INTO @DataTypes ([Column], DataType, Ordinal)
	SELECT 
		c.name AS "Column",
		UPPER(CONCAT(
			TYPE_NAME(c.user_type_id),
			CASE 
				--types without length, precision, or scale specifiecation
				WHEN TYPE_NAME(c.user_type_id) IN (N'int',N'bigint',N'smallint',N'tinyint',N'money',N'smallmoney',N'real',N'datetime',N'smalldatetime',N'bit',N'image',N'text',N'uniqueidentifier',N'date',N'ntext',N'sql_variant',N'hierarchyid','geography',N'timestamp',N'xml') 
					THEN N''
				--types with precision and scale specification
				WHEN TYPE_NAME(c.user_type_id) in (N'decimal',N'numeric') 
					THEN N'(' + CAST(c.precision AS varchar(5)) + N',' + CAST(c.scale AS varchar(5)) + N')'
				--types with scale specification only
				WHEN TYPE_NAME(c.user_type_id) in (N'time',N'datetime2',N'datetimeoffset') 
					THEN N'(' + CAST(c.scale AS varchar(5)) + N')'
				--float default precision is 53 - add precision when column has a different precision value
				WHEN TYPE_NAME(c.user_type_id) in (N'float')
					THEN CASE WHEN c.precision = 53 THEN N'' ELSE N'(' + CAST(c.precision AS varchar(5)) + N')' END
				--types with length specifiecation
				ELSE N'(' + CASE c.max_length WHEN -1 THEN N'MAX' ELSE CAST(c.max_length AS nvarchar(20)) END + N')'
			END
		)) AS DataType,
		c.column_id AS Ordinal
	FROM
		sys.columns AS c (NOLOCK) 
		LEFT JOIN sys.computed_columns AS cc (NOLOCK) ON
			cc.object_id = c.object_id
			AND cc.column_id = c.column_id
	WHERE
		c.object_id = OBJECT_ID('ImageDB.' + @TableName)
	ORDER BY c.column_id;

	DECLARE @InputTable TABLE (
		[Column] NVARCHAR(255),
		DataType NVARCHAR(255),
		Value NVARCHAR(MAX)
	);	
	IF @Payload IS NOT NULL
		BEGIN
			INSERT INTO @InputTable
			SELECT
				d.[Column],
				d.DataType,
				j.StringValue
			FROM
				ParseJSON(@Payload) j
				INNER JOIN @DataTypes d
					ON d.[Column] = j.[Name]
		END

	IF @Action = 0	-- CREATE
		BEGIN
			SELECT 0
--			DECLARE @Vars NVARCHAR(MAX) = '';
--			DECLARE @Insert NVARCHAR(MAX) = 'INSERT INTO ImageDB.[Camera] (
--';

--			SELECT
--				@Insert = CONCAT(
--					@Insert,
--					CHAR(9),
--					'[',
--					i.[Column],
--					'],',
--					CHAR(13)
--				),
--				@Vars = CONCAT(
--					@Vars,
--					CHAR(9),
--					CASE
--						WHEN i.Value = 'NULL' THEN NULL
--						WHEN j.ValueType = 'string' THEN CONCAT('''', i.Value, '''')
--						ELSE i.Value
--					END,
--					',',
--					CHAR(13)
--				)
--			FROM
--				@InputTable i
--				INNER JOIN ParseJSON(@Payload) j
--					ON i.[Column] = j.[Name]
	

--			SET @Insert = SUBSTRING(@Insert, 0, LEN(@Insert) - 1) + '
--)
--VALUES (
--' + SUBSTRING(@Vars, 0, LEN(@Vars ) - 1)+ '
--);';

--			EXEC(@Insert);
		END
		
	IF @Action = 1	-- READ
		BEGIN
			IF @Payload IS NULL
				BEGIN
					SELECT
						*
					FROM
						ImageDB.Camera WITH (NOLOCK)
				END
			ELSE
				BEGIN
					DECLARE @Select NVARCHAR(MAX) = 'SELECT
';
					SELECT
						@Select = CONCAT(
							@Select,
							CHAR(9),
							'[',
							i.[Column],
							'],',
							CHAR(13)
						)
					FROM
						@InputTable i

					SET @Select = SUBSTRING(@Select, 0, LEN(@Select) - 1)+ '
FROM
	ImageDB.Camera WITH (NOLOCK)'
	+ CASE
		WHEN @Condition IS NOT NULL THEN CONCAT('
WHERE
	', @Condition)
		ELSE ''
	END;

					EXEC(@Select);
				END
		END
		
	IF @Action = 2	-- UPDATE
		BEGIN
			SELECT 2

			DECLARE @Update NVARCHAR(MAX) = ''
		END
		
	IF @Action = 3	-- DELETE
		BEGIN
			SELECT 3
		END
END