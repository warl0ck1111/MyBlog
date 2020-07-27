<?php 

// Post variables
$topic_id = 0;
$isEditingTopic = false;
$topic_name = "";
$topic_slug = "";




/* - - - - - - - - - - 
-  Topic functions
- - - - - - - - - - -*/
// get all Topics from DB
function getAllTopics()
{
	global $conn;
	
	// Admin & Author can CRUD all Topics
	if ($_SESSION['user']['role'] == "Admin") {
		$sql = "SELECT * FROM topics";
	} elseif ($_SESSION['user']['role'] == "Author") {
		$user_id = $_SESSION['user']['id'];
		$sql = "SELECT * FROM topics";
	}
	$result = mysqli_query($conn, $sql);
	$topics = mysqli_fetch_all($result, MYSQLI_ASSOC);

	// $final_topics = array();
	// foreach ($topics as $topic) {
	// 	$topic['author'] = getPostAuthorById($post['user_id']);
	// 	array_push($final_posts, $post);
	// }
	return $topics;
}



/* - - - - - - - - - - 
-  Topic actions 
- - - - - - - - - - -*/
// if user clicks the create Topic button
if (isset($_POST['create_topic'])) { createTopic($_POST); }

// if user clicks the Edit post button
if (isset($_GET['edit-topic'])) {
	$isEditingTopic = true;
	$topic_id = $_GET['edit-topic'];
	editTopic($topic_id);
}

// if user clicks the update post button
if (isset($_POST['update_topic'])) {
	updateTopic($_POST);
}

// if user clicks the Delete post button
if (isset($_GET['delete-topic'])) {
	$topic_id = $_GET['delete-topic'];
	deleteTopic($topic_id);
}

/* - - - - - - - - - - 
-  Topic functions
- - - - - - - - - - -*/
function createTopic($request_values)
	{
		global $conn, $errors, $topic_id, $topic_name, $topic_slug;
		$topic_name = esc($request_values['topic_name']);
		
		if (isset($request_values['topic_id'])) {
			$topic_id = esc($request_values['topic_id']);
		}
		
		// create slug: if Topic Name is "The Storm Is Over", return "the-storm-is-over" as slug
		$topic_slug = makeSlug($topic_name);
		
		// validate form
		if (empty($topic_name)) { array_push($errors, "Topic name is required"); }
	
		
	
		// Ensure that no Topic is saved twice. 
		$topic_check_query = "SELECT * FROM topics WHERE name='$topic_name' LIMIT 1";
		$result = mysqli_query($conn, $topic_check_query);

		if (mysqli_num_rows($result) > 0) { // if topic exists
			array_push($errors, "A Topic already exists with that title.");
		}
		// create topic if there are no errors in the form
		if (count($errors) == 0) {

			//$user_id = $_SESSION['user']['id'];
			$query = "INSERT INTO topics ( name, slug ) VALUES('$topic_name', '$topic_slug')";
			
			//  if Topics created successfully
			if(mysqli_query($conn, $query)){ 
				
				$_SESSION['message'] = "Topic created successfully";
				header('location: topics.php');
				exit(0);
			}
			else{array_push($errors, "Topic could not be Created: \n".$conn->error);}
        } 
             
	}

	/* * * * * * * * * * * * * * * * * * * * *
	* - Takes Topic id as parameter
	* - Fetches the Topic from database
	* - sets Topic fields on form for editing
	* * * * * * * * * * * * * * * * * * * * * */
	function editTopic($role_id)
	{
		global $conn, $topic_name, $topic_slug, $topic_id;
		$sql = "SELECT * FROM topics WHERE id=$role_id LIMIT 1";
		$result = mysqli_query($conn, $sql);
		$topic = mysqli_fetch_assoc($result);
		// set form values on the form to be updated
		$topic_name = $topic['name'];

        //return $topic;//////////////////////////////////////////////////////////////////////////
	}


	/* * * * * * * * * * * * * * * * * * * * ** * * * * * * *
	* - Takes Topic the $_Get Array as parameter from the form 
	* - Updates The topic 
	* * * * * * * * * * * * * * * * * * * * * * * * * * * */
	function updateTopic($request_values)
	{
		global $conn, $errors,$topic_id, $topic_name, $topic_slug;

		$topic_name = esc($request_values['topic_name']);
		
		//$topic_id = esc($request_values['topic_id']);
		if (isset($request_values['topic_id'])) {
			$topic_id = esc($request_values['topic_id']);
		}else{array_push($errors,"ant get Topic Id");}

		// create slug: if title is "The Storm Is Over", return "the-storm-is-over" as slug
		$topic_slug = makeSlug($topic_name);

		if (empty($topic_name)) { array_push($errors, "Topic title is required"); }
		//isTopicExist Check if topic Exists;
		// register topic if there are no errors in the form
		if (count($errors) == 0) {
			$query = "UPDATE topics SET name='$topic_name', slug='$topic_slug' WHERE id=$topic_id";
			
			if(mysqli_query($conn, $query)){ // if Topic updated  successfully
				
				$_SESSION['message'] = "Topic updated successfully";
					header('location: topics.php');
					exit(0);
			}else{array_push($errors, $conn->error);} //change this to more readable error msg i.e you hv to write duuplicate entry error handler
			
		}
		
	}
	// delete blog Topic
	/* this takes a parameter of $_GET array = id (topic id)*/
	function deleteTopic($topic_id)
	{
		global $conn;
		$sql = "DELETE FROM Topics WHERE id=$topic_id";
		if (mysqli_query($conn, $sql)) {
			$_SESSION['message'] = "Topic successfully deleted";
			header("location: topics.php");
			exit(0);
		}
		else{
			array_push($errors,$conn->error);
		}
    }
    
   

?>