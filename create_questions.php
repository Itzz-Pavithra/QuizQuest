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
    <title>Create Quiz - QuizQuest</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .create-container {
            padding-top: 80px;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .question-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .options-container {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1rem;
            align-items: center;
        }

        .option-input {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .correct-option {
            background: #e8f5e9;
            border-color: #4caf50;
        }

        .add-option-btn {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
        }

        .add-option-btn:hover {
            background: #27ae60;
        }

        .remove-option-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
        }

        .remove-option-btn:hover {
            background: #c0392b;
        }

        .preview-section {
            margin-top: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .preview-question {
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .error-message {
            color: var(--accent-color);
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        .success-message {
            color: var(--secondary-color);
            margin-top: 0.5rem;
            font-size: 0.9rem;
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
            <a href="quiz_game.php"><i class="fas fa-play"></i> Play</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="create-container">
        <h1 class="animate__animated animate__fadeIn"><i class="fas fa-plus-circle"></i> Create Quiz Question</h1>
        
        <form id="questionForm" class="question-form animate__animated animate__fadeInUp">
            <div class="form-group">
                <label for="domain">Domain</label>
                <select id="domain" name="domain" required>
                    <option value="">Select Domain</option>
                    <option value="science">Science</option>
                    <option value="history">History</option>
                    <option value="technology">Technology</option>
                    <option value="arts">Arts</option>
                    <option value="sports">Sports</option>
                    <option value="geography">Geography</option>
                </select>
            </div>

            <div class="form-group">
                <label for="difficulty">Difficulty Level</label>
                <select id="difficulty" name="difficulty" required>
                    <option value="">Select Difficulty</option>
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>

            <div class="form-group">
                <label for="question">Question</label>
                <textarea id="question" name="question" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>Options</label>
                <div id="optionsContainer">
                    <!-- Options Container Content -->
                    <div class="option-input">
                        <input type="text" name="options[]" placeholder="Option 1" required>
                        <input type="radio" name="correct" value="0" required>
                        <button type="button" class="remove-option-btn" disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="option-input">
                        <input type="text" name="options[]" placeholder="Option 2" required>
                        <input type="radio" name="correct" value="1" required>
                        <button type="button" class="remove-option-btn" disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <button type="button" id="addOptionBtn" class="add-option-btn">
                    <i class="fas fa-plus"></i> Add Option
                </button>
            </div>

            <div class="form-group">
                <label for="explanation">Explanation (Optional)</label>
                <textarea id="explanation" name="explanation" rows="2"></textarea>
                <small>Provide an explanation for the correct answer</small>
            </div>

            <button type="submit" class="btn primary-btn">
                <i class="fas fa-save"></i> Save Question
            </button>
        </form>

        <div id="preview" class="preview-section animate__animated animate__fadeIn" style="display: none;">
            <h2><i class="fas fa-eye"></i> Preview</h2>
            <div class="preview-question">
                <!-- Preview content will be inserted here -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('questionForm');
            const optionsContainer = document.getElementById('optionsContainer');
            const addOptionBtn = document.getElementById('addOptionBtn');
            const preview = document.getElementById('preview');
            let optionCount = 2;

            // Add new option
            addOptionBtn.addEventListener('click', () => {
                if (optionCount < 6) {
                    optionCount++;
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'option-input';
                    optionDiv.innerHTML = `
                        <input type="text" name="options[]" placeholder="Option ${optionCount}" required>
                        <input type="radio" name="correct" value="${optionCount - 1}" required>
                        <button type="button" class="remove-option-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    optionsContainer.appendChild(optionDiv);

                    if (optionCount >= 6) {
                        addOptionBtn.style.display = 'none';
                    }

                    // Add remove option functionality
                    const removeBtn = optionDiv.querySelector('.remove-option-btn');
                    removeBtn.addEventListener('click', () => {
                        optionsContainer.removeChild(optionDiv);
                        optionCount--;
                        addOptionBtn.style.display = 'block';
                        updateOptionNumbers();
                    });
                }
            });

            // Update option numbers
            function updateOptionNumbers() {
                const options = optionsContainer.querySelectorAll('.option-input input[type="text"]');
                options.forEach((option, index) => {
                    option.placeholder = `Option ${index + 1}`;
                });
            }

            // Form submission
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                try {
                    const response = await fetch('api/save_question.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showPreview(formData);
                        form.reset();
                        alert('Question saved successfully!');
                    } else {
                        alert(data.message || 'Error saving question');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });

            // Show preview
            function showPreview(formData) {
                const previewContent = document.querySelector('.preview-question');
                const question = formData.get('question');
                const options = formData.getAll('options[]');
                const correctIndex = formData.get('correct');
                
                let optionsHtml = '';
                options.forEach((option, index) => {
                    const isCorrect = index == correctIndex;
                    optionsHtml += `
                        <div class="option ${isCorrect ? 'correct-option' : ''}">
                            ${index + 1}. ${option}
                            ${isCorrect ? ' <i class="fas fa-check"></i>' : ''}
                        </div>
                    `;
                });

                previewContent.innerHTML = `
                    <h3>${question}</h3>
                    <div class="options">
                        ${optionsHtml}
                    </div>
                    <div class="explanation">
                        <strong>Explanation:</strong> ${formData.get('explanation') || 'No explanation provided'}
                    </div>
                `;

                preview.style.display = 'block';
                preview.scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
</body>
</html>