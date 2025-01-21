<?php
session_start();
require_once('connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$db = Database::getInstance()->getDB();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz - QuizQuest</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <h1 class="animate__animated animate__fadeIn">
            <i class="fas fa-plus-circle"></i> Create Quiz Question
        </h1>
        
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
                <textarea id="question" name="question" rows="3" required 
                    placeholder="Enter your question here..."></textarea>
            </div>

            <div class="form-group">
                <label>Options</label>
                <div id="optionsContainer">
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
                <textarea id="explanation" name="explanation" rows="2" 
                    placeholder="Explain why the correct answer is right..."></textarea>
                <small>Provide an explanation for the correct answer</small>
            </div>

            <div class="loading-spinner">
                <i class="fas fa-spinner fa-2x"></i>
            </div>

            <button type="submit" class="btn primary-btn">
                <i class="fas fa-save"></i> Save Question
            </button>
        </form>

        <div id="preview" class="preview-section animate__animated animate__fadeIn" style="display: none;">
            <h2><i class="fas fa-eye"></i> Preview</h2>
            <div class="preview-question"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('questionForm');
            const optionsContainer = document.getElementById('optionsContainer');
            const addOptionBtn = document.getElementById('addOptionBtn');
            const preview = document.getElementById('preview');
            const loadingSpinner = document.querySelector('.loading-spinner');
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
                
                try {
                    loadingSpinner.style.display = 'block';
                    const formData = new FormData(form);
                    
                    // Create the question data object
                    const questionData = {
                        domain: formData.get('domain'),
                        difficulty: formData.get('difficulty'),
                        question: formData.get('question'),
                        options: formData.getAll('options[]'),
                        correct_answer: parseInt(formData.get('correct')),
                        explanation: formData.get('explanation'),
                        created_by: <?php echo json_encode($_SESSION['user_id']); ?>,
                        created_at: new Date().toISOString()
                    };

                    const response = await fetch('api/save_question.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(questionData)
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showPreview(formData);
                        form.reset();
                        showMessage('Question saved successfully!', 'success');
                    } else {
                        showMessage(data.message || 'Error saving question', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showMessage('An error occurred. Please try again.', 'error');
                } finally {
                    loadingSpinner.style.display = 'none';
                }
            });

            // Show message function
            function showMessage(message, type) {
                const messageDiv = document.createElement('div');
                messageDiv.className = type === 'success' ? 'success-message' : 'error-message';
                messageDiv.textContent = message;
                
                form.insertBefore(messageDiv, form.firstChild);
                
                setTimeout(() => {
                    messageDiv.remove();
                }, 5000);
            }

            // Show preview
            function showPreview(formData) {
                const previewContent = document.querySelector('.preview-question');
                const question = formData.get('question');
                const options = formData.getAll('options[]');
                const correctIndex = formData.get('correct');
                const domain = formData.get('domain');
                const difficulty = formData.get('difficulty');
                
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
                    <div class="question-meta">
                        <span class="domain">${domain}</span> | 
                        <span class="difficulty">${difficulty}</span>
                    </div>
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