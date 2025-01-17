<?php
session_start();
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', 'hosshoss', 'projet');


$quiz_id = (int) $_GET['quiz_id']; 
$result = $conn->query("SELECT * FROM quiz WHERE id = $quiz_id AND deadline > NOW()");
$quiz = $result->fetch_assoc();

if (!$quiz) {
    die("Ce quiz n'existe pas ou est déjà expiré.");
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['user_id']; // Remplace par l'authentification réelle de l'étudiant

    foreach ($_POST['answers'] as $question_id => $choice_id) {
        $stmt = $conn->prepare("INSERT INTO student_answers (quiz_id, student_id, question_id, choice_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $quiz_id, $student_id, $question_id, $choice_id);
        $stmt->execute();
    }

    echo "Merci ! Vos réponses ont été soumises.";
    exit;
}

// Récupère les questions et les choix du quiz
$questions = $conn->query("SELECT * FROM questions WHERE quiz_id = $quiz_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Répondre au Quiz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
    <p>Date limite : <?php echo htmlspecialchars($quiz['deadline']); ?></p>

    <form method="POST">
        <?php while ($question = $questions->fetch_assoc()): ?>
            <div class="question">
                <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
                <?php
                $choices = $conn->query("SELECT * FROM choices WHERE question_id = " . $question['id']);
                while ($choice = $choices->fetch_assoc()):
                ?>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo $choice['id']; ?>" required>
                        <?php echo htmlspecialchars($choice['choice_text']); ?>
                    </label><br>
                <?php endwhile; ?>
            </div>
        <?php endwhile; ?>

        <button type="submit">Soumettre mes réponses</button>
    </form>
</body>
</html>
