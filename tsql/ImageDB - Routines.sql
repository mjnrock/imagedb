--	======================================
--	PURPOSE: This TVF recursively adds the
--	  parent category into a dot-syntax
--	  naming (e.g. Root.Child.GrandChild)
--	======================================
IF OBJECT_ID('ImageDB.tvfEEntity') IS NOT NULL DROP FUNCTION ImageDB.tvfEEntity;
GO

CREATE FUNCTION ImageDB.tvfEEntity ()
RETURNS @EEntity TABLE (
	EEntityID INT,
	ParentEEntityID INT,
	Name VARCHAR(255),
	[Description] NVARCHAR(MAX),
	EntityEndpoint VARCHAR(255),
	EndpointDepth INT,
	Tags VARCHAR(255),
	UUID UNIQUEIDENTIFIER
)
AS
BEGIN
	WITH cte AS (
		SELECT
			c.EEntityID,
			c.ParentEEntityID,
			c.Name,
			c.[Description],
			CAST(c.Name AS VARCHAR(MAX)) AS EntityEndpoint,
			0 AS EndpointDepth,
			c.Tags,
			c.UUID
		FROM
			ImageDB.EEntity c
		WHERE
			c.ParentEEntityID IS NULL

		UNION ALL

		SELECT
			c.EEntityID,
			c.ParentEEntityID,
			c.Name,
			c.[Description],
			CAST(CONCAT(cte.EntityEndpoint, '.', c.Name) AS VARCHAR(MAX)) AS EntityEndpoint,
			cte.EndpointDepth + 1,
			c.Tags,
			c.UUID
		FROM
			cte
			INNER JOIN ImageDB.EEntity c
				ON cte.EEntityID = c.ParentEEntityID
	)

	INSERT INTO @EEntity
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

IF OBJECT_ID('ImageDB.UpdateTags') IS NOT NULL DROP PROCEDURE ImageDB.UpdateTags
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


--	[TODO]:	If there is an ERROR, INSERT will still increment IDENTITY; encapsulate transaction to allow COMMIT/ROLLBACK
IF OBJECT_ID('ImageDB.CRUD') IS NOT NULL DROP PROCEDURE ImageDB.CRUD
GO

CREATE PROCEDURE ImageDB.CRUD (
	@TableName NVARCHAR(255),
	@Action TINYINT = 1,
	@Payload NVARCHAR(MAX) = NULL,
	@Condition NVARCHAR(MAX) = NULL
)
AS
BEGIN
	SET NOCOUNT ON;

	DECLARE @DataTypes TABLE ([Column] VARCHAR(255), DataType VARCHAR(255), Ordinal INT);


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
		c.object_id = OBJECT_ID(CONCAT('ImageDB', '.', @TableName))
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
				ImageDB.ParseJSON(@Payload) j
				INNER JOIN @DataTypes d
					ON d.[Column] = j.[Name]
		END

	IF @Action = 0	-- CREATE
		BEGIN
			DECLARE @Vars NVARCHAR(MAX) = '';
			DECLARE @Insert NVARCHAR(MAX) = 'INSERT INTO [' + 'ImageDB' + '].[' + @TableName + '] (
';

			SELECT
				@Insert = CONCAT(
					@Insert,
					CHAR(9),
					'[',
					i.[Column],
					'],',
					CHAR(13)
				),
				@Vars = CONCAT(
					@Vars,
					CHAR(9),
					CASE
						WHEN i.Value = 'NULL' THEN NULL
						WHEN j.ValueType = 'string' THEN CONCAT('''', i.Value, '''')
						ELSE i.Value
					END,
					',',
					CHAR(13)
				)
			FROM
				@InputTable i
				INNER JOIN ImageDB.ParseJSON(@Payload) j
					ON i.[Column] = j.[Name]
	

			SET @Insert = SUBSTRING(@Insert, 0, LEN(@Insert) - 1) + '
)
OUTPUT INSERTED.*
VALUES (
' + SUBSTRING(@Vars, 0, LEN(@Vars ) - 1)+ '
);';

			--	PRINT @Insert;
			EXEC(@Insert);
		END
		
	IF @Action = 1	-- READ
		BEGIN
			DECLARE @Select NVARCHAR(MAX) = 'SELECT
';

			IF @Payload IS NULL
				BEGIN
					SET @Select = @Select + '	*,,';
				END
			ELSE
				BEGIN
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
			END

			SET @Select = SUBSTRING(@Select, 0, LEN(@Select) - 1) + '
FROM
	[' + 'ImageDB' + '].[' + @TableName + '] WITH (NOLOCK)'
	+ CASE
		WHEN @Condition IS NOT NULL THEN CONCAT('
WHERE
	', @Condition)
		ELSE ''
	END;
	
			--	PRINT @Select;
			EXEC(@Select);
		END
		
	IF @Action = 2	-- UPDATE
		BEGIN
			DECLARE @Update NVARCHAR(MAX) = 'UPDATE [' + 'ImageDB' + '].[' + @TableName + ']
SET
';
			SELECT
				@Update = CONCAT(
					@Update,
					CHAR(9),
					'[',
					i.[Column],
					'] = ',
					i.Value,
					',',
					CHAR(13)
				)
			FROM
				@InputTable i

		SET @Update = SUBSTRING(@Update, 0, LEN(@Update) - 1)
		+ '
OUTPUT INSERTED.*'
		+ CASE
			WHEN @Condition IS NOT NULL THEN CONCAT('
WHERE
	', @Condition)
			ELSE ''
		END;

			IF (SELECT COUNT(*) FROM @InputTable) > 0 AND @Condition IS NOT NULL
				BEGIN
					--	PRINT @Update
					EXEC(@Update);
				END			
			END
		
	IF @Action = 3	-- DELETE
		BEGIN
			DECLARE @Delete NVARCHAR(MAX) = 'DELETE FROM [' + 'ImageDB' + '].[' + @TableName + ']
OUTPUT DELETED.*' + CASE
			WHEN @Condition IS NOT NULL THEN CONCAT('
WHERE
	', @Condition)
			ELSE ''
		END;

			IF @Condition IS NOT NULL
				BEGIN
					EXEC(@Delete);
				END
		END
END
GO





IF OBJECT_ID('ImageDB.ParseJSON') IS NOT NULL DROP FUNCTION ImageDB.ParseJSON
GO

CREATE FUNCTION ImageDB.ParseJSON(@JSON NVARCHAR(MAX))
/**
Summary: >
  The code for the JSON Parser/Shredder will run in SQL Server 2005, 
  and even in SQL Server 2000 (with some modifications required).
 
  First the function replaces all strings with tokens of the form @Stringxx,
  where xx is the foreign key of the table variable where the strings are held.
  This takes them, and their potentially difficult embedded brackets, out of 
  the way. Names are  always strings in JSON as well as  string values.
 
  Then, the routine iteratively finds the next structure that has no structure 
  Contained within it, (and is, by definition the leaf structure), and parses it,
  replacing it with an object token of the form ‘@Objectxxx‘, or ‘@arrayxxx‘, 
  where xxx is the object id assigned to it. The values, or name/value pairs 
  are retrieved from the string table and stored in the hierarchy table. G
  radually, the JSON document is eaten until there is just a single root
  object left.
Author: PhilFactor
Date: 01/07/2010
Version: 
  Number: 4.6.2
  Date: 01/07/2019
  Why: case-insensitive version
Example: >
  Select * from ParseJSON('{    "Person": 
      {
       "firstName": "John",
       "lastName": "Smith",
       "age": 25,
       "Address": 
           {
          "streetAddress":"21 2nd Street",
          "city":"New York",
          "state":"NY",
          "postalCode":"10021"
           },
       "PhoneNumbers": 
           {
           "home":"212 555-1234",
          "fax":"646 555-4567"
           }
        }
     }
  ')
Returns: >
  nothing
**/
	RETURNS @hierarchy TABLE
	  (
	   Element_ID INT IDENTITY(1, 1) NOT NULL, /* internal surrogate primary key gives the order of parsing and the list order */
	   SequenceNo [int] NULL, /* the place in the sequence for the element */
	   Parent_ID INT null, /* if the element has a parent then it is in this column. The document is the ultimate parent, so you can get the structure from recursing from the document */
	   Object_ID INT null, /* each list or object has an object id. This ties all elements to a parent. Lists are treated as objects here */
	   Name NVARCHAR(2000) NULL, /* the Name of the object */
	   StringValue NVARCHAR(MAX) NOT NULL,/*the string representation of the value of the element. */
	   ValueType VARCHAR(10) NOT null /* the declared type of the value represented as a string in StringValue*/
	  )
	  /*
 
	   */
	AS
	BEGIN
	  DECLARE
	    @FirstObject INT, --the index of the first open bracket found in the JSON string
	    @OpenDelimiter INT,--the index of the next open bracket found in the JSON string
	    @NextOpenDelimiter INT,--the index of subsequent open bracket found in the JSON string
	    @NextCloseDelimiter INT,--the index of subsequent close bracket found in the JSON string
	    @Type NVARCHAR(10),--whether it denotes an object or an array
	    @NextCloseDelimiterChar CHAR(1),--either a '}' or a ']'
	    @Contents NVARCHAR(MAX), --the unparsed contents of the bracketed expression
	    @Start INT, --index of the start of the token that you are parsing
	    @end INT,--index of the end of the token that you are parsing
	    @param INT,--the parameter at the end of the next Object/Array token
	    @EndOfName INT,--the index of the start of the parameter at end of Object/Array token
	    @token NVARCHAR(200),--either a string or object
	    @value NVARCHAR(MAX), -- the value as a string
	    @SequenceNo int, -- the sequence number within a list
	    @Name NVARCHAR(200), --the Name as a string
	    @Parent_ID INT,--the next parent ID to allocate
	    @lenJSON INT,--the current length of the JSON String
	    @characters NCHAR(36),--used to convert hex to decimal
	    @result BIGINT,--the value of the hex symbol being parsed
	    @index SMALLINT,--used for parsing the hex value
	    @Escape INT --the index of the next escape character
	    
	  DECLARE @Strings TABLE /* in this temporary table we keep all strings, even the Names of the elements, since they are 'escaped' in a different way, and may contain, unescaped, brackets denoting objects or lists. These are replaced in the JSON string by tokens representing the string */
	    (
	     String_ID INT IDENTITY(1, 1),
	     StringValue NVARCHAR(MAX)
	    )
	  SELECT--initialise the characters to convert hex to ascii
	    @characters='0123456789abcdefghijklmnopqrstuvwxyz',
	    @SequenceNo=0, --set the sequence no. to something sensible.
	  /* firstly we process all strings. This is done because [{} and ] aren't escaped in strings, which complicates an iterative parse. */
	    @Parent_ID=0;
	  WHILE 1=1 --forever until there is nothing more to do
	    BEGIN
	      SELECT
	        @start=PATINDEX('%[^a-zA-Z]["]%', @json collate SQL_Latin1_General_CP850_Bin);--next delimited string
	      IF @start=0 BREAK --no more so drop through the WHILE loop
	      IF SUBSTRING(@json, @start+1, 1)='"' 
	        BEGIN --Delimited Name
	          SET @start=@Start+1;
	          SET @end=PATINDEX('%[^\]["]%', RIGHT(@json, LEN(@json+'|')-@start) collate SQL_Latin1_General_CP850_Bin);
	        END
	      IF @end=0 --either the end or no end delimiter to last string
	        BEGIN-- check if ending with a double slash...
             SET @end=PATINDEX('%[\][\]["]%', RIGHT(@json, LEN(@json+'|')-@start) collate SQL_Latin1_General_CP850_Bin);
 		     IF @end=0 --we really have reached the end 
				BEGIN
				BREAK --assume all tokens found
				END
			END 
	      SELECT @token=SUBSTRING(@json, @start+1, @end-1)
	      --now put in the escaped control characters
	      SELECT @token=REPLACE(@token, FromString, ToString)
	      FROM
	        (SELECT           '\b', CHAR(08)
	         UNION ALL SELECT '\f', CHAR(12)
	         UNION ALL SELECT '\n', CHAR(10)
	         UNION ALL SELECT '\r', CHAR(13)
	         UNION ALL SELECT '\t', CHAR(09)
			 UNION ALL SELECT '\"', '"'
	         UNION ALL SELECT '\/', '/'
	        ) substitutions(FromString, ToString)
		SELECT @token=Replace(@token, '\\', '\')
	      SELECT @result=0, @escape=1
	  --Begin to take out any hex escape codes
	      WHILE @escape>0
	        BEGIN
	          SELECT @index=0,
	          --find the next hex escape sequence
	          @escape=PATINDEX('%\x[0-9a-f][0-9a-f][0-9a-f][0-9a-f]%', @token collate SQL_Latin1_General_CP850_Bin)
	          IF @escape>0 --if there is one
	            BEGIN
	              WHILE @index<4 --there are always four digits to a \x sequence   
	                BEGIN
	                  SELECT --determine its value
	                    @result=@result+POWER(16, @index)
	                    *(CHARINDEX(SUBSTRING(@token, @escape+2+3-@index, 1),
	                                @characters)-1), @index=@index+1 ;
	         
	                END
	                -- and replace the hex sequence by its unicode value
	              SELECT @token=STUFF(@token, @escape, 6, NCHAR(@result))
	            END
	        END
	      --now store the string away 
	      INSERT INTO @Strings (StringValue) SELECT @token
	      -- and replace the string with a token
	      SELECT @JSON=STUFF(@json, @start, @end+1,
	                    '@string'+CONVERT(NCHAR(5), @@identity))
	    END
	  -- all strings are now removed. Now we find the first leaf.  
	  WHILE 1=1  --forever until there is nothing more to do
	  BEGIN
	 
	  SELECT @Parent_ID=@Parent_ID+1
	  --find the first object or list by looking for the open bracket
	  SELECT @FirstObject=PATINDEX('%[{[[]%', @json collate SQL_Latin1_General_CP850_Bin)--object or array
	  IF @FirstObject = 0 BREAK
	  IF (SUBSTRING(@json, @FirstObject, 1)='{') 
	    SELECT @NextCloseDelimiterChar='}', @type='object'
	  ELSE 
	    SELECT @NextCloseDelimiterChar=']', @type='array'
	  SELECT @OpenDelimiter=@firstObject
	  WHILE 1=1 --find the innermost object or list...
	    BEGIN
	      SELECT
	        @lenJSON=LEN(@JSON+'|')-1
	  --find the matching close-delimiter proceeding after the open-delimiter
	      SELECT
	        @NextCloseDelimiter=CHARINDEX(@NextCloseDelimiterChar, @json,
	                                      @OpenDelimiter+1)
	  --is there an intervening open-delimiter of either type
	      SELECT @NextOpenDelimiter=PATINDEX('%[{[[]%',
	             RIGHT(@json, @lenJSON-@OpenDelimiter)collate SQL_Latin1_General_CP850_Bin)--object
	      IF @NextOpenDelimiter=0 
	        BREAK
	      SELECT @NextOpenDelimiter=@NextOpenDelimiter+@OpenDelimiter
	      IF @NextCloseDelimiter<@NextOpenDelimiter 
	        BREAK
	      IF SUBSTRING(@json, @NextOpenDelimiter, 1)='{' 
	        SELECT @NextCloseDelimiterChar='}', @type='object'
	      ELSE 
	        SELECT @NextCloseDelimiterChar=']', @type='array'
	      SELECT @OpenDelimiter=@NextOpenDelimiter
	    END
	  ---and parse out the list or Name/value pairs
	  SELECT
	    @contents=SUBSTRING(@json, @OpenDelimiter+1,
	                        @NextCloseDelimiter-@OpenDelimiter-1)
	  SELECT
	    @JSON=STUFF(@json, @OpenDelimiter,
	                @NextCloseDelimiter-@OpenDelimiter+1,
	                '@'+@type+CONVERT(NCHAR(5), @Parent_ID))
	  WHILE (PATINDEX('%[A-Za-z0-9@+.e]%', @contents collate SQL_Latin1_General_CP850_Bin))<>0 
	    BEGIN
	      IF @Type='object' --it will be a 0-n list containing a string followed by a string, number,boolean, or null
	        BEGIN
	          SELECT
	            @SequenceNo=0,@end=CHARINDEX(':', ' '+@contents)--if there is anything, it will be a string-based Name.
	          SELECT  @start=PATINDEX('%[^A-Za-z@][@]%', ' '+@contents collate SQL_Latin1_General_CP850_Bin)--AAAAAAAA
              SELECT @token=RTrim(Substring(' '+@contents, @start+1, @End-@Start-1)),
	            @endofName=PATINDEX('%[0-9]%', @token collate SQL_Latin1_General_CP850_Bin),
	            @param=RIGHT(@token, LEN(@token)-@endofName+1)
	          SELECT
	            @token=LEFT(@token, @endofName-1),
	            @Contents=RIGHT(' '+@contents, LEN(' '+@contents+'|')-@end-1)
	          SELECT  @Name=StringValue FROM @strings
	            WHERE string_id=@param --fetch the Name
	        END
	      ELSE 
	        SELECT @Name=null,@SequenceNo=@SequenceNo+1 
	      SELECT
	        @end=CHARINDEX(',', @contents)-- a string-token, object-token, list-token, number,boolean, or null
                IF @end=0
	        --HR Engineering notation bugfix start
	          IF ISNUMERIC(@contents) = 1
		    SELECT @end = LEN(@contents) + 1
	          Else
	        --HR Engineering notation bugfix end 
		  SELECT  @end=PATINDEX('%[A-Za-z0-9@+.e][^A-Za-z0-9@+.e]%', @contents+' ' collate SQL_Latin1_General_CP850_Bin) + 1
	       SELECT
	        @start=PATINDEX('%[^A-Za-z0-9@+.e][A-Za-z0-9@+.e]%', ' '+@contents collate SQL_Latin1_General_CP850_Bin)
	      --select @start,@end, LEN(@contents+'|'), @contents  
	      SELECT
	        @Value=RTRIM(SUBSTRING(@contents, @start, @End-@Start)),
	        @Contents=RIGHT(@contents+' ', LEN(@contents+'|')-@end)
	      IF SUBSTRING(@value, 1, 7)='@object' 
	        INSERT INTO @hierarchy
	          (Name, SequenceNo, Parent_ID, StringValue, Object_ID, ValueType)
	          SELECT @Name, @SequenceNo, @Parent_ID, SUBSTRING(@value, 8, 5),
	            SUBSTRING(@value, 8, 5), 'object' 
	      ELSE 
	        IF SUBSTRING(@value, 1, 6)='@array' 
	          INSERT INTO @hierarchy
	            (Name, SequenceNo, Parent_ID, StringValue, Object_ID, ValueType)
	            SELECT @Name, @SequenceNo, @Parent_ID, SUBSTRING(@value, 7, 5),
	              SUBSTRING(@value, 7, 5), 'array' 
	        ELSE 
	          IF SUBSTRING(@value, 1, 7)='@string' 
	            INSERT INTO @hierarchy
	              (Name, SequenceNo, Parent_ID, StringValue, ValueType)
	              SELECT @Name, @SequenceNo, @Parent_ID, StringValue, 'string'
	              FROM @strings
	              WHERE string_id=SUBSTRING(@value, 8, 5)
	          ELSE 
	            IF @value IN ('true', 'false') 
	              INSERT INTO @hierarchy
	                (Name, SequenceNo, Parent_ID, StringValue, ValueType)
	                SELECT @Name, @SequenceNo, @Parent_ID, @value, 'boolean'
	            ELSE
	              IF @value='null' 
	                INSERT INTO @hierarchy
	                  (Name, SequenceNo, Parent_ID, StringValue, ValueType)
	                  SELECT @Name, @SequenceNo, @Parent_ID, @value, 'null'
	              ELSE
	                IF PATINDEX('%[^0-9]%', @value collate SQL_Latin1_General_CP850_Bin)>0 
	                  INSERT INTO @hierarchy
	                    (Name, SequenceNo, Parent_ID, StringValue, ValueType)
	                    SELECT @Name, @SequenceNo, @Parent_ID, @value, 'real'
	                ELSE
	                  INSERT INTO @hierarchy
	                    (Name, SequenceNo, Parent_ID, StringValue, ValueType)
	                    SELECT @Name, @SequenceNo, @Parent_ID, @value, 'int'
	      if @Contents=' ' Select @SequenceNo=0
	    END
	  END
	INSERT INTO @hierarchy (Name, SequenceNo, Parent_ID, StringValue, Object_ID, ValueType)
	  SELECT '-',1, NULL, '', @Parent_ID-1, @type
	--
	   RETURN
	END
GO