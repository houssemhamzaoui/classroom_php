<?php
// Start the session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo "Unauthorized access. Please log in as a teacher.";
    exit();
}
$conn = new mysqli('localhost', 'root', 'hosshoss', 'projet');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connexion à la base de données

    // Ajouter le quiz
    $title = $_POST['quiz_title'];
    $deadline = $_POST['deadline'];
    $course_id = (int)$_POST['course_id']; // Cast course_id to integer
    $stmt = $conn->prepare("INSERT INTO quiz (title, deadline, course_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $deadline, $course_id);
    $stmt->execute();
    $quiz_id = $conn->insert_id;

    // Ajouter les questions et réponses
    foreach ($_POST['questions'] as $index => $question_text) {
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
        $stmt->bind_param("is", $quiz_id, $question_text);
        $stmt->execute();
        $question_id = $conn->insert_id;

        foreach ($_POST['choices'][$index] as $choice_index => $choice_text) {
            $is_correct = isset($_POST['correct'][$index][$choice_index]) ? 1 : 0;
            $stmt = $conn->prepare("INSERT INTO choices (question_id, choice_text, is_correct) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $question_id, $choice_text, $is_correct);
            $stmt->execute();
        }
    }

    echo "Quiz créé avec succès !";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Créer un Quiz</title>
</head>
<body>
    <h1>Créer un nouveau quiz</h1>
    <form method="POST">
    <label>Titre du Quiz:</label>
    <input type="text" name="quiz_title" id="deadline" required><br><br>

    <label>Date limite:</label>
    <input type="datetime-local" name="deadline" required><br><br>

    <label>Cours associé:</label>
    <select name="course_id" required >
        <option value="" disabled selected>-- Sélectionnez un cours --</option>
        <?php
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id_teacher = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($course = $result->fetch_assoc()) {
        echo "<option value='{$course['id']}'>" . htmlspecialchars($course['name']) . "</option>";
}
?>

    </select><br><br>
    <div id="questions-container">
        <h3>Questions:</h3>
        <!-- Question 1 -->
        <div class="question">
            <label>Question 1:</label>
            <input type="text" name="questions[]" required><br>

            <div class="choices">
                <label>Choix 1:</label>
                <input type="text" name="choices[0][]" required>
                <input type="checkbox" name="correct[0][0]"> Correct<br>

                <label>Choix 2:</label>
                <input type="text" name="choices[0][]" required>
                <input type="checkbox" name="correct[0][1]"> Correct<br>
            </div>
        </div>
    </div>

    <button type="button" onclick="addQuestion()">Ajouter une question</button><br><br>
    <button type="submit">Créer Quiz</button>
</form>


    <script>
        let questionCount = 1;

        function addQuestion() {
            const container = document.getElementById('questions-container');
            questionCount++;

            const questionDiv = document.createElement('div');
            questionDiv.className = 'question';

            questionDiv.innerHTML = `
                <label>Question ${questionCount}:</label>
                <input type="text" name="questions[]" required><br>
                <div class="choices">
                    <label>Choix 1:</label>
                    <input type="text" name="choices[${questionCount - 1}][]" required>
                    <input type="checkbox" name="correct[${questionCount - 1}][0]"> Correct<br>

                    <label>Choix 2:</label>
                    <input type="text" name="choices[${questionCount - 1}][]" required>
                    <input type="checkbox" name="correct[${questionCount - 1}][1]"> Correct<br>
                </div>
            `;
            container.appendChild(questionDiv);
        }
        function setDefaultDeadline() {
        const now = new Date();
        now.setMinutes(now.getMinutes() + 10);

        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const date = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        const formattedDeadline = `${year}-${month}-${date}T${hours}:${minutes}`;
        document.getElementById('deadline').value = formattedDeadline;
    }

    // Set the default deadline when the page loads
    window.onload = setDefaultDeadline;
    </script>
</body>
</html>
