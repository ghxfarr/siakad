<?php
session_start();
include '../config/db.php';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $data = $result->fetch_assoc();
            if (password_verify($password, $data['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin'] = $username;
                header("Location: ../index.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SIAKAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(45deg, #93c5fd, #d8b4fe, #e5e7eb, #60a5fa);
            background-size: 600% 600%;
            animation: gradientBackground 15s ease infinite;
            will-change: background-position;
            overflow: hidden;
            position: relative;
            font-family: 'Inter', sans-serif;
        }

        @keyframes gradientBackground {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .card {
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.5s ease-out;
        }

        .card.show {
            opacity: 1;
            transform: scale(1);
        }

        .form-control {
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 10px rgba(37, 99, 235, 0.5);
            border-color: #2563eb;
            transform: translateY(-2px);
        }

        .form-label {
            position: absolute;
            top: 10px;
            left: 15px;
            transition: all 0.3s ease;
            color: #6b7280;
            pointer-events: none;
            background: white;
            padding: 0 5px;
        }

        .form-control:focus+.form-label,
        .form-control:not(:placeholder-shown)+.form-label {
            top: -10px;
            font-size: 0.85rem;
            color: #2563eb;
        }

        .btn-primary {
            position: relative;
            overflow: hidden;
            background-color: #1e40af;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-primary:hover::after {
            width: 200px;
            height: 200px;
        }

        .btn-primary.loading::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            pointer-events: none;
            animation: float 10s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-100px);
            }
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1e40af',
                        secondary: '#4b5563',
                    },
                    animation: {
                        fade: 'fadeIn 0.5s ease-in-out',
                        slideLeft: 'slideLeft 0.5s ease-in-out',
                        slideRight: 'slideRight 0.5s ease-in-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideLeft: {
                            '0%': { transform: 'translateX(-20px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                        slideRight: {
                            '0%': { transform: 'translateX(20px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                    },
                },
            },
        };

        document.addEventListener('DOMContentLoaded', () => {
            const card = document.querySelector('.card');
            const body = document.body;

            // Show card on click
            body.addEventListener('click', () => {
                card.classList.add('show');
            });

            // Create particles
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.width = `${Math.random() * 8 + 5}px`;
                particle.style.height = particle.style.width;
                particle.style.left = `${Math.random() * 100}vw`;
                particle.style.top = `${Math.random() * 100}vh`;
                particle.style.animationDelay = `${Math.random() * 5}s`;
                body.appendChild(particle);
            }

            // Handle form submit with loading animation
            const form = document.querySelector('form');
            const submitButton = form.querySelector('button[type="submit"]');
            form.addEventListener('submit', () => {
                submitButton.classList.add('loading');
                submitButton.disabled = true;
                submitButton.textContent = '';
            });
        });
    </script>
</head>

<body>
    <div class="container py-5 h-screen flex items-center justify-center">
        <div class="card shadow-lg bg-white rounded-xl w-full max-w-md">
            <div class="card-body p-6">
                <h4 class="text-center mb-4 text-2xl font-bold text-primary animate-slideLeft">🔐 Login Admin</h4>
                <?php if ($error): ?>
                    <div class="alert alert-danger animate-fade animate-delay-100"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST" class="space-y-4">
                    <div class="mb-3 position-relative">
                        <input type="text" name="username"
                            class="form-control rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20"
                            placeholder=" " value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        <label class="form-label">Username</label>
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="password" name="password"
                            class="form-control rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20"
                            placeholder=" " required>
                        <label class="form-label">Password</label>
                    </div>
                    <button type="submit"
                        class="btn btn-primary w-100 mt-4 animate-slideRight animate-delay-200">Login</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>