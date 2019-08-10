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
	@Tags NVARCHAR(255) = NULL
AS
BEGIN
	SET NOCOUNT ON;

    MERGE INTO ImageDB.[Image] AS t
	USING (
		SELECT
			@FilePath,
			@FileName,
			@FileExtension
	) as s (FilePath, [FileName], FileExtension)
		ON t.FilePath = s.FilePath
		AND t.[FileName] = s.[FileName]
		AND t.FileExtension = s.FileExtension
	WHEN MATCHED THEN
		UPDATE SET
			t.Width = CASE
				WHEN @Width = 'NULL' THEN NULL
				WHEN t.Width IS NULL AND @Width IS NULL THEN NULL
				WHEN t.Width IS NULL AND @Width IS NOT NULL THEN CAST(@Width AS REAL)
				WHEN t.Width IS NOT NULL AND @Width IS NULL THEN CAST(t.Width AS REAL)
				WHEN t.Width IS NOT NULL AND @Width IS NOT NULL THEN CAST(@Width AS REAL)
				ELSE NULL
			END,
			t.Height = CASE
				WHEN @Height = 'NULL' THEN NULL
				WHEN t.Height IS NULL AND @Height IS NULL THEN NULL
				WHEN t.Height IS NULL AND @Height IS NOT NULL THEN CAST(@Height AS REAL)
				WHEN t.Height IS NOT NULL AND @Height IS NULL THEN CAST(t.Height AS REAL)
				WHEN t.Height IS NOT NULL AND @Height IS NOT NULL THEN CAST(@Height AS REAL)
				ELSE NULL
			END,
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
