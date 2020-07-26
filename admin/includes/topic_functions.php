<?php 

// Post variables
$topic_id = 0;
$isEditingTopic = false;
$topic_name = "";
$topic_slug = "";




/* - - - - - - - - - - 
-  Post functions
- - - - - - - - - - -*/
// get all posts from DB
function getAllTopics()
{
	global $conn;
	
	// Admin can view all posts
	// Author can only view their posts
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
if (isset($_GET['edit-post'])) {
	$isEditingPost = true;
	$post_id = $_GET['edit-post'];
	editPost($post_id);
}

// if user clicks the update post button
if (isset($_POST['update_post'])) {
	updatePost($_POST);
}

// if user clicks the Delete post button
if (isset($_GET['delete-post'])) {
	$post_id = $_GET['delete-post'];
	deletePost($post_id);
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
		//$topic_slug = makeSlug($topic_name);
		// validate form
		if (empty($topic_name)) { array_push($errors, "topic name is required"); }
	
		
	
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
			else{array_push($errors, "Topic could not be Created");}
        } 
             
	}

	/* * * * * * * * * * * * * * * * * * * * *
	* - Takes Topic id as parameter
	* - Fetches the Topic from database
	* - sets Topic fields on form for editing
	* * * * * * * * * * * * * * * * * * * * * */
	function editTopic($role_id)
	{
		global $conn, $title, $post_slug, $body, $published, $isEditingPost, $post_id;
		$sql = "SELECT * FROM posts WHERE id=$role_id LIMIT 1";
		$result = mysqli_query($conn, $sql);
		$post = mysqli_fetch_assoc($result);
		// set form values on the form to be updated
		$title = $post['title'];
		$body = $post['body'];
        $published = $post['published'];
        // return $post;//////////////////////////////////////////////////////////////////////////
	}

	function updateTopic($request_values)
	{
		global $conn, $errors, $post_id, $title, $featured_image, $topic_id, $body, $published;

		$title = esc($request_values['title']);
		$body = esc($request_values['body']);
		$post_id = esc($request_values['post_id']);
		if (isset($request_values['topic_id'])) {
			$topic_id = esc($request_values['topic_id']);
		}
		// create slug: if title is "The Storm Is Over", return "the-storm-is-over" as slug
		$post_slug = makeSlug($title);

		if (empty($title)) { array_push($errors, "Post title is required"); }
		if (empty($body)) { array_push($errors, "Post body is required"); }
		// if new featured image has been provided
		if (isset($_POST['featured_image'])) {
			// Get image name
		  	$featured_image = $_FILES['featured_image']['name'];
		  	// image file directory
		  	$target = "../static/images/" . basename($featured_image);
		  	if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
		  		array_push($errors, "Failed to upload image. Please check file settings for your server");
		  	}
		}

		// register topic if there are no errors in the form
		if (count($errors) == 0) {
			$query = "UPDATE posts SET title='$title', slug='$post_slug', views=0, image='$featured_image', body='$body', published=$published, updated_at=now() WHERE id=$post_id";
			// attach topic to post on post_topic table
			if(mysqli_query($conn, $query)){ // if post created successfully
				if (isset($topic_id)) {
					$inserted_post_id = mysqli_insert_id($conn);
					// create relationship between post and topic
					$sql = "INSERT INTO post_topic (post_id, topic_id) VALUES($inserted_post_id, $topic_id)";
					mysqli_query($conn, $sql);
					$_SESSION['message'] = "Post created successfully";
					header('location: posts.php');
					exit(0);
				}
			}
			$_SESSION['message'] = "Post updated successfully";
			header('location: posts.php');
			exit(0);
		}
	}
	// delete blog post
	function deleteTopic($post_id)
	{
		global $conn;
		$sql = "DELETE FROM posts WHERE id=$post_id";
		if (mysqli_query($conn, $sql)) {
			$_SESSION['message'] = "Post successfully deleted";
			header("location: posts.php");
			exit(0);
		}
    }
    
    // if user clicks the publish post button
if (isset($_GET['publish']) || isset($_GET['unpublish'])) {
	$message = "";
	if (isset($_GET['publish'])) {
		$message = "Post published successfully";
		$post_id = $_GET['publish'];
	} else if (isset($_GET['unpublish'])) {
		$message = "Post successfully unpublished";
		$post_id = $_GET['unpublish'];
	}
	togglePublishPost($post_id, $message);
}
// delete blog post
function togglePublishTopic($post_id, $message)
{
	global $conn;
	$sql = "UPDATE posts SET published=!published WHERE id=$post_id";
	
	if (mysqli_query($conn, $sql)) {
		$_SESSION['message'] = $message;
		header("location: posts.php");
		exit(0);
	}
}
?>