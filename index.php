<?php
session_start();
require_once('connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizQuest - Challenge Your Knowledge</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand animate__animated animate__fadeIn">
            <i class="fas fa-brain"></i> QuizQuest
        </div>
        <div class="nav-links">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="#" onclick="showLoginModal()"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="#" onclick="showRegisterModal()"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content animate__animated animate__fadeInUp">
            <h1>Welcome to QuizQuest</h1>
            <p>Challenge yourself, compete with others, and become a quiz master!</p>
            <div class="cta-buttons">
                <a href="create_questions.php" class="btn primary-btn">
                    <i class="fas fa-plus"></i> Create Quiz
                </a>
                <a href="quiz_game.php" class="btn secondary-btn">
                    <i class="fas fa-play"></i> Play Now
                </a>
            </div>
        </div>
    </header>

    <section class="features">
        <h2>Why Choose QuizQuest?</h2>
        <div class="feature-grid">
            <div class="feature-card animate__animated animate__fadeInLeft">
                <i class="fas fa-trophy"></i>
                <h3>Compete Globally</h3>
                <p>Challenge players worldwide and climb the leaderboard.</p>
            </div>
            <div class="feature-card animate__animated animate__fadeInUp">
                <i class="fas fa-graduation-cap"></i>
                <h3>Learn & Grow</h3>
                <p>Expand your knowledge across multiple domains.</p>
            </div>
            <div class="feature-card animate__animated animate__fadeInRight">
                <i class="fas fa-users"></i>
                <h3>Community Driven</h3>
                <p>Join a community of knowledge enthusiasts.</p>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>
            <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
            <form id="loginForm" action="auth/login.php" method="POST">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn primary-btn">Login</button>
            </form>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRegisterModal()">&times;</span>
            <h2><i class="fas fa-user-plus"></i> Register</h2>
            <form id="registerForm" action="auth/register.php" method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn primary-btn">Register</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>QuizQuest</h3>
                <p>Making learning fun and competitive</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="leaderboard.php">Leaderboard</a>
                <a href="profile.php">Profile</a>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 QuizQuest. All rights reserved.</p>
        </div>
    </footer>

    <script src="scripts/main.js"></script>
</body>
</html>