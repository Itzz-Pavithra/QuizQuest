<?php
session_start();
require_once('connection.php');

$db = Database::getInstance();
$users = $db->getUsers();

// Get leaderboard data
$leaderboard = $users->aggregate([
    [
        '$project' => [
            'username' => 1,
            'total_score' => ['$ifNull' => ['$total_score', 0]],
            'quizzes_completed' => ['$ifNull' => ['$quizzes_completed', 0]],
            'achievement_points' => ['$ifNull' => ['$achievement_points', 0]]
        ]
    ],
    [
        '$sort' => ['total_score' => -1]
    ],
    [
        '$limit' => 100
    ]
])->toArray();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - QuizQuest</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .leaderboard-container {
            padding-top: 80px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .leaderboard-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 20px;
            background: white;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn.active {
            background: var(--primary-color);
            color: white;
        }

        .leaderboard-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: var(--primary-color);
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #f5f5f5;
        }

        .rank {
            width: 80px;
            text-align: center;
        }

        .rank-1 {
            color: gold;
        }

        .rank-2 {
            color: silver;
        }

        .rank-3 {
            color: #cd7f32;
        }

        .profile-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dark-color);
            text-decoration: none;
        }

        .profile-link:hover {
            color: var(--primary-color);
        }

        .achievement-badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
        }

        .page-btn.active {
            background: var(--primary-color);
            color: white;
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

    <div class="leaderboard-container">
        <div class="leaderboard-header animate__animated animate__fadeIn">
            <h1><i class="fas fa-trophy"></i> Global Leaderboard</h1>
            <p>Top performers from around the world</p>
        </div>

        <div class="filters animate__animated animate__fadeIn">
            <button class="filter-btn active">All Time</button>
            <button class="filter-btn">This Month</button>
            <button class="filter-btn">This Week</button>
            <button class="filter-btn">By Domain</button>
        </div>

        <div class="leaderboard-table animate__animated animate__fadeInUp">
            <table>
                <thead>
                    <tr>
                        <th class="rank">Rank</th>
                        <th>Player</th>
                        <th>Score</th>
                        <th>Quizzes</th>
                        <th>Achievement Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $index => $user): ?>
                        <tr>
                            <td class="rank <?php echo $index < 3 ? "rank-" . ($index + 1) : ""; ?>">
                                <?php if ($index < 3): ?>
                                    <i class="fas fa-trophy"></i>
                                <?php endif; ?>
                                #<?php echo $index + 1; ?>
                            </td>
                            <td>
                                <a href="profile.php?id=<?php echo $user->_id; ?>" class="profile-link">
                                    <i class="fas fa-user-circle"></i>
                                    <?php echo htmlspecialchars($user->username); ?>
                                    <?php if (isset($user->achievements)): ?>
                                        <span class="achievement-badge" style="background: #FFD700;">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td><?php echo number_format($user->total_score); ?></td>
                            <td><?php echo number_format($user->quizzes_completed); ?></td>
                            <td><?php echo number_format($user->achievement_points); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">4</button>
            <button class="page-btn">5</button>
            <button class="page-btn"><i class="fas fa-ellipsis-h"></i></button>
            <button class="page-btn">Next</button>
        </div>
    </div>

    <script>
        // Filter buttons functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                // Add AJAX call here to fetch filtered data
            });
        });

        // Pagination functionality
        const pageButtons = document.querySelectorAll('.page-btn');
        pageButtons.forEach(button => {
            button.addEventListener('click', () => {
                if (!button.classList.contains('active')) {
                    pageButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    // Add AJAX call here to fetch page data
                }
            });
        });
    </script>
</body>
</html>