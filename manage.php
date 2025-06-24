<?php
$conn = new mysqli("localhost", "root", "", "faculty_scheduling");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Initialize error message variable
$error_message = '';

// Handle Program CRUD
if (isset($_POST['add_program'])) {
    $name = $_POST['name'];
    $student_count_sem1 = (int)$_POST['student_count_sem1'];
    $student_count_sem2 = (int)$_POST['student_count_sem2'];
    $student_count_sem3 = (int)$_POST['student_count_sem3'];
    $student_count_sem4 = (int)$_POST['student_count_sem4'];
    $student_count_sem5 = (int)$_POST['student_count_sem5'];
    $student_count_sem6 = (int)$_POST['student_count_sem6'];
    $stmt = $conn->prepare("INSERT INTO programs (name, student_count_sem1, student_count_sem2, student_count_sem3, student_count_sem4, student_count_sem5, student_count_sem6) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiiii", $name, $student_count_sem1, $student_count_sem2, $student_count_sem3, $student_count_sem4, $student_count_sem5, $student_count_sem6);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['edit_program'])) {
    $id = (int)$_POST['id'];
    $name = $_POST['name'];
    $student_count_sem1 = (int)$_POST['student_count_sem1'];
    $student_count_sem2 = (int)$_POST['student_count_sem2'];
    $student_count_sem3 = (int)$_POST['student_count_sem3'];
    $student_count_sem4 = (int)$_POST['student_count_sem4'];
    $student_count_sem5 = (int)$_POST['student_count_sem5'];
    $student_count_sem6 = (int)$_POST['student_count_sem6'];
    $stmt = $conn->prepare("UPDATE programs SET name = ?, student_count_sem1 = ?, student_count_sem2 = ?, student_count_sem3 = ?, student_count_sem4 = ?, student_count_sem5 = ?, student_count_sem6 = ? WHERE id = ?");
    $stmt->bind_param("siiiiiii", $name, $student_count_sem1, $student_count_sem2, $student_count_sem3, $student_count_sem4, $student_count_sem5, $student_count_sem6, $id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete_program'])) {
    $id = (int)$_GET['delete_program'];
    // Check for dependent courses
    $check = $conn->query("SELECT COUNT(*) as count FROM courses WHERE program_id = $id");
    $row = $check->fetch_assoc();
    if ($row['count'] > 0) {
        $error_message = "Cannot delete program because it has associated courses. Please delete the courses first.";
    } else {
        $conn->query("DELETE FROM programs WHERE id = $id");
    }
}

// Handle Lecturer CRUD
if (isset($_POST['add_lecturer'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("INSERT INTO lecturers (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['edit_lecturer'])) {
    $id = (int)$_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("UPDATE lecturers SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete_lecturer'])) {
    $id = (int)$_GET['delete_lecturer'];
    $conn->query("DELETE FROM lecturers WHERE id = $id");
}

// Handle Course CRUD
if (isset($_POST['add_course'])) {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $credits = (int)$_POST['credits'];
    $semester = $_POST['semester'];
    $semester_number = (int)$_POST['semester_number'];
    $program_id = (int)$_POST['program_id'];
    $lecturer_id = (int)$_POST['lecturer_id'];
    $stmt = $conn->prepare("INSERT INTO courses (code, name, credits, semester, semester_number, program_id, lecturer_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisiii", $code, $name, $credits, $semester, $semester_number, $program_id, $lecturer_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['edit_course'])) {
    $id = (int)$_POST['id'];
    $code = $_POST['code'];
    $name = $_POST['name'];
    $credits = (int)$_POST['credits'];
    $semester = $_POST['semester'];
    $semester_number = (int)$_POST['semester_number'];
    $program_id = (int)$_POST['program_id'];
    $lecturer_id = (int)$_POST['lecturer_id'];
    $stmt = $conn->prepare("UPDATE courses SET code = ?, name = ?, credits = ?, semester = ?, semester_number = ?, program_id = ?, lecturer_id = ? WHERE id = ?");
    $stmt->bind_param("ssisiiii", $code, $name, $credits, $semester, $semester_number, $program_id, $lecturer_id, $id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete_course'])) {
    $id = (int)$_GET['delete_course'];
    $conn->query("DELETE FROM courses WHERE id = $id");
}

// Handle Room CRUD
if (isset($_POST['add_room'])) {
    $name = $_POST['name'];
    $capacity = (int)$_POST['capacity'];
    $stmt = $conn->prepare("INSERT INTO rooms (name, capacity) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $capacity);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['edit_room'])) {
    $id = (int)$_POST['id'];
    $name = $_POST['name'];
    $capacity = (int)$_POST['capacity'];
    $stmt = $conn->prepare("UPDATE rooms SET name = ?, capacity = ? WHERE id = ?");
    $stmt->bind_param("sii", $name, $capacity, $id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete_room'])) {
    $id = (int)$_GET['delete_room'];
    $conn->query("DELETE FROM rooms WHERE id = $id");
}

// Handle Time Slot CRUD
if (isset($_POST['add_time_slot'])) {
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $stmt = $conn->prepare("INSERT INTO time_slots (day, start_time, end_time) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $day, $start_time, $end_time);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['edit_time_slot'])) {
    $id = (int)$_POST['id'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $stmt = $conn->prepare("UPDATE time_slots SET day = ?, start_time = ?, end_time = ? WHERE id = ?");
    $stmt->bind_param("sssi", $day, $start_time, $end_time, $id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete_time_slot'])) {
    $id = (int)$_GET['delete_time_slot'];
    $conn->query("DELETE FROM time_slots WHERE id = $id");
}

// Fetch data for display
$programs = $conn->query("SELECT * FROM programs");
$lecturers = $conn->query("SELECT * FROM lecturers");
$courses = $conn->query("SELECT c.*, p.name as program_name, l.name as lecturer_name FROM courses c JOIN programs p ON c.program_id = p.id JOIN lecturers l ON c.lecturer_id = l.id");
$rooms = $conn->query("SELECT * FROM rooms");
$time_slots = $conn->query("SELECT * FROM time_slots");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Entities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Scheduling System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="manage.php">Manage Entities</a></li>
                    <li class="nav-item"><a class="nav-link" href="schedule.php">Generate Schedule</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Manage Entities</h2>
        <ul class="nav nav-tabs" id="entityTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs" type="button" role="tab">Programs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="lecturers-tab" data-bs-toggle="tab" data-bs-target="#lecturers" type="button" role="tab">Lecturers</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab">Courses</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#rooms" type="button" role="tab">Rooms</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="time-slots-tab" data-bs-toggle="tab" data-bs-target="#time-slots" type="button" role="tab">Time Slots</button>
            </li>
        </ul>
        <div class="tab-content" id="entityTabContent">
            <!-- Programs Tab -->
            <div class="tab-pane fade show active" id="programs" role="tabpanel">
                <h3 class="mt-4">Programs</h3>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="name" class="form-control" placeholder="Program Name" required>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="student_count_sem1" class="form-control" placeholder="Sem 1" required min="0">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="student_count_sem2" class="form-control" placeholder="Sem 2" required min="0">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="student_count_sem3" class="form-control" placeholder="Sem 3" required min="0">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="student_count_sem4" class="form-control" placeholder="Sem 4" required min="0">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="student_count_sem5" class="form-control" placeholder="Sem 5" required min="0">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="student_count_sem6" class="form-control" placeholder="Sem 6" required min="0">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_program" class="btn btn-primary">Add Program</button>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Sem 1</th>
                            <th>Sem 2</th>
                            <th>Sem 3</th>
                            <th>Sem 4</th>
                            <th>Sem 5</th>
                            <th>Sem 6</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $programs->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['student_count_sem1']; ?></td>
                                <td><?php echo $row['student_count_sem2']; ?></td>
                                <td><?php echo $row['student_count_sem3']; ?></td>
                                <td><?php echo $row['student_count_sem4']; ?></td>
                                <td><?php echo $row['student_count_sem5']; ?></td>
                                <td><?php echo $row['student_count_sem6']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProgramModal<?php echo $row['id']; ?>">Edit</button>
                                    <a href="?delete_program=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this program?');">Delete</a>
                                </td>
                            </tr>
                            <!-- Edit Program Modal -->
                            <div class="modal fade" id="editProgramModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Program</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label>Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label>Sem 1</label>
                                                        <input type="number" name="student_count_sem1" class="form-control" value="<?php echo $row['student_count_sem1']; ?>" required min="0">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label>Sem 2</label>
                                                        <input type="number" name="student_count_sem2" class="form-control" value="<?php echo $row['student_count_sem2']; ?>" required min="0">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label>Sem 3</label>
                                                        <input type="number" name="student_count_sem3" class="form-control" value="<?php echo $row['student_count_sem3']; ?>" required min="0">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label>Sem 4</label>
                                                        <input type="number" name="student_count_sem4" class="form-control" value="<?php echo $row['student_count_sem4']; ?>" required min="0">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label>Sem 5</label>
                                                        <input type="number" name="student_count_sem5" class="form-control" value="<?php echo $row['student_count_sem5']; ?>" required min="0">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label>Sem 6</label>
                                                        <input type="number" name="student_count_sem6" class="form-control" value="<?php echo $row['student_count_sem6']; ?>" required min="0">
                                                    </div>
                                                </div>
                                                <button type="submit" name="edit_program" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Lecturers Tab -->
            <div class="tab-pane fade" id="lecturers" role="tabpanel">
                <h3 class="mt-4">Lecturers</h3>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="Lecturer Name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_lecturer" class="btn btn-primary">Add Lecturer</button>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $lecturers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editLecturerModal<?php echo $row['id']; ?>">Edit</button>
                                    <a href="?delete_lecturer=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                            <!-- Edit Lecturer Modal -->
                            <div class="modal fade" id="editLecturerModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Lecturer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label>Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control" value="<?php echo $row['email']; ?>" required>
                                                </div>
                                                <button type="submit" name="edit_lecturer" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Courses Tab -->
            <div class="tab-pane fade" id="courses" role="tabpanel">
                <h3 class="mt-4">Courses</h3>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" name="code" class="form-control" placeholder="Code" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="name" class="form-control" placeholder="Course Name" required>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="credits" class="form-control" placeholder="Credits" required min="1">
                        </div>
                        <div class="col-md-2">
                            <select name="semester" class="form-control" required>
                                <option value="odd">Odd</option>
                                <option value="even">Even</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="semester_number" class="form-control" placeholder="Sem No" required min="1" max="6">
                        </div>
                        <div class="col-md-2">
                            <select name="program_id" class="form-control" required>
                                <?php
                                $programs->data_seek(0);
                                while ($row = $programs->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="lecturer_id" class="form-control" required>
                                <?php
                                $lecturers->data_seek(0);
                                while ($row = $lecturers->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Credits</th>
                            <th>Semester</th>
                            <th>Semester Number</th>
                            <th>Program</th>
                            <th>Lecturer</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $courses->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['code']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['credits']; ?></td>
                                <td><?php echo $row['semester']; ?></td>
                                <td><?php echo $row['semester_number']; ?></td>
                                <td><?php echo $row['program_name']; ?></td>
                                <td><?php echo $row['lecturer_name']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCourseModal<?php echo $row['id']; ?>">Edit</button>
                                    <a href="?delete_course=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                            <!-- Edit Course Modal -->
                            <div class="modal fade" id="editCourseModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Course</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label>Code</label>
                                                    <input type="text" name="code" class="form-control" value="<?php echo $row['code']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Credits</label>
                                                    <input type="number" name="credits" class="form-control" value="<?php echo $row['credits']; ?>" required min="1">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Semester</label>
                                                    <select name="semester" class="form-control" required>
                                                        <option value="odd" <?php if ($row['semester'] == 'odd') echo 'selected'; ?>>Odd</option>
                                                        <option value="even" <?php if ($row['semester'] == 'even') echo 'selected'; ?>>Even</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Semester Number</label>
                                                    <input type="number" name="semester_number" class="form-control" value="<?php echo $row['semester_number']; ?>" required min="1" max="6">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Program</label>
                                                    <select name="program_id" class="form-control" required>
                                                        <?php
                                                        $programs->data_seek(0);
                                                        while ($p = $programs->fetch_assoc()): ?>
                                                            <option value="<?php echo $p['id']; ?>" <?php if ($p['id'] == $row['program_id']) echo 'selected'; ?>><?php echo $p['name']; ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Lecturer</label>
                                                    <select name="lecturer_id" class="form-control" required>
                                                        <?php
                                                        $lecturers->data_seek(0);
                                                        while ($l = $lecturers->fetch_assoc()): ?>
                                                            <option value="<?php echo $l['id']; ?>" <?php if ($l['id'] == $row['lecturer_id']) echo 'selected'; ?>><?php echo $l['name']; ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <button type="submit" name="edit_course" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Rooms Tab -->
            <div class="tab-pane fade" id="rooms" role="tabpanel">
                <h3 class="mt-4">Rooms</h3>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="Room Name" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="capacity" class="form-control" placeholder="Capacity" required min="1">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_room" class="btn btn-primary">Add Room</button>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $rooms->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['capacity']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editRoomModal<?php echo $row['id']; ?>">Edit</button>
                                    <a href="?delete_room=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                            <!-- Edit Room Modal -->
                            <div class="modal fade" id="editRoomModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Room</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label>Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Capacity</label>
                                                    <input type="number" name="capacity" class="form-control" value="<?php echo $row['capacity']; ?>" required min="1">
                                                </div>
                                                <button type="submit" name="edit_room" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Time Slots Tab -->
            <div class="tab-pane fade" id="time-slots" role="tabpanel">
                <h3 class="mt-4">Time Slots</h3>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="day" class="form-control" required>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_time_slot" class="btn btn-primary">Add Time Slot</button>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $time_slots->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['day']; ?></td>
                                <td><?php echo $row['start_time']; ?></td>
                                <td><?php echo $row['end_time']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTimeSlotModal<?php echo $row['id']; ?>">Edit</button>
                                    <a href="?delete_time_slot=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                            <!-- Edit Time Slot Modal -->
                            <div class="modal fade" id="editTimeSlotModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Time Slot</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label>Day</label>
                                                    <select name="day" class="form-control" required>
                                                        <option value="Monday" <?php if ($row['day'] == 'Monday') echo 'selected'; ?>>Monday</option>
                                                        <option value="Tuesday" <?php if ($row['day'] == 'Tuesday') echo 'selected'; ?>>Tuesday</option>
                                                        <option value="Wednesday" <?php if ($row['day'] == 'Wednesday') echo 'selected'; ?>>Wednesday</option>
                                                        <option value="Thursday" <?php if ($row['day'] == 'Thursday') echo 'selected'; ?>>Thursday</option>
                                                        <option value="Friday" <?php if ($row['day'] == 'Friday') echo 'selected'; ?>>Friday</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Start Time</label>
                                                    <input type="time" name="start_time" class="form-control" value="<?php echo $row['start_time']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>End Time</label>
                                                    <input type="time" name="end_time" class="form-control" value="<?php echo $row['end_time']; ?>" required>
                                                </div>
                                                <button type="submit" name="edit_time_slot" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>