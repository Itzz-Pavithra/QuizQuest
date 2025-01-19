<?php
session_start();
require_once('connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$db = Database::getInstance();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Quiz - QuizQuest</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .quiz-container {
            padding-top: 80px;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .quiz-setup {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .quiz-question {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: none;
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .timer {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: #eee;
            border-radius: 5px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            background: var(--primary-color);
            width: 0%;
            transition: width 0.3s ease;
        }

        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1rem;
        }

        .option-btn {
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: left;
        }

        .option-btn:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .option-btn.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .option-btn.correct {
            background: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
        }

        .option-btn.incorrect {
            background: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
        }

        .quiz-results {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            display: none;
        }

        .results-header {
            margin-bottom: 2rem;
        }

        .score-circle {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 10px solid var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 2rem;
        }

        .results-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            padding: 1rem;
            background: var(--light-color);
            border-radius: 5px;
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fas fa-brain"></i> QuizQuest
        </div>
        <div class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="quiz-container">
        <!-- Quiz Setup Section -->
        <div id="quizSetup" class="quiz-setup animate__animated animate__fadeIn">
            <h2><i class="fas fa-gamepad"></i> Start New Quiz</h2>
            <form id="setupForm">
                <div class="form-group">
                    <label for="domain">Select Domain</label>
                    <select id="domain" name="domain" required>
                        <option value="">Choose a domain</option>
                        <option value="science">Science</option>
                        <option value="history">History</option>
                        <option value="technology">Technology</option>
                        <option value="arts">Arts</option>
                        <option value="sports">Sports</option>
                        <option value="geography">Geography</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="difficulty">Select Difficulty</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="">Choose difficulty</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>

                <button type="submit" class="btn primary-btn">
                    <i class="fas fa-play"></i> Start Quiz
                </button>
            </form>
        </div>

        <!-- Quiz Question Section -->
        <div id="quizQuestion" class="quiz-question">
            <div class="question-header">
                <div class="question-info">
                    <span class="question-number">Question 1/10</span>
                    <span class="difficulty">Medium</span>
                </div>
                <div class="timer">
                    <i class="fas fa-clock"></i> <span id="timeLeft">30</span>s
                </div>
            </div>

            <div class="progress-bar">
                <div class="progress"></div>
            </div>

            <h3 id="questionText"></h3>
            <div class="options-grid" id="optionsContainer"></div>
        </div>

        <!-- Quiz Results Section -->
        <div id="quizResults" class="quiz-results">
            <div class="results-header">
                <h2><i class="fas fa-trophy"></i> Quiz Complete!</h2>
                <p>Here's how you did:</p>
            </div>

            <div class="score-circle">
                <span id="finalScore">85%</span>
            </div>

            <div class="results-stats">
                <div class="stat-item">
                    <h4>Correct Answers</h4>
                    <p id="correctAnswers">8/10</p>
                </div>
                <div class="stat-item">
                    <h4>Time Taken</h4>
                    <p id="timeTaken">2:45</p>
                </div>
                <div class="stat-item">
                    <h4>Points Earned</h4>
                    <p id="pointsEarned">250</p>
                </div>
            </div>

            <div class="actions">
                <button class="btn primary-btn" onclick="restartQuiz()">
                    <i class="fas fa-redo"></i> Try Again
                </button>
                <button class="btn secondary-btn" onclick="viewLeaderboard()">
                    <i class="fas fa-trophy"></i> View Leaderboard
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentQuestion = 0;
        let score = 0;
        let timer;
        let questions = [];

        // Start quiz
        document.getElementById('setupForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            try {
                const response = await fetch('api/get_questions.php', {
                    method: 'POST',
                    body: formData
                });
                
                questions = await response.json();
                
                if (questions.length > 0) {
                    document.getElementById('quizSetup').style.display = 'none';
                    document.getElementById('quizQuestion').style.display = 'block';
                    startQuiz();
                } else {
                    alert('No questions available for selected criteria');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading questions');
            }
        });

        function startQuiz() {
            showQuestion(0);
            startTimer();
        }

        function showQuestion(index) {
            const question = questions[index];
            const questionElem = document.getElementById('questionText');
            const optionsContainer = document.getElementById('optionsContainer');
            const questionNumber = document.querySelector('.question-number');
            const progressBar = document.querySelector('.progress');
            
            // Update question number and progress
            questionNumber.textContent = `Question ${index + 1}/${questions.length}`;
            progressBar.style.width = `${((index + 1) / questions.length) * 100}%`;
            
            // Set question text
            questionElem.textContent = question.question;
            
            // Clear previous options
            optionsContainer.innerHTML = '';
            
            // Add options
            question.options.forEach((option, i) => {
                const button = document.createElement('button');
                button.className = 'option-btn animate__animated animate__fadeIn';
                button.textContent = option;
                button.onclick = () => selectOption(i);
                optionsContainer.appendChild(button);
            });
        }

        function selectOption(index) {
            const options = document.querySelectorAll('.option-btn');
            
            // Remove previous selection
            options.forEach(opt => opt.classList.remove('selected'));
            
            // Add selection to clicked option
            options[index].classList.add('selected');
            
            // Automatically move to next question after brief delay
            setTimeout(() => {
                checkAnswer(index);
            }, 1000);
        }

        function checkAnswer(selectedIndex) {
            const question = questions[currentQuestion];
            const options = document.querySelectorAll('.option-btn');
            
            // Show correct and incorrect answers
            options[question.correct_answer].classList.add('correct');
            
            if (selectedIndex !== question.correct_answer) {
                options[selectedIndex].classList.add('incorrect');
            } else {
                score++;
            }
            
            // Wait before moving to next question
            setTimeout(() => {
                if (currentQuestion < questions.length - 1) {
                    currentQuestion++;
                    showQuestion(currentQuestion);
                } else {
                    showResults();
                }
            }, 1500);
        }

        function startTimer() {
            let timeLeft = 30;
            const timerDisplay = document.getElementById('timeLeft');
            
            timer = setInterval(() => {
                timeLeft--;
                timerDisplay.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    // Auto-submit current question if time runs out
                    const selectedOption = document.querySelector('.option-btn.selected');
                    if (!selectedOption) {
                        checkAnswer(-1); // -1 indicates no answer selected
                    }
                }
            }, 1000);
        }

        function showResults() {
            clearInterval(timer);
            
            const quizQuestion = document.getElementById('quizQuestion');
            const quizResults = document.getElementById('quizResults');
            
            quizQuestion.style.display = 'none';
            quizResults.style.display = 'block';
            
            // Calculate and display results
            const percentage = Math.round((score / questions.length) * 100);
            document.getElementById('finalScore').textContent = `${percentage}%`;
            document.getElementById('correctAnswers').textContent = `${score}/${questions.length}`;
            
            // Calculate points based on difficulty and accuracy
            const difficultyMultiplier = {
                'easy': 1,
                'medium': 1.5,
                'hard': 2
            }[document.getElementById('difficulty').value];
            
            const points = Math.round(score * 100 * difficultyMultiplier);
            document.getElementById('pointsEarned').textContent = points;
            
            // Save results to database
            saveResults(points, percentage);
        }

        async function saveResults(points, percentage) {
            try {
                const response = await fetch('api/save_results.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        points,
                        percentage,
                        domain: document.getElementById('domain').value,
                        difficulty: document.getElementById('difficulty').value
                    })
                });
                
                const data = await response.json();
                if (!data.success) {
                    console.error('Error saving results:', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function restartQuiz() {
            currentQuestion = 0;
            score = 0;
            document.getElementById('quizResults').style.display = 'none';
            document.getElementById('quizSetup').style.display = 'block';
        }

        function viewLeaderboard() {
            window.location.href = 'leaderboard.php';
        }
    </script>
</body>
</html>