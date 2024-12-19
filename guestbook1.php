<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "root1234";
$dbname = "guestbook";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_record'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO guest (name, email, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $comment);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM guest WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_record'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['edit_name'];
    $email = $_POST['edit_email'];
    $comment = $_POST['edit_comment'];

    $sql = "UPDATE guest SET name = ?, email = ?, comment = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $comment, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch all records
$sql = "SELECT * FROM guest";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 30px;
        }
        form label {
            display: block;
            margin: 10px 0 5px;
        }
        form input, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form button {
            background-color: #5cb85c;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #4cae4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .actions a, .actions form {
            display: inline-block;
            margin-right: 10px;
        }
        .actions a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            color: #fff;
        }
        .actions a.delete {
            background-color: #d9534f;
        }
        .actions a.edit {
            background-color: #5bc0de;
        }
        .actions a:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Guest Book</h1>

        <form method="POST" action="">
            <input type="hidden" name="add_record" value="1">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" rows="4" placeholder="Write your comment" required></textarea>

            <button type="submit">Submit</button>
        </form>

        <h2>Guest Entries</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Comment</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['comment']) ?></td>
                    <td class="actions">
                        <form method="POST" action="">
                            <input type="hidden" name="edit_record" value="1">
                            <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                            <input type="text" name="edit_name" value="<?= htmlspecialchars($row['name']) ?>" required>
                            <input type="email" name="edit_email" value="<?= htmlspecialchars($row['email']) ?>" required>
                            <input type="text" name="edit_comment" value="<?= htmlspecialchars($row['comment']) ?>" required>
                            <button type="submit">Update</button>
                        </form>
                        <a class="delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
