<?php
session_start();
require_once('connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$db = Database::getInstance();
$users = $db->getUsers();
$user = $users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - QuizQuest</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            padding-top: 80px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .profile-info h1 {
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .domains-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .domain-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .domain-item {
            background: var(--light-color);
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .domain-item:hover {
            background: var(--primary-color);
            color: white;
        }

        .recent-activity {
            margin-top: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .activity-list {
            margin-top: 1rem;
        }

        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .activity-item i {
            color: var(--primary-color);
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
            <a href="quiz_game.php"><i class="fas fa-play"></i> Play</a>
            <a href="leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="profile-container">
        <div class="profile-header animate__animated animate__fadeIn">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user->username); ?></h1>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user->email); ?></p>
                <p><i class="fas fa-calendar"></i> Joined: <?php echo date('F Y', $user->created_at->toDateTime()->getTimestamp()); ?></p>
            </div>
        </div>

        <div class="stats-grid animate__animated animate__fadeInUp">
            <div class="stat-card">
                <i class="fas fa-trophy"></i>
                <h3>Total Score</h3>
                <p><?php echo number_format($user->total_score ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-gamepad"></i>
                <h3>Quizzes Completed</h3>
                <p><?php echo number_format($user->quizzes_completed ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-star"></i>
                <h3>Achievement Points</h3>
                <p><?php echo number_format($user->achievement_points ?? 0); ?></p>
            </div>
        </div>

        <div class="domains-section animate__animated animate__fadeInUp">
            <h2><i class="fas fa-book"></i> Preferred Domains</h2>
            <div class="domain-list">
                <div class="domain-item">Science</div>
                <div class="domain-item">History</div>
                <div class="domain-item">Technology</div>
                <div class="domain-item">Arts</div>
                <div class="domain-item">Sports</div>
                <div class="domain-item">Geography</div>
            </div>
        </div>

        <div class="recent-activity animate__animated animate__fadeInUp">
            <h2><i class="fas fa-history"></i> Recent Activity</h2>
            <div class="activity-list">
                <?php
                // Fetch recent activity from database
                $activities = [
                    ['type' => 'quiz', 'description' => 'Completed Science Quiz', 'score' => 85],
                    ['type' => 'achievement', 'description' => 'Earned "Quick Thinker" Badge'],
                    ['type' => 'quiz', 'description' => 'Completed History Quiz', 'score' => 92],
                ];

                foreach ($activities as $activity): ?>
                    <div class="activity-item">
                        <i class="fas <?php echo $activity['type'] === 'quiz' ? 'fa-check-circle' : 'fa-award'; ?>"></i>
                        <div>
                            <p><?php echo htmlspecialchars($activity['description']); ?></p>
                            <?php if (isset($activity['score'])): ?>
                                <small>Score: <?php echo $activity['score']; ?>%</small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="scripts/main.js"></script>
</body>
</html>