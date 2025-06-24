<?php
$conn = new mysqli("localhost", "root", "", "faculty_scheduling");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle Courses
if (isset($_POST['create_course'])) {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $credits = $_POST['credits'];
    $semester = $_POST['semester'];
    $program_id = $_POST['program_id'];
    $stmt = $conn->prepare("INSERT INTO courses (code, name, credits, semester, program_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $code, $name, $credits, $semester, $program_id);
    $stmt->execute();
    $stmt->close();
}
if (isset($_POST['update_course'])) {
    $id = $_POST['id'];
    $code = $_POST['code'];
    $name = $_POST['name'];
    $credits = $_POST['credits'];
    $semester = $_POST['semester'];
    $program_id = $_POST['program_id'];
    $stmt = $conn->prepare("UPDATE courses SET code=?, name=?, credits=?, semester=?, program_id=? WHERE id=?");
    $stmt->bind_param("ssisii", $code, $name, $credits, $semester, $program_id, $id);
    $stmt->execute();
    $stmt->close();
}
if (isset($_GET['delete_course'])) {
    $id = $_GET['delete_course'];
    $stmt = $conn->prepare("DELETE FROM courses WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Handle Lecturers
if (isset($_POST['create_lecturer'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("INSERT INTO lecturers (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $stmt->close();
}
if (isset($_POST['update_lecturer'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("UPDATE lecturers SET name=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $email, $id);
    $stmt->execute();
    $stmt->close();
}
if (isset($_GET['delete_lecturer'])) {
    $id = $_GET['delete_lecturer'];
    $stmt = $conn->prepare("DELETE FROM lecturers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Handle Rooms
if (isset($_POST['create_room'])) {
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $stmt = $conn->prepare("INSERT INTO rooms (name, capacity) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $capacity);
    $stmt->execute();
    $stmt->close();
}
if (isset($_POST['update_room'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $stmt = $conn->prepare("UPDATE rooms SET name=?, capacity=? WHERE id=?");
    $stmt->bind_param("sii", $name, $capacity, $id);
    $stmt->execute();
    $stmt->close();
}
if (isset($_GET['delete_room'])) {
    $id = $_GET['delete_room'];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Handle Time Slots
if (isset($_POST['create_time_slot'])) {
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $stmt = $conn->prepare("INSERT INTO time_slots (day, start_time, end_time) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $day, $start_time, $end_time);
    $stmt->execute();
    $stmt->close();
}
if (isset($_POST['update_time_slot'])) {
    $id = $_POST['id'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $stmt = $conn->prepare("UPDATE time_slots SET day=?, start_time=?, end_time=? WHERE id=?");
    $stmt->bind_param("sssi", $day, $start_time, $end_time, $id);
    $stmt->execute();
    $stmt->close();
}
if (isset($_GET['delete_time_slot'])) {
    $id = $_GET['delete_time_slot'];
    $stmt = $conn->prepare("DELETE FROM time_slots WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Handle Programs
if (isset($_POST['create_program'])) {
    $name = $_POST['name'];
    $student_count = $_POST['student_count'];
    $stmt = $conn->prepare("INSERT INTO programs (name, student_count) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $student_count);
    $stmt->execute();
    $stmt->close();
}
if (isset($_POST['update_program'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $student_count = $_POST['student_count'];
    $stmt = $conn->prepare("UPDATE programs SET name=?, student_count=? WHERE id=?");
    $stmt->bind_param("sii", $name, $student_count, $id);
    $stmt->execute();
    $stmt->close();
}
if (isset($_GET['delete_program'])) {
    $id = $_GET['delete_program'];
    $stmt = $conn->prepare("DELETE FROM programs WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch data
$courses = $conn->query("SELECT c.*, p.name as program_name FROM courses c JOIN programs p ON c.program_id = p.id");
$lecturers = $conn->query("SELECT * FROM lecturers");
$rooms = $conn->query("SELECT * FROM rooms");
$time_slots = $conn->query("SELECT * FROM time_slots");
$programs = $conn->query("SELECT * FROM programs");
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
                <button class="nav-link active" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab">Courses</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="lecturers-tab" data-bs-toggle="tab" data-bs-target="#lecturers" type="button" role="tab">Lecturers</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#rooms" type="button" role="tab">Rooms</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="time-slots-tab" data-bs-toggle="tab" data-bs-target="#time-slots" type="button" role="tab">Time Slots</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs" type="button" role="tab">Programs</button>
            </li>
        </ul>
        <div class="tab-content" id="entityTabContent">
            <!-- Courses -->
            <div class="tab-pane fade show active" id="courses" role="tabpanel">
                <h3 class="mt-4">Courses</h3>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-2"><input type="text" name="code" class="form-control" placeholder="Code" required></div>
                        <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
                        <div class="col-md-2"><input type="number" name="credits" class="form-control" placeholder="Credits" required></div>
                        <div class="col-md-2">
                            <select name="semester" class="form-control" required>
                                <option value="odd">Odd</option>
                                <option value="even">Even</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="program_id" class="form-control" required>
                                <?php while ($row = $programs->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                <?php endwhile; $programs->data_seek(0); ?>
                            </select>
                        </div>
                        <div class="col-md-1"><button type="submit" name="create_course" class="btn btn-primary">Add</button></div>
                    </div>
                </form>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Credits</th>
                            <th>Semester</th>
                            <th>Program</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $courses->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['code']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['credits']; ?></td>
                                <td><?php echo ucfirst($row['semester']); ?></td>
                                <td><?php echo $row['program_name']; ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="text" name="code" value="<?php echo $row['code']; ?>" required>
                                        <input type="text" name="name" value="<?php echo $row['name']; ?>" required>
                                        <input type="number" name="credits" value="<?php echo $row['credits']; ?>" required>
                                        <select name="semester" required>
                                            <option value="odd" <?php if ($row['semester'] == 'odd') echo 'selected'; ?>>Odd</option>
                                            <option value="even" <?php if ($row['semester'] == 'even') echo 'selected'; ?>>Even</option>
                                        </select>
                                        <select name="program_id" required>
                                            <?php $programs->data_seek(0); while ($p = $programs->fetch_assoc()): ?>
                                                <option value="<?php echo $p['id']; ?>" <?php if ($p['id'] == $row['program_id']) echo 'selected'; ?>><?php echo $p['name']; ?></option>
                                            <?php endwhile; $programs->data_seek(0); ?>
                                        </select>
                                        <button type="submit" name="update_course" class="btn btn-sm btn-warning">Update</button>
                                    </form>
                                    <a href="?delete_course=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <!-- Lecturers -->
            <div class="tab-pane fade" id="lecturers" role="tabpanel">
                <h3 class="mt-4">Lecturers</h3>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
                        <div class="col-md-4"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                        <div class="col-md-2"><button type="submit" name="create_lecturer" class="btn btn-primary">Add</button></div>
                    </div>
                </form>
                <table class="table table-bordered">
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
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="text" name="name" value="<?php echo $row['name']; ?>" required>
                                        <input type="email" name="email" value="<?php echo $row['email']; ?>" required>
                                        <button type="submit" name="update_lecturer" class="btn btn-sm btn-warning">Update</button>
                                    </form>
                                    <a href="?delete_lecturer=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <!-- Rooms -->
            <div class="tab-pane fade" id="rooms" role="tabpanel">
                <h3 class="mt-4">Rooms</h3>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Room Name" required></div>
                        <div class="col-md-4"><input type="number" name="capacity" class="form-control" placeholder="Capacity" required></div>
                        <div class="col-md-2"><button type="submit" name="create_room" class="btn btn-primary">Add</button></div>
                    </div>
                </form>
                <table class="table table-bordered">
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
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="text" name="name" value="<?php echo $row['name']; ?>" required>
                                        <input type="number" name="capacity" value="<?php echo $row['capacity']; ?>" required>
                                        <button type="submit" name="update_room" class="btn btn-sm btn-warning">Update</button>
                                    </form>
                                    <a href="?delete_room=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <!-- Time Slots -->
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
                        <div class="col-md-3"><input type="time" name="start_time" class="form-control" required></div>
                        <div class="col-md-3"><input type="time" name="end_time" class="form-control" required></div>
                        <div class="col-md-2"><button type="submit" name="create_time_slot" class="btn btn-primary">Add</button></div>
                    </div>
                </form>
                <table class="table table-bordered">
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
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <select name="day" required>
                                            <option value="Monday" <?php if ($row['day'] == 'Monday') echo 'selected'; ?>>Monday</option>
                                            <option value="Tuesday" <?php if ($row['day'] == 'Tuesday') echo 'selected'; ?>>Tuesday</option>
                                            <option value="Wednesday" <?php if ($row['day'] == 'Wednesday') echo 'selected'; ?>>Wednesday</option>
                                            <option value="Thursday" <?php if ($row['day'] == 'Thursday') echo 'selected'; ?>>Thursday</option>
                                            <option value="Friday" <?php if ($row['day'] == 'Friday') echo 'selected'; ?>>Friday</option>
                                        </select>
                                        <input type="time" name="start_time" value="<?php echo $row['start_time']; ?>" required>
                                        <input type="time" name="end_time" value="<?php echo $row['end_time']; ?>" required>
                                        <button type="submit" name="update_time_slot" class="btn btn-sm btn-warning">Update</button>
                                    </form>
                                    <a href="?delete_time_slot=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <!-- Programs -->
            <div class="tab-pane fade" id="programs" role="tabpanel">
                <h3 class="mt-4">Programs</h3>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Program Name" required></div>
                        <div class="col-md-4"><input type="number" name="student_count" class="form-control" placeholder="Student Count" required></div>
                        <div class="col-md-2"><button type="submit" name="create_program" class="btn btn-primary">Add</button></div>
                    </div>
                </form>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Student Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $programs->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['student_count']; ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="text" name="name" value="<?php echo $row['name']; ?>" required>
                                        <input type="number" name="student_count" value="<?php echo $row['student_count']; ?>" required>
                                        <button type="submit" name="update_program" class="btn btn-sm btn-warning">Update</button>
                                    </form>
                                    <a href="?delete_program=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
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