<?php
$conn = new mysqli("localhost", "root", "", "faculty_scheduling");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

class Schedule {
    public $course_id, $lecturer_id, $room_id, $time_slot_id;
    public function __construct($course_id, $lecturer_id, $room_id, $time_slot_id) {
        $this->course_id = $course_id;
        $this->lecturer_id = $lecturer_id;
        $this->room_id = $room_id;
        $this->time_slot_id = $time_slot_id;
    }
}

function get_fitness($population, $conn, $semester) {
    $conflicts = 0;
    $room_usage = [];
    $lecturer_usage = [];
    foreach ($population as $schedule) {
        $key = "{$schedule->time_slot_id}-{$schedule->room_id}";
        $lkey = "{$schedule->time_slot_id}-{$schedule->lecturer_id}";
        if (isset($room_usage[$key]) || isset($lecturer_usage[$lkey])) {
            $conflicts++;
        }
        $room_usage[$key] = true;
        $lecturer_usage[$lkey] = true;

        // Check room capacity
        $result = $conn->query("SELECT r.capacity, p.student_count 
                                FROM rooms r 
                                JOIN courses c ON c.id = {$schedule->course_id}
                                JOIN programs p ON c.program_id = p.id 
                                WHERE r.id = {$schedule->room_id} AND c.semester = '$semester'");
        if ($result && $data = $result->fetch_assoc()) {
            if ($data['student_count'] > $data['capacity']) {
                $conflicts++;
            }
        }
    }
    return $conflicts == 0 ? 1000 : 1000 / (1 + $conflicts);
}

function crossover($parent1, $parent2) {
    $child = [];
    $crossover_point = rand(0, count($parent1) - 1);
    for ($i = 0; $i < count($parent1); $i++) {
        if ($i < $crossover_point) {
            $child[] = clone $parent1[$i];
        } else {
            $child[] = clone $parent2[$i];
        }
    }
    return $child;
}

function mutate($population, $lecturers, $rooms, $time_slots) {
    $index = rand(0, count($population) - 1);
    $schedule = $population[$index];
    $schedule->lecturer_id = $lecturers[array_rand($lecturers)]['id'];
    $schedule->room_id = $rooms[array_rand($rooms)]['id'];
    $schedule->time_slot_id = $time_slots[array_rand($time_slots)]['id'];
    $population[$index] = $schedule;
    return $population;
}

function generate_schedule($conn, $semester) {
    // Fetch data
    $courses = $conn->query("SELECT id FROM courses WHERE semester = '$semester'")->fetch_all(MYSQLI_ASSOC);
    $lecturers = $conn->query("SELECT id FROM lecturers")->fetch_all(MYSQLI_ASSOC);
    $rooms = $conn->query("SELECT id FROM rooms")->fetch_all(MYSQLI_ASSOC);
    $time_slots = $conn->query("SELECT id FROM time_slots")->fetch_all(MYSQLI_ASSOC);

    if (empty($courses) || empty($lecturers) || empty($rooms) || empty($time_slots)) {
        return false; // Data not sufficient
    }

    // Initialize population
    $population_size = 50;
    $generations = 100;
    $population = [];
    for ($i = 0; $i < $population_size; $i++) {
        $individual = [];
        foreach ($courses as $course) {
            $individual[] = new Schedule(
                $course['id'],
                $lecturers[array_rand($lecturers)]['id'],
                $rooms[array_rand($rooms)]['id'],
                $time_slots[array_rand($time_slots)]['id']
            );
        }
        $population[] = $individual;
    }

    // GA loop
    for ($gen = 0; $gen < $generations; $gen++) {
        // Evaluate fitness
        $fitness_scores = array_map(function($pop) use ($conn, $semester) {
            return get_fitness($pop, $conn, $semester);
        }, $population);

        // Select parents
        $parents = [];
        $temp_scores = $fitness_scores;
        for ($i = 0; $i < $population_size / 2; $i++) {
            $parent_idx = array_keys($temp_scores, max($temp_scores))[0];
            $parents[] = $population[$parent_idx];
            unset($temp_scores[$parent_idx]);
        }

        // Crossover and mutation
        $new_population = [];
        for ($i = 0; $i < count($parents) - 1; $i += 2) {
            $child1 = crossover($parents[$i], $parents[$i + 1]);
            $child2 = crossover($parents[$i + 1], $parents[$i]);
            $new_population[] = $child1;
            $new_population[] = $child2;
        }

        // Mutation
        foreach ($new_population as $i => $pop) {
            if (rand(0, 100) < 10) { // 10% mutation rate
                $new_population[$i] = mutate($pop, $lecturers, $rooms, $time_slots);
            }
        }

        $population = array_merge($new_population, array_slice($parents, 0, $population_size - count($new_population)));
    }

    // Save best schedule
    $best_fitness = 0;
    $best_population = $population[0];
    foreach ($population as $pop) {
        $fitness = get_fitness($pop, $conn, $semester);
        if ($fitness > $best_fitness) {
            $best_fitness = $fitness;
            $best_population = $pop;
        }
    }

    // Clear previous schedules
    $conn->query("DELETE FROM schedules WHERE semester = '$semester'");

    // Save to database
    foreach ($best_population as $schedule) {
        $stmt = $conn->prepare("INSERT INTO schedules (course_id, lecturer_id, room_id, time_slot_id, semester) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiis", $schedule->course_id, $schedule->lecturer_id, $schedule->room_id, $schedule->time_slot_id, $semester);
        $stmt->execute();
        $stmt->close();
    }
    return true;
}

if (isset($_POST['generate'])) {
    $semester = $_POST['semester'];
    $success = generate_schedule($conn, $semester);
}

// Fetch schedules
$semester_filter = isset($_POST['semester']) ? $_POST['semester'] : 'odd';
$result = $conn->query("SELECT s.*, c.name as course_name, l.name as lecturer_name, r.name as room_name, t.day, t.start_time, t.end_time 
                        FROM schedules s 
                        JOIN courses c ON s.course_id = c.id 
                        JOIN lecturers l ON s.lecturer_id = l.id 
                        JOIN rooms r ON s.room_id = r.id 
                        JOIN time_slots t ON s.time_slot_id = t.id 
                        WHERE s.semester = '$semester_filter'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Schedule</title>
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
                    <li class="nav-item"><a class="nav-link" href="manage.php">Manage Entities</a></li>
                    <li class="nav-item"><a class="nav-link active" href="schedule.php">Generate Schedule</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Generate Schedule</h2>
        <?php if (isset($_POST['generate']) && !$success): ?>
            <div class="alert alert-danger">Failed to generate schedule. Ensure all entities (courses, lecturers, rooms, time slots) are populated.</div>
        <?php elseif (isset($_POST['generate']) && $success): ?>
            <div class="alert alert-success">Schedule generated successfully!</div>
        <?php endif; ?>
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <select name="semester" class="form-control" required>
                        <option value="odd" <?php if ($semester_filter == 'odd') echo 'selected'; ?>>Odd Semester</option>
                        <option value="even" <?php if ($semester_filter == 'even') echo 'selected'; ?>>Even Semester</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="generate" class="btn btn-success">Generate Schedule</button>
                </div>
                <div class="col-md-2">
                    <a href="export.php?semester=<?php echo $semester_filter; ?>" class="btn btn-primary">Export to Excel</a>
                </div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Lecturer</th>
                    <th>Room</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['lecturer_name']; ?></td>
                        <td><?php echo $row['room_name']; ?></td>
                        <td><?php echo $row['day']; ?></td>
                        <td><?php echo $row['start_time'] . ' - ' . $row['end_time']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>