<?php
/**
 * Data source relational mapper application
 * @author  kari.eve.trace@gmail.com
 * @version 0.2
 * @since   2013-07-23
 * 
 * @package     Data Mapper
 * @copyright   Kari Trace
 */

/**
 * Deal with all the database conenction details and CRUD actions
 * @author  kari.eve.trace@gmail.com
 * @version 0.2
 * @since 	2013-07-23
* @todo 	CRUD methods need to handle both data AND title edits
 */
class database {
	/**
	 * Publicly accessible db_conn property
	 * @var PDO Object
	 */
	public  $db_conn 	= null;
	/**
	 * URL / IP of the DB to connect to
	 * @var string
	 */
	private $address 	= "test-mysql01.healthplan.com";
	/**
	 * Username to use when connecting
	 * @var string
	 */
	private $username 	= "root2";
	/**
	 * Password to use when connection
	 * @var string
	 */
	private $password 	= "php5orbust";
	/**
	 * DB to connect to when connection
	 * @var  string
	 */
	private $db_name 	= "mapping_0.1";
	/**
	 * title table
	 * @var  string
	 */
	private $title_tbo 	= "map_titles";
	/**
	 * data table
	 * @var  string
	 */
	private $data_tbo 	= "map_data";



	/**
	 * Build the connection
	 * @author  kari.eve.trace@gmail.com
	 * @version 0.1
	 * @since 	2013-07-09
	 * @return  null || string
	 */
	public function __construct() 
	{
		// Processing side changes when testing on localhost
		// If we are testing on localhost add '_test' to TBO string
		$whitelist = array('mapper.localhost', '127.0.0.1');
        if(in_array($_SERVER['HTTP_HOST'], $whitelist)){
			$this->title_tbo 	= $this->title_tbo."_test";
    		$this->data_tbo 	= $this->data_tbo."_test";
        };


		// PDO (prefered option)
		try {

			//$this->db_conn = new PDO('pdo:'.$this->address.';dbname='.$this->db_name.'', $this->username, $this->password);
			$this->db_conn =  new PDO('mysql:dbname='.$this->db_name.';host='.$this->address.'', $this->username, $this->password); 

    		$this->db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
    		echo 'ERROR: ' . $e->getMessage();
    	}
	}

	/**
	 * Create data method
	 * @author  kari.eve.trace@gmail.com
	 * @version 0.1
	 * @since  	2013-07-09
	 * @param  	stdClass $parameter Data to create (save) into the db
	 * @return 	mixed
	 */
	public function createItem($parameter) 
	{
		$return_data = false;
		$stmt 	= null;
		$query 	= null;



		// PDO prepard statement
		// TODO refactor this, apply DRY concept
		if ($parameter->type == "data") {
			$query = "
				INSERT INTO `{$this->db_name}`.`{$this->data_tbo}`
					(`map_group`, `map_data_col_1`, `map_data_col_2`, `map_data_col_3`, `map_data_col_4`, `map_data_col_5`, `sort_group`, `create`)
				VALUES
					(:map_group, :map_data_col_1, :map_data_col_2, :map_data_col_3, :map_data_col_4, :map_data_col_5, :sort_group, :create)
			";

			$pdo_stmt = $this->db_conn->prepare($query);

	    	$return_Data = $pdo_stmt->execute(
	    		array(
		    		"map_group"  	=> (!empty($parameter->map_group) ? $parameter->map_group : null),
		    		"map_data_col_1"=> (!empty($parameter->map_data_col_1) ? $parameter->map_data_col_1 : null),
		    		"map_data_col_2"=> (!empty($parameter->map_data_col_2) ? $parameter->map_data_col_2 : null),
		    		"map_data_col_3"=> (!empty($parameter->map_data_col_3) ? $parameter->map_data_col_3 : null),
		    		"map_data_col_4"=> (!empty($parameter->map_data_col_4) ? $parameter->map_data_col_4 : null),
		    		"map_data_col_5"=> (!empty($parameter->map_data_col_5) ? $parameter->sort_group : null),
		    		"sort_group" 	=> (!empty($parameter->sort_group) ? $parameter->sort_group : null),
		    		"create" => date("Y-m-d H:i:s"),
	    		)
	    	);
		} elseif ($parameter->type == "title") {
			$query = "
				INSERT INTO `{$this->db_name}`.`{$this->title_tbo}`
					(`map_data_title_1`, `map_data_title_2`, `map_data_title_3`, `map_data_title_4`, `map_data_title_5`, `sort_group`, `create`)
				VALUES
					(:map_data_title_1, :map_data_title_2, :map_data_title_3, :map_data_title_4, :map_data_title_5, :sort_group, :create)
			";

			$pdo_stmt = $this->db_conn->prepare($query);

	    	$return_Data = $pdo_stmt->execute(
	    		array(
		    		"map_data_title_1"  => (!empty($parameter->map_data_title_1) ? $parameter->map_data_title_1 : null),
		    		"map_data_title_2"  => (!empty($parameter->map_data_title_2) ? $parameter->map_data_title_2 : null),
		    		"map_data_title_3"  => (!empty($parameter->map_data_title_3) ? $parameter->map_data_title_3 : null),
		    		"map_data_title_4"  => (!empty($parameter->map_data_title_4) ? $parameter->map_data_title_4 : null),
		    		"map_data_title_5"  => (!empty($parameter->map_data_title_5) ? $parameter->map_data_title_5 : null),
		    		"sort_group" 		=> (!empty($parameter->map_data_col_5) ? $parameter->sort_group : null),
		    		"create" => date("Y-m-d H:i:s"),
	    		)
	    	);
		} else {

			return "No valid item type detected.";
		}

		try {
			//return data in a returnData() acceptable format
			$tmp['row_id'] = (integer)$this->db_conn->lastInsertId();
			return $tmp;
		} catch (Exception $e) {
			return "Error retrieving last inserted DB row ID.";
		}

		// Failback boolean return
		return false;
	}

	/**
	 * Read requested item data and return
	 * @author  kari.eve.trace@gmail.com
	 * @version 0.1.3
	 * @since 	2013-07-11
	 * @param  	stdClass $parameter [required]
	 * @return 	stdClass $return_data
	 * @todo 	make this able to handle both data AND title reading
	 */
	public function readItem($parameter)
	{
		$return_data = false;
		$stmt 	= null;
		$query 	= null;



		// Read the data and set return
		if ($parameter->type == "data") {
			
			// PDO prepard statement
			$query = "
				SELECT
					`{$this->data_tbo}`.*
				FROM
					`{$this->db_name}`.`{$this->data_tbo}`
				WHERE
					`{$this->data_tbo}`.`map_group` = :map_group AND
					`{$this->data_tbo}`.`delete` IS NULL
				ORDER BY
					`sort_group` DESC,
					`id` DESC
			";

			$stmt = $this->db_conn->prepare($query);

	    	$stmt->execute(
	    		array(
	    			"map_group"  => $parameter->map_group
	    		)
	    	);

			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		        $return_data[] = $row;
	    	}

		// Read the title and set return
		} elseif ($parameter->type == "title") {


			// PDO prepard statement
			$query = "
				SELECT
					*
				FROM
					`{$this->db_name}`.`{$this->title_tbo}`
				WHERE
					`{$this->title_tbo}`.`map_group` = :map_group AND
					`{$this->title_tbo}`.`delete` IS NULL
				ORDER BY
					`id` DESC
			";

			$stmt = $this->db_conn->prepare($query);

	    	$stmt->execute(
	    		array(
	    			"map_group"  => $parameter->map_group
	    		)
	    	);

			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		        $return_data[] = $row;
	    	}
		}

		return $return_data;
	}

	/**
	 * Update data method
	 * @author  kari.eve.trace@gmail.com
	 * @version 0.1
	 * @since  	2013-07-09
	 * @param  	stdClass $parameter Data to update (save) into the db
	 * @return 	array $return_data
	 * @todo 	make this able to handle both data AND title updates
	 */
	public function updateItem($parameter) 
	{
		$return_data = false;

		// PDO prepard statement
		$query = "
			UPDATE
				`{$this->db_name}`.`{$this->data_tbo}`
			SET
				`map_data_col_1`= :map_data_col_1,
				`map_data_col_2`= :map_data_col_2,
				`map_data_col_3`= :map_data_col_3,
				`map_data_col_4`= :map_data_col_4,
				`map_data_col_5`= :map_data_col_5,
				`sort_group` 	= :sort_group,
				`update` 		= :update
			WHERE
				`{$this->data_tbo}`.`id` = :id AND
				`{$this->data_tbo}`.`map_group` = :map_group
		";

		$stmt = $this->db_conn->prepare($query);

    	$return_data = $stmt->execute(
    		array(
    			"id" 			=> $parameter->id,
    			"map_group"  	=> $parameter->map_group,
    			"map_data_col_1"=> (!empty($parameter->map_data_col_1) ? $parameter->map_data_col_1 : null),
    			"map_data_col_2"=> (!empty($parameter->map_data_col_2) ? $parameter->map_data_col_2 : null),
    			"map_data_col_3"=> (!empty($parameter->map_data_col_3) ? $parameter->map_data_col_3 : null),
    			"map_data_col_4"=> (!empty($parameter->map_data_col_4) ? $parameter->map_data_col_4 : null),
    			"map_data_col_5"=> (!empty($parameter->map_data_col_5) ? $parameter->map_data_col_5 : null),
    			"sort_group" 	=> (!empty($parameter->sort_group) ? $parameter->sort_group : null),
    			"update" 		=> date("Y-m-d H:i:s")
    		)
    	);

		return array("boolean" => $return_data);
	}

	/**
	 * Delete data method
	 * NEVER EVER FOR ALL THAT IS HOLY HARD DELETE DATA!
	 * Instead update/populate the 'deleted' field
	 * @author  kari.eve.trace@gmail.com
	 * @version 0.1
	 * @since  	2013-07-10
	 * @param  	stdClass $parameter [required] Data object to use when "deleting" some dat ain the DB
	 * @return 	boolean
	 * @todo 	make this able to handle both data AND title deletions
	 */
	public function deleteItem($parameter) 
	{
		$return_Data = false;

		// PDO prepard statement
		$query = "
			UPDATE

				`{$this->db_name}`.`{$this->data_tbo}`
			SET
				`delete` = :delete,
				`map_data_col_1` = null
			WHERE
				id= :id;
		";

		$stmt = $this->db_conn->prepare($query);
    	
    	$return_data = $stmt->execute(
    		array(
    			"id" 			=> (integer)$parameter->id,
				"delete"		=> date("Y-m-d H:i:s")
			)
    	);

		return array("boolean" => $return_data);
	}

	/**
	 * Get and return mapping group options
	 * @author  kari.eve.trace@gmail.com
	 * @version 0.1.3
	 * @since   2013-07-11
	 * @return  object || boolean
	 */
	public function getMapGroupOptions()
	{
		$return_data = false;

		// PDO prepard statement
		$query = "
			SELECT
				*
			FROM
				`{$this->db_name}`.`{$this->title_tbo}`
			WHERE
				`delete` IS NULL
			ORDER BY
				`id` DESC
		";

		$stmt = $this->db_conn->prepare($query);

    	$stmt->execute();

		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	        $return_data[] = $row;
    	}

		if (!empty($empty_data)) {
			return $return_data;
		} else {
			return array("boolean" => $return_data);
		}
	}
}



/**
 * Process request and deal with the type of request (CRUD)
 * @author  kari.eve.trace@gmail.com
 * @version 0.1
 * @since 	2013-07-09
 */
class Processor {
	/**
	 * $field_data Form field data passed in becomes a object property
	 * @var string
	 */
	private $field_data = null;



	/**
	 * Build the process object
	 * @author  kari.eve.trace@gmail.com
	 * @version 0.1
	 * @since 	2013-07-09
	 * @param 	database $database [required] Database object to handle connection and query logic
	 * @param 	returnData $returnData [required] Object trhat handles data returning logic
	 */
	public function __construct(database $database, returnData $returnData)
	{
		// Load POST data into a local private var
		if (isset($_POST['data']) && !empty($_POST['data'])) {

			foreach (json_decode($_POST['data']) as $key=>$value) {
				
				$tmp = explode("=", $value);

				$_POST[$tmp[0]] = $tmp[1];
			}

			unset($_POST['data']);
		}

		// Convert all $_POST key=>value paris into local properties
		foreach ($_POST as $key => $value) {
			$field_data->$key = $value;
		}

		// UnComment to view inbound data
		// print_r($field_data);
		// exit;


		// TODO sanitize data



		// DB connection
		$this->database = $database;

		// getMapGroupOptions
		if (isset($field_data->action) && $field_data->action  == "getMapGroupOptions") {
			$returnData->returnData($database->getMapGroupOptions($field_data));
		}

		// Crud for Items
		if (isset($field_data->action) && $field_data->action  == "createItem") {
				$returnData->returnData($database->createItem($field_data));
		}

		// cRud for titles
		if (isset($field_data->action) && $field_data->action  == "readItem") {
			$returnData->returnData($database->readItem($field_data));
		}

		// crUd for Item(s)
		if ((isset($field_data->action) && $field_data->action  == "updateItem") && !empty($field_data->id)) {
				$returnData->returnData($database->updateItem($field_data));
		}

		// cruD for data
		if (
			$field_data->action  == "deleteItem" &&
			isset($field_data->type) && is_string($field_data->type) &&
			isset($field_data->id) && is_numeric($field_data->id)
		) {

			$returnData->returnData($database->deleteItem($field_data));
		}
	}



	// Private helper methods
	/**
	 * Sanitize data
	 * @author 	kari.eve.trace@gmail.com
	 * @version 0.2
	 * @since 	2013-07-09
	 */
	private function sanitize() {}
}



/**
 * Return data back to the user. Right now simply as json
 * @author  kari.eve.trace@gmail.com
 * @version 0.1
 * @since 	2013-07-10
 */
class returnData {

	/**
	 * Empty __constructor
 	 * @author  kari.eve.trace@gmail.com
 	 * @version 0.1
 	 * @since  	2013-07-10
	 */
	public function __construct() {}

	/**
	 * Pass in array, return json object
 	 * @author  kari.eve.trace@gmail.com
 	 * @version 0.1
 	 * @since 	2013-07-10
 	 * @param 	array || object $parameter [required] Array of data to return to user
 	 * @return  JSON object || boolean
	 */
	public static function returnData($parameter)
	{
		if (is_array($parameter) || is_object($parameter)) {
			echo json_encode($parameter);
			exit;
		} else {
			$error = array("boolean" => false);
			echo json_encode($error);
			exit;
		}
	}
}

$processor 	= new Processor(new database(), new returnData());
