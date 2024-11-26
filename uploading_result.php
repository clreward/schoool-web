<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to calculate the division based on the total grade points
function calculate_division($total_grade_points) {
    if ($total_grade_points >= 7 && $total_grade_points <= 17) {
        return "I";
    } elseif ($total_grade_points >= 18 && $total_grade_points <= 21) {
        return "II";
    } elseif ($total_grade_points >= 22 && $total_grade_points <= 25) {
        return "III";
    } elseif ($total_grade_points >= 26 && $total_grade_points <= 32) {
        return "IV";
    } else {
        return "O";
    }
}

// Define the forms you want to display
$forms = [1, 2, 3, 4, 5, 6];

// Add Bootstrap CSS for styling
echo '<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">';

// Navigation menu with dynamic form links
echo '
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">School Management System</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Students</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Subjects</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Results</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Logout</a>
      </li>';

foreach ($forms as $form) {
    echo '<li class="nav-item">
            <a class="nav-link" href="?form=' . $form . '">Form ' . $form . '</a>
          </li>';
}

echo '
    </ul>
  </div>
</nav>
';

// Check if a form number is selected and display the corresponding data
if (isset($_GET['form']) && in_array($_GET['form'], $forms)) {
    $form = $_GET['form'];

    // Fetch all subjects for the selected form
    $sql_subjects = "
        SELECT s.subject_id, s.subject_name
        FROM subjects s
        JOIN student_subjects ss ON s.subject_id = ss.subject_id
        JOIN students st ON ss.student_id = st.registration_number
        WHERE st.form = ?
    ";

    if ($stmt_subjects = $conn->prepare($sql_subjects)) {
        $stmt_subjects->bind_param("i", $form);
        $stmt_subjects->execute();
        $result_subjects = $stmt_subjects->get_result();

        $subjects = [];
        while ($row = $result_subjects->fetch_assoc()) {
            $subjects[$row['subject_id']] = $row['subject_name'];
        }

        // Fetch students for the selected form
        $sql_students = "
            SELECT 
                s.registration_number, 
                CONCAT(s.first_name, ' ', s.middle_name, ' ', s.surname) AS names, 
                s.gender
            FROM students s
            WHERE s.form = ?
        ";

        if ($stmt_students = $conn->prepare($sql_students)) {
            $stmt_students->bind_param("i", $form);
            $stmt_students->execute();
            $result_students = $stmt_students->get_result();

            // Display the table for the selected form
            echo "<h2>Form $form</h2>";
            echo "<table class='table table-bordered'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Registration Number</th>";
            echo "<th>Names</th>";
            echo "<th>Gender</th>";
            echo "<th>Total Marks</th>";
            echo "<th>Total Grade Points</th>";
            echo "<th>Division</th>";
            echo "<th>Rank</th>";

            // Add columns for each subject
            foreach ($subjects as $subject_name) {
                echo "<th colspan='2'>$subject_name</th>";
            }
            echo "</tr>";

            echo "<tr>";
            echo "<th></th>";
            echo "<th></th>";
            echo "<th></th>";
            echo "<th></th>";
            echo "<th></th>";
            echo "<th></th>";
            echo "<th></th>";

            // Add sub-headers for each subject (Marks and Grade)
            foreach ($subjects as $subject_name) {
                echo "<th>Marks</th>";
                echo "<th>Grade</th>";
            }
            echo "</tr>";
            echo "</thead>";

            echo "<tbody>";

            $students_data = [];

            // Loop through students to calculate total marks and grades
            while ($student = $result_students->fetch_assoc()) {
                $student_id = $student['registration_number'];
                $student_name = $student['names'];
                $gender = $student['gender'];

                $total_marks = 0;
                $grades = [];
                $total_possible_marks = 0; // Assuming each subject has the same max marks
                $subjects_with_marks = 0; // Counter for subjects with marks

                // Loop through subjects and get marks
                foreach ($subjects as $subject_id => $subject_name) {
                    $sql_results = "
                        SELECT 
                            test_marks, 
                            midterm_marks, 
                            final_marks, 
                            (COALESCE(test_marks, 0) + COALESCE(midterm_marks, 0) + COALESCE(final_marks, 0)) AS total
                        FROM student_results
                        WHERE student_id = ? AND subject_id = ? 
                    ";

                    if ($stmt_results = $conn->prepare($sql_results)) {
                        $stmt_results->bind_param("si", $student_id, $subject_id);
                        $stmt_results->execute();
                        $result_results = $stmt_results->get_result();
                        $result_data = $result_results->fetch_assoc();

                        $subject_total_marks = 0;
                        $grade = '';
                        if ($result_data && $result_data['final_marks'] !== null) {
                            $subject_total_marks = $result_data['total'];
                            $subjects_with_marks++;

                            // Calculate grade
                            if ($subject_total_marks >= 75) {
                                $grade = 'A';
                            } elseif ($subject_total_marks >= 60) {
                                $grade = 'B';
                            } elseif ($subject_total_marks >= 45) {
                                $grade = 'C';
                            } elseif ($subject_total_marks >= 30) {
                                $grade = 'D';
                            } else {
                                $grade = 'F';
                            }
                        } else {
                            $subject_total_marks = 'inc';
                            $grade = '';
                        }

                        $total_marks += is_numeric($subject_total_marks) ? $subject_total_marks : 0;
                        $grades[$subject_name] = ['marks' => $subject_total_marks, 'grade' => $grade];

                        $stmt_results->close();
                    }
                }

                // Calculate total grade points from top 7 grades
                $grade_points = [
                    'A' => 1,
                    'B' => 2,
                    'C' => 3,
                    'D' => 4,
                    'F' => 5
                ];

                $all_grade_points = [];
                foreach ($grades as $subject_name => $data) {
                    $grade = $data['grade'];
                    if ($grade && isset($grade_points[$grade])) {
                        $all_grade_points[] = $grade_points[$grade];
                    }
                }

                // Sort grade points in ascending order (best grades first)
                sort($all_grade_points);

                // Sum up the top 7 grade points
                $top_seven_grades = array_slice($all_grade_points, 0, 7);
                $total_grade_points = array_sum($top_seven_grades);

                // Get division
                $division = ($subjects_with_marks >= 7) ? calculate_division($total_grade_points) : "INC";

                $students_data[] = [
                    'student_id' => $student_id,
                    'name' => $student_name,
                    'gender' => $gender,
                    'total_marks' => $total_marks,
                    'total_grade_points' => $total_grade_points,
                    'division' => $division,
                    'grades' => $grades
                ];
            }

            // Sort students by total marks (for rank calculation)
            usort($students_data, function($a, $b) {
                return $b['total_marks'] - $a['total_marks'];
            });

            $rank = 1;

            // Display student data
            foreach ($students_data as $student_data) {
                echo "<tr>";
                echo "<td>{$student_data['student_id']}</td>";
                echo "<td>{$student_data['name']}</td>";
                echo "<td>{$student_data['gender']}</td>";
                echo "<td>{$student_data['total_marks']}</td>";
                echo "<td>{$student_data['total_grade_points']}</td>";
                echo "<td>{$student_data['division']}</td>";
                echo "<td>{$rank}</td>";
                $rank++;

                foreach ($subjects as $subject_name) {
                    $subject_data = $student_data['grades'][$subject_name] ?? ['marks' => 'inc', 'grade' => ''];
                    echo "<td>{$subject_data['marks']}</td>";
                    echo "<td>{$subject_data['grade']}</td>";
                }

                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";

            $stmt_students->close();
        }
    }
}

$conn->close();
?>
