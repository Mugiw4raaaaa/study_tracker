<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'study_hours');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $hours = $_POST['hours'];
        $conn->query("INSERT INTO students (name, subject, hours) VALUES ('$name', '$subject', $hours)");
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $hours = $_POST['hours'];
        $conn->query("UPDATE students SET name='$name', subject='$subject', hours=$hours WHERE id=$id");
    }
    header('Location: index.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$id_to_delete");
    header('Location: index.php');
    exit;
}

// Fetch all students
$result = $conn->query("SELECT * FROM students");

// Insights with SQL subqueries
$top_students = $conn->query("SELECT name, subject, hours FROM students ORDER BY hours DESC LIMIT 3");
$ranking = $conn->query("SELECT name, subject, hours FROM students ORDER BY hours DESC");
$avg_result = $conn->query("SELECT AVG(hours) AS avg_hours FROM students");
$avg_hours = $avg_result->fetch_assoc()['avg_hours'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Student Study Hours Tracker</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- Custom CSS -->
<link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="container my-5">
    <header class="text-center mb-5">
        <h1 class="display-4 text-success fw-bold">Student Study Hours Tracker</h1>
        <p class="lead text-muted">by Cedrick Opulencia</p>
    </header>

    <!-- Add Student Form -->
    <section class="mb-5">
        <div class="card shadow-sm border-0 rounded-3 bg-light">
            <div class="card-body p-4">
                <h4 class="mb-4 text-center text-primary">Add New Student</h4>
                <form method="POST" class="row g-3 align-items-center justify-content-center">
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control" placeholder="Student Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="hours" class="form-control" placeholder="Hours" required>
                    </div>
                    <div class="col-md-2 text-center">
                        <button class="btn btn-success w-100" type="submit" name="add">
                            <i class="bi bi-plus-lg"></i> Add
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Student List -->
    <section class="mb-5">
        <h3 class="text-center mb-4 text-secondary">All Students</h3>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle bg-white shadow-sm rounded-3">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Hours</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form method="POST" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <td>
                                <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <input type="text" name="subject" value="<?= htmlspecialchars($row['subject']) ?>" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <input type="number" name="hours" value="<?= $row['hours'] ?>" class="form-control form-control-sm" required>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm me-2" type="submit" name="update" title="Update">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"
                                   title="Delete"><i class="bi bi-trash"></i></a>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Insights Dashboard -->
    <section>
        <h3 class="text-center mb-4 text-secondary">Insights & Rankings</h3>
        <div class="row g-4">
            <!-- Top 3 Students -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 bg-light">
                    <div class="card-header bg-primary text-white text-center rounded-top">
                        <h5>Top 3 Students</h5>
                    </div>
                    <ul class="list-group list-group-flush p-3" style="max-height: 200px; overflow-y: auto;">
                        <?php while($student = $top_students->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($student['name']) ?>
                            <span class="badge bg-primary rounded-pill"><?= $student['hours'] ?> hrs</span>
                        <?php if($student['subject']): ?>
                            <br><small class="text-muted">Subject: <?= htmlspecialchars($student['subject']) ?></small>
                        <?php endif; ?>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
            <!-- Overall Ranking -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 bg-light">
                    <div class="card-header bg-success text-white text-center rounded-top">
                        <h5>Overall Ranking</h5>
                    </div>
                    <ul class="list-group list-group-flush p-3" style="max-height: 200px; overflow-y: auto;">
                        <?php
                        $ranking->data_seek(0);
                        while($student = $ranking->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($student['name']) ?>
                            <span class="badge bg-success rounded-pill"><?= $student['hours'] ?> hrs</span>
                            <br><small class="text-muted">Subject: <?= htmlspecialchars($student['subject']) ?></small>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
            <!-- Average Hours -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 bg-light d-flex flex-column justify-content-center align-items-center p-4">
                    <h5 class="mb-3 text-info">Average Study Hours</h5>
                    <p class="display-4 text-success"><?= round($avg_hours, 2) ?> hrs</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Bootstrap JS & Icons (Optional for icons) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</body>
</html>