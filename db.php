<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bmsi_sis_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Display a success message as a pop-up
echo '<script>
        window.onload = function() {
            const notification = document.createElement("div");
            notification.id = "connectionNotification";
            notification.innerHTML = "Connected successfully";
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.add("show");
            }, 100);
            setTimeout(() => {
                notification.classList.remove("show");
            }, 3000); // Hide the notification after 4 seconds
        };
      </script>';

?>
