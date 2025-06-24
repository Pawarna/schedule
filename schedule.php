<?php
$conn = new mysqli("localhost", "root", "", "faculty_scheduling");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


class Schedule {
    public $course_id, $lecturer_id, $room_id, $time_slot_id, $class_label;
    public function __construct($course_id, $lecturer_id, $room_id, $time_slot_id, $class_label) {
        $this->course_id = $course_id;
        $this->lecturer_id = $lecturer_id;
        $this->room_id = $room_id;
        $this->time_slot_id = $time_slot_id;
        $this->class_label = $class_label;
    }
}

function get_fitness($population, $conn, $semester, $course_class_counts) {
    $conflicts = 0;
    $room_usage = [];
    $lecturer_usage = [];
    $capacity_issues = [];
    foreach ($population as $schedule) {
        $key = "{$schedule->time_slot_id}-{$schedule->room_id}";
        $lkey = "{$schedule->time_slot_id}-{$schedule->lecturer_id}";
        if (isset($room_usage[$key])) $conflicts++;
        if (isset($lecturer_usage[$lkey])) $conflicts += 2;
        $room_usage[$key] = true;
        $lecturer_usage[$lkey] = true;

        $result = $conn->query("SELECT r.capacity, 
                                       CASE c.semester_number 
                                           WHEN 1 THEN p.student_count_sem1 
                                           WHEN 2 THEN p.student_count_sem2 
                                           WHEN 3 THEN p.student_count_sem3 
                                           WHEN 4 THEN p.student_count_sem4 
                                           WHEN 5 THEN p.student_count_sem5 
                                           WHEN 6 THEN p.student_count_sem6 
                                       END AS student_count,
                                       c.name as course_name
                                FROM rooms r 
                                JOIN courses c ON c.id = {$schedule->course_id}
                                JOIN programs p ON c.program_id = p.id 
                                WHERE r.id = {$schedule->room_id} AND c.semester = '$semester'");
        if ($result && $data = $result->fetch_assoc()) {
            $num_classes = $course_class_counts[$schedule->course_id]['num_classes'];
            $students_per_class = ceil($data['student_count'] / $num_classes);
            if ($students_per_class > $data['capacity']) {
                $conflicts += 0.5; // Further reduced penalty
                $capacity_issues[] = "{$data['course_name']} (Class {$schedule->class_label}, ~{$students_per_class} students, needs >{$data['capacity']} capacity)";
            }
        }

        $result = $conn->query("SELECT lecturer_id FROM courses WHERE id = {$schedule->course_id}");
        if ($result && $data = $result->fetch_assoc()) {
            if ($schedule->lecturer_id != $data['lecturer_id']) {
                $conflicts += 3;
            }
        }
    }
    return [
        'fitness' => $conflicts == 0 ? 1000 : 1000 / (1 + $conflicts),
        'capacity_issues' => $capacity_issues
    ];
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

function mutate($population, $rooms, $time_slots) {
    $index = rand(0, count($population) - 1);
    $schedule = $population[$index];
    $schedule->room_id = $rooms[array_rand($rooms)]['id'];
    $schedule->time_slot_id = $time_slots[array_rand($time_slots)]['id'];
    $population[$index] = $schedule;
    return $population;
}

function generate_schedule($conn, $semester) {
    $start_time = microtime(true);
    $courses = $conn->query("SELECT c.id, c.program_id, c.lecturer_id, c.semester_number, 
                                    CASE c.semester_number 
                                        WHEN 1 THEN p.student_count_sem1 
                                        WHEN 2 THEN p.student_count_sem2 
                                        WHEN 3 THEN p.student_count_sem3 
                                        WHEN 4 THEN p.student_count_sem4 
                                        WHEN 5 THEN p.student_count_sem5 
                                        WHEN 6 THEN p.student_count_sem6 
                                    END AS student_count,
                                    c.name as course_name
                             FROM courses c 
                             JOIN programs p ON c.program_id = p.id 
                             WHERE c.semester = '$semester'")->fetch_all(MYSQLI_ASSOC);
    $rooms = $conn->query("SELECT id, capacity FROM rooms")->fetch_all(MYSQLI_ASSOC);
    $time_slots = $conn->query("SELECT id FROM time_slots")->fetch_all(MYSQLI_ASSOC);

    // Get min and max room capacity
    $min_room_capacity = min(array_column($rooms, 'capacity'));
    $max_room_capacity = max(array_column($rooms, 'capacity'));

    $total_classes = 0;
    $lecturer_load = [];
    $course_class_counts = [];
    $semester_numbers = $semester == 'odd' ? [1, 3, 5] : [2, 4, 6];
    foreach ($courses as $course) {
        if (in_array($course['semester_number'], $semester_numbers)) {
            // Ensure students_per_class fits within min_room_capacity
            $num_classes = max(1, ceil($course['student_count'] / $min_room_capacity));
            $students_per_class = ceil($course['student_count'] / $num_classes);
            // Adjust if still too large for max_room_capacity
            while ($students_per_class > $max_room_capacity && $num_classes < 10) {
                $num_classes++;
                $students_per_class = ceil($course['student_count'] / $num_classes);
            }
            $course_class_counts[$course['id']] = [
                'num_classes' => $num_classes,
                'lecturer_id' => $course['lecturer_id'],
                'course_name' => $course['course_name'],
                'student_count' => $course['student_count'],
                'students_per_class' => $students_per_class
            ];
            $total_classes += $num_classes;
            $lecturer_load[$course['lecturer_id']] = ($lecturer_load[$course['lecturer_id']] ?? 0) + $num_classes;
        }
    }
    $available_slots = count($rooms) * count($time_slots);
    $overloaded_lecturers = [];
    foreach ($lecturer_load as $lecturer_id => $load) {
        if ($load > count($time_slots)) {
            $lecturer_name = $conn->query("SELECT name FROM lecturers WHERE id = $lecturer_id")->fetch_assoc()['name'];
            $overloaded_lecturers[] = "$lecturer_name ($load classes)";
        }
    }
    $capacity_issues = [];
    foreach ($course_class_counts as $course_id => $info) {
        if ($info['students_per_class'] > $max_room_capacity) {
            $capacity_issues[] = "{$info['course_name']} ({$info['student_count']} students, needs >$max_room_capacity capacity)";
        }
    }
    if (empty($courses) || empty($rooms) || empty($time_slots) || !empty($overloaded_lecturers) || $available_slots < $total_classes || !empty($capacity_issues)) {
        $message = "Sumber daya tidak cukup: ";
        if (empty($courses)) $message .= "Tidak ada mata kuliah. ";
        if (empty($rooms)) $message .= "Tidak ada ruangan. ";
        if (empty($time_slots)) $message .= "Tidak ada slot waktu. ";
        if (!empty($overloaded_lecturers)) $message .= "Dosen kelebihan beban: " . implode(", ", $overloaded_lecturers) . ". Maksimal " . count($time_slots) . " slot per dosen. ";
        if ($available_slots < $total_classes) $message .= "Butuh lebih banyak ruangan/slot waktu ($total_classes kelas, $available_slots slot tersedia). ";
        if (!empty($capacity_issues)) $message .= "Masalah kapasitas: " . implode("; ", $capacity_issues) . ". ";
        $message .= "Jumlah kelas per mata kuliah: ";
        foreach ($course_class_counts as $course_id => $info) {
            $message .= "{$info['course_name']} ({$info['num_classes']} kelas, ~{$info['students_per_class']} siswa per kelas), ";
        }
        return ["success" => false, "message" => rtrim($message, ", "), "generation" => 0];
    }

    $population_size = 150;
    $generations = 1000; // Increased for better convergence
    $mutation_rate = 30;
    $population = [];
    $individual = [];
    foreach ($courses as $course) {
        if (in_array($course['semester_number'], $semester_numbers)) {
            $num_classes = $course_class_counts[$course['id']]['num_classes'];
            for ($class_num = 0; $class_num < $num_classes; $class_num++) {
                $class_label = chr(65 + $class_num); // A, B, C, etc.
                $individual[] = new Schedule(
                    $course['id'],
                    $course['lecturer_id'],
                    $rooms[array_rand($rooms)]['id'],
                    $time_slots[array_rand($time_slots)]['id'],
                    $class_label
                );
            }
        }
    }
    for ($i = 0; $i < $population_size; $i++) {
        $population[] = array_map(function($schedule) {
            return clone $schedule;
        }, $individual);
    }

    $best_fitness = 0;
    $best_population = $population[0];
    $best_capacity_issues = [];
    for ($gen = 0; $gen < $generations; $gen++) {
        $fitness_results = array_map(function($pop) use ($conn, $semester, $course_class_counts) {
            return get_fitness($pop, $conn, $semester, $course_class_counts);
        }, $population);

        $fitness_scores = array_map(function($result) {
            return $result['fitness'];
        }, $fitness_results);

        $parents = [];
        $temp_scores = $fitness_scores;
        for ($i = 0; $i < $population_size / 2; $i++) {
            $parent_idx = array_keys($temp_scores, max($temp_scores))[0];
            $parents[] = $population[$parent_idx];
            unset($temp_scores[$parent_idx]);
        }

        $new_population = [];
        for ($i = 0; $i < count($parents) - 1; $i += 2) {
            $child1 = crossover($parents[$i], $parents[$i + 1]);
            $child2 = crossover($parents[$i + 1], $parents[$i]);
            $new_population[] = $child1;
            $new_population[] = $child2;
        }

        foreach ($new_population as $i => $pop) {
            if (rand(0, 100) < $mutation_rate) {
                $new_population[$i] = mutate($pop, $rooms, $time_slots);
            }
        }

        $population = array_merge($new_population, array_slice($parents, 0, $population_size - count($new_population)));

        $current_best_idx = array_search(max($fitness_scores), $fitness_scores);
        $current_best_fitness = $fitness_scores[$current_best_idx];
        if ($current_best_fitness > $best_fitness) {
            $best_fitness = $current_best_fitness;
            $best_population = $population[$current_best_idx];
            $best_capacity_issues = $fitness_results[$current_best_idx]['capacity_issues'];
        }

        $execution_time = microtime(true) - $start_time;
        if ($execution_time > 18) { // Adjusted for increased generations
            $message = "Generasi timeout setelah $gen generasi (fitness terbaik: $best_fitness). ";
            if (!empty($best_capacity_issues)) {
                $message .= "Masalah kapasitas: " . implode("; ", $best_capacity_issues) . ". ";
            }
            $message .= "Jumlah kelas: ";
            foreach ($course_class_counts as $course_id => $info) {
                $message .= "{$info['course_name']} ({$info['num_classes']} kelas, ~{$info['students_per_class']} siswa per kelas), ";
            }
            $message .= "Coba tambah kapasitas ruangan (>50), tambah ruangan/slot waktu, atau kurangi jumlah siswa.";
            return [
                "success" => false,
                "message" => $message,
                "generation" => $gen
            ];
        }

        if ($best_fitness == 1000) {
            break;
        }
    }

    if ($best_fitness < 1000) {
        $message = "Gagal menemukan jadwal tanpa konflik setelah $generations generasi (fitness terbaik: $best_fitness). Konflik: ";
        $room_usage = [];
        $lecturer_usage = [];
        foreach ($best_population as $schedule) {
            $key = "{$schedule->time_slot_id}-{$schedule->room_id}";
            $lkey = "{$schedule->time_slot_id}-{$schedule->lecturer_id}";
            if (isset($room_usage[$key])) {
                $course_name = $conn->query("SELECT name FROM courses WHERE id = {$schedule->course_id}")->fetch_assoc()['name'];
                $message .= "Konflik ruangan untuk $course_name (Class {$schedule->class_label}) pada slot waktu {$schedule->time_slot_id}, ";
            }
            if (isset($lecturer_usage[$lkey])) {
                $course_name = $conn->query("SELECT name FROM courses WHERE id = {$schedule->course_id}")->fetch_assoc()['name'];
                $message .= "Konflik dosen untuk $course_name (Class {$schedule->class_label}) pada slot waktu {$schedule->time_slot_id}, ";
            }
            $room_usage[$key] = true;
            $lecturer_usage[$lkey] = true;
        }
        if (!empty($best_capacity_issues)) {
            $message .= "Masalah kapasitas: " . implode("; ", $best_capacity_issues) . ". ";
        }
        $message .= "Jumlah kelas: ";
        foreach ($course_class_counts as $course_id => $info) {
            $message .= "{$info['course_name']} ({$info['num_classes']} kelas, ~{$info['students_per_class']} siswa per kelas), ";
        }
        return ["success" => false, "message" => rtrim($message, ", "), "generation" => $generations];
    }

    $conn->query("DELETE FROM schedules WHERE semester = '$semester'");
    foreach ($best_population as $schedule) {
        $stmt = $conn->prepare("INSERT INTO schedules (course_id, lecturer_id, room_id, time_slot_id, semester, class_label) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiss", $schedule->course_id, $schedule->lecturer_id, $schedule->room_id, $schedule->time_slot_id, $semester, $schedule->class_label);
        $stmt->execute();
        $stmt->close();
    }
    $message = "Jadwal berhasil dibuat! Jumlah kelas: ";
    foreach ($course_class_counts as $course_id => $info) {
        $message .= "{$info['course_name']} ({$info['num_classes']} kelas, ~{$info['students_per_class']} siswa per kelas), ";
    }
    return ["success" => true, "message" => rtrim($message, ", "), "generation" => $gen + 1];
}

if (isset($_POST['generate'])) {
    $semester = $_POST['semester'];
    $result = generate_schedule($conn, $semester);
    echo json_encode($result);
    exit;
}

$semester_filter = isset($_POST['semester']) ? $_POST['semester'] : 'odd';
$result = $conn->query("SELECT s.*, c.name as course_name, c.semester_number, l.name as lecturer_name, r.name as room_name, t.day, t.start_time, t.end_time 
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
    <style>
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .modal-body {
            text-align: center;
            padding: 2rem;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
            margin-bottom: 1rem;
        }
    </style>
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
        <div id="alertContainer"></div>
        <form id="generateForm" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <select name="semester" id="semester" class="form-control" required>
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
                    <th>Semester Number</th>
                    <th>Class</th>
                    <th>Lecturer</th>
                    <th>Room</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody id="scheduleTable">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['semester_number']; ?></td>
                        <td><?php echo $row['class_label']; ?></td>
                        <td><?php echo $row['lecturer_name']; ?></td>
                        <td><?php echo $row['room_name']; ?></td>
                        <td><?php echo $row['day']; ?></td>
                        <td><?php echo $row['start_time'] . ' - ' . $row['end_time']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="spinner-border text-primary" role="status"></div>
                    <h5>Generating Schedule...</h5>
                    <p>Generation: <span id="generationCounter">0</span>/500</p>
                    <button id="cancelGeneration" class="btn btn-danger mt-2">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let generationInterval;
            let xhr;

            function startGenerationCounter(maxGenerations) {
                let generation = 0;
                $('#generationCounter').text(generation);
                generationInterval = setInterval(() => {
                    generation++;
                    if (generation <= maxGenerations) {
                        $('#generationCounter').text(generation);
                    }
                }, 15);
            }

            function stopGenerationCounter() {
                clearInterval(generationInterval);
            }

            function stopGeneration() {
                stopGenerationCounter();
                if (xhr) {
                    xhr.abort();
                }
                $('#loadingModal').modal('hide');
                $('#alertContainer').html(
                    '<div class="alert alert-warning">Pembuatan jadwal dibatalkan.</div>'
                );
            }

            $('#generateForm').on('submit', function(e) {
                e.preventDefault();
                $('#loadingModal').modal({ backdrop: 'static', keyboard: false });
                $('#loadingModal').modal('show');
                $('#alertContainer').empty();
                startGenerationCounter(500);

                xhr = $.ajax({
                    url: 'schedule.php',
                    type: 'POST',
                    data: $(this).serialize() + '&generate=1',
                    dataType: 'json',
                    success: function(response) {
                        stopGenerationCounter();
                        $('#loadingModal').modal('hide');
                        $('#alertContainer').html(
                            `<div class="alert ${response.success ? 'alert-success' : 'alert-danger'}">${response.message} (Generasi: ${response.generation})</div>`
                        );
                        if (response.success) {
                            $.ajax({
                                url: 'schedule.php',
                                type: 'POST',
                                data: { semester: $('#semester').val() },
                                success: function(html) {
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(html, 'text/html');
                                    const newTable = $(doc).find('#scheduleTable').html();
                                    $('#scheduleTable').html(newTable);
                                }
                            });
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        if (textStatus !== 'abort') {
                            stopGenerationCounter();
                            $('#loadingModal').modal('hide');
                            $('#alertContainer').html(
                                '<div class="alert alert-danger">Terjadi kesalahan saat membuat jadwal. Coba lagi atau cek log server.</div>'
                            );
                        }
                    }
                });
            });

            $('#cancelGeneration').on('click', function() {
                stopGeneration();
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>