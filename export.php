<?php
$conn = new mysqli("localhost", "root", "", "faculty_scheduling");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$semester = isset($_GET['semester']) ? $_GET['semester'] : 'odd';
$result = $conn->query("SELECT c.name as course_name, c.semester_number, s.class_label, l.name as lecturer_name, r.name as room_name, t.day, t.start_time, t.end_time 
                        FROM schedules s 
                        JOIN courses c ON s.course_id = c.id 
                        JOIN lecturers l ON s.lecturer_id = l.id 
                        JOIN rooms r ON s.room_id = r.id 
                        JOIN time_slots t ON s.time_slot_id = t.id 
                        WHERE s.semester = '$semester'");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="schedule_' . $semester . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Course', 'Semester Number', 'Class', 'Lecturer', 'Room', 'Day', 'Time Slot']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['course_name'],
        $row['semester_number'],
        $row['class_label'],
        $row['lecturer_name'],
        $row['room_name'],
        $row['day'],
        $row['start_time'] . ' - ' . $row['end_time']
    ]);
}

fclose($output);
$conn->close();
exit;
?>