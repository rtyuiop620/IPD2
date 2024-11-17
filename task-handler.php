<?php
$servername = "localhost"; // Replace with your database host
$username = "username"; // Replace with your database username
$password = "password"; // Replace with your database password
$dbname = "strategy"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save task to database
    $taskText = $_POST['task'];
    $sql = "INSERT INTO list (task_text, completed) VALUES ('$taskText', 0)";  // Default 'completed' is 0 (not completed)
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch tasks from database
    $result = $conn->query("SELECT * FROM list");
    $tasks = [];

    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    echo json_encode($tasks);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Delete task from database
    parse_str(file_get_contents("php://input"), $deleteData); // To parse the raw POST data
    $taskId = $deleteData['id'];  // Assuming task ID is passed
    $sql = "DELETE FROM list WHERE id = $taskId";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update task completion status in database
    parse_str(file_get_contents("php://input"), $putData); // To parse the raw POST data
    $taskId = $putData['id'];  // Task ID
    $completed = $putData['completed'];  // Completed status (1 or 0)

    $sql = "UPDATE list SET completed = $completed WHERE id = $taskId";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
}

$conn->close();
?>
