<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users_list";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($_POST['action'] === 'add') {
        $sql = "INSERT INTO users (`Email`, `PASSWORD`, `Role`) VALUES ('$email', '$password', '$role')";
    } elseif ($_POST['action'] === 'edit') {
        $id = $_POST['id'];
        $sql = "UPDATE users SET `Email` = '$email', `PASSWORD` = '$password', `Role` = '$role' WHERE id = $id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    $sql = "DELETE FROM users WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #222222;
            color: white;
        }
        input {
            background-color: white;
            color: black;
        }
        select {
            background-color: white;
            color: black;
        }
        .btn-primary {
            background-color: #375A7F;
            border: none;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Users</h1>

    <form method="POST" action="" class="mb-4">
        <input type="hidden" id="action" name="action" value="add">
        <input type="hidden" id="user_id" name="id">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" id="formSubmitBtn">Add User</button>
    </form>

    <table class="table table-bordered table-dark">
        <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Password</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['Email'] ?></td>
                    <td>
                        <span class="password-text"><?= $user['PASSWORD'] ?></span>
                    </td>
                    <td><?= $user['Role'] ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-btn"
                                data-id="<?= $user['id'] ?>"
                                data-email="<?= $user['Email'] ?>"
                                data-role="<?= $user['Role'] ?>">
                            Edit
                        </button>
                        <a href="?delete_user=<?= $user['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <hr>
    <pre class="bg-dark text-white p-3">
<?php print_r($users); ?>
    </pre>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const actionInput = document.getElementById('action');
    const formSubmitBtn = document.getElementById('formSubmitBtn');
    const userIdInput = document.getElementById('user_id');

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            actionInput.value = 'edit';
            formSubmitBtn.textContent = 'Save Changes';

            userIdInput.value = button.getAttribute('data-id');
            document.getElementById('email').value = button.getAttribute('data-email');
            document.getElementById('role').value = button.getAttribute('data-role');
        });
    });
});
</script>
</body>
</html>
