<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST request received";
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "GET request received";
} else {
    echo "This request method is not allowed";
}

// Start output buffering
ob_start();

// Sign-Up Process
if (isset($_POST['submit_signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];

    // Database connection parameters
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'folder'; // Database name changed to 'folder'

    // Establish database connection
    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Secure the password using hashing
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare the SQL query using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO mytable (NAME, EMAIL, PHONE_NO, PASSWORD) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $mobile, $hashed_password);

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>
                alert('Sign-Up successful!');
                setTimeout(function() {
                    window.location.href = 'index2.html';
                }, 2000); // 2-second delay before redirecting
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and database connection
    $stmt->close();
    mysqli_close($conn);
}

// Login Process
if (isset($_POST['submit_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection parameters
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'folder'; // Database name changed to 'folder'

    // Establish database connection
    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare the SQL query using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT PASSWORD FROM mytable WHERE EMAIL = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the entered password against the stored hashed password
        if (password_verify($password, $hashed_password)) {
            // Set the session variable for user email
            $_SESSION['user_email'] = $email;

            echo "<script>
                    alert('Login successful!');
                    setTimeout(function() {
                        window.location.href = 'index2.html';
                    }, 1000); // 2-second delay before redirecting
                  </script>";
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Invalid email or password.";
    }

    // Close the statement and database connection
    $stmt->close();
    mysqli_close($conn);
}

// End output buffering and flush output
ob_end_flush();
?>
