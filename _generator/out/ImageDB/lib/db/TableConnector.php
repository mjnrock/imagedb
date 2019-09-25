<?php	
	class TableConnector {
        protected $Database;
        public $Table = [
            "Catalog" => "FuzzyKnights",
            "Schema" => "ImageDB",
            "Table" => "Camera"
        ];
        public $Columns = [];

        public function __construct(&$database, $catalog, $schema, $table) {
            $this->Database = $database;
            $this->Table["Catalog"] = $catalog;
            $this->Table["Schema"] = $schema;
            $this->Table["Table"] = $table;

            $this->Columns = $this->Database->query($this->MetaQuery());

            foreach($this->Columns as $k => $Column) {
                $this->Columns[$k]["meta"] = json_decode($this->Columns[$k]["meta"]);

                //? Convert SQL BIT into true Boolean values
                // foreach($this->Columns[$k]["meta"] as $flag => $value) {
                //     if(substr($flag, 0, 2) === "is") {
                //         $this->Columns[$k]["meta"]->$flag = !!$this->Columns[$k]["meta"]->$flag;
                //     }
                // }
            }
        }
        public function __destruct() {}

		/**
		 * $action | INT | 0: CREATE, 1: READ, 2: UPDATE, 3: DELETE
		 * $payload | JSON | { "key":value, ... } structure
		 * $condition | STRING | The literal WHERE clause conditions in SQL syntax
		 */
        public function CRUD($action, $payload = null, $condition = null, $asJSON = false) {
			$params = [];
			$cols = [];

            if(is_array($payload)) {
                $payload = json_encode($payload);
				$cols = array_keys($payload);
            }

            $results = $this->Database->PDOStoredProcedure("CRUD", [
                [ $this->Table["Table"], PDO::PARAM_STR ],
                [ $action, PDO::PARAM_INT ],
                [ $payload, isset($payload) ? PDO::PARAM_STR : PDO::PARAM_NULL ],
                [ $condition, isset($condition) ? PDO::PARAM_STR : PDO::PARAM_NULL ]
            ], $this->Table["Schema"]);

            return $asJSON ? json_encode($results) : $results;
        }

		/**
		 * $params | ARRAY | Array of each parameter index fn order
		 * $asJSON | BOOL | Return as PHP array or as JSON
		 */
        public function Fetch($params = [], $asJSON = false) {
            $results = $this->Database->TVF("Get" . $this->Table["Table"], $params);

            return $asJSON ? json_encode($results) : $results;
        }

        public function MetaQuery() {
            return <<<SQL
SELECT
    c.TABLE_NAME AS "table",
    c.DATA_TYPE AS "type",
    c.COLUMN_NAME AS "name",
    c.ORDINAL_POSITION AS ordinality,
    CONCAT(
        '{',
            '"precision": ', COALESCE(CAST(c.CHARACTER_MAXIMUM_LENGTH AS VARCHAR), CAST(c.NUMERIC_PRECISION AS VARCHAR), 'null'), ', ',
            '"scale": ', COALESCE(CAST(c.NUMERIC_SCALE AS VARCHAR), 'null'), ',' ,
            '"default": ', CASE WHEN c.COLUMN_DEFAULT IS NOT NULL THEN CONCAT('"', SUBSTRING(c.COLUMN_DEFAULT, 2, LEN(c.COLUMN_DEFAULT) - 2), '"') ELSE 'null' END, ',' ,
            '"isNullable": ', CASE WHEN c.IS_NULLABLE = 'YES' THEN 1 ELSE 0 END, ',' ,
            '"isUnicode": ', CASE WHEN c.CHARACTER_SET_NAME = 'UNICODE' THEN 1 ELSE 0 END, ',' ,
            '"isString": ', CASE WHEN c.CHARACTER_SET_NAME IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isNumber": ', CASE WHEN c.NUMERIC_PRECISION IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isDatetime": ', CASE WHEN c.DATETIME_PRECISION IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isBoolean": ', CASE WHEN c.DATA_TYPE = 'bit' THEN 1 ELSE 0 END, ',' ,
            '"isUUID": ', CASE WHEN c.DATA_TYPE = 'uniqueidentifier' THEN 1 ELSE 0 END, ',' ,
			'"isPrimaryKey": ', CASE WHEN pk.COLUMN_NAME IS NOT NULL THEN 1 ELSE 0 END,
        '}'
    ) AS meta
FROM
    INFORMATION_SCHEMA.COLUMNS c
	LEFT JOIN (
		SELECT
			ku.TABLE_CATALOG,
			ku.TABLE_SCHEMA,
			ku.TABLE_NAME,
			ku.COLUMN_NAME
		FROM
			INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
			INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE ku
				ON tc.CONSTRAINT_TYPE = 'PRIMARY KEY'
				AND tc.CONSTRAINT_NAME = ku.CONSTRAINT_NAME
	) pk
		ON c.TABLE_CATALOG = pk.TABLE_CATALOG
		AND c.TABLE_SCHEMA = pk.TABLE_SCHEMA
		AND c.TABLE_NAME = pk.TABLE_NAME
		AND c.COLUMN_NAME = pk.COLUMN_NAME
WHERE
    c.TABLE_CATALOG = '{$this->Table["Catalog"]}'
    AND c.TABLE_SCHEMA = '{$this->Table["Schema"]}'
    AND c.TABLE_NAME = '{$this->Table["Table"]}'
ORDER BY
    ordinality
SQL;
        }
    }
?>