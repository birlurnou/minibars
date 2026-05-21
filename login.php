<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();

require_once 'admin/encryption_key.php';

$users = json_decode(file_get_contents('config/users.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($users[$login])) {

        $stored_password = decryptPassword($users[$login]['password']);
        if ($password === $stored_password) {
            $_SESSION = array();
            $_SESSION['user'] = $login;
            $_SESSION['access'] = $users[$login]['access'];
            $_SESSION['last_activity'] = time();
            session_write_close();
            session_start();
            header('Location: index.php');
            exit;
        }
    }
    
    $error = 'Неверный логин или пароль';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }



        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        /*@media (max-width: 768px) {
            body {
                background: url('background.jpg') no-repeat center center fixed;
                background-size: cover;
                padding: 16px;
            }
        }*/

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
            font-weight: 500;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
            font-family: inherit;
        }

        .input-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-group input::placeholder {
            color: #aaa;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .error-message {
            margin-top: 20px;
            padding: 12px;
            background: #fee;
            border: 1px solid #fcc;
            border-radius: 10px;
            color: #d32f2f;
            font-size: 14px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            html, body {
                height: 100%;
                overflow: hidden;
                position: fixed;
                width: 100%;
            }
            
            body {
                background: url('background.jpg') no-repeat center center fixed !important;
                background-size: cover !important;
                padding: 16px;
                min-height: 100%;
                overflow: hidden;
            }
            
            .login-container {
                height: 100%;
                display: flex;
                align-items: center;
            }
            
            .login-card {
                padding: 24px 20px;
                width: 100%;
            }
            
            .login-header h2 {
                font-size: 22px;
            }
            
            .login-header p {
                font-size: 13px;
            }
            
            .input-group input {
                padding: 10px 12px;
                font-size: 16px;
            }
            
            .login-btn {
                padding: 10px;
            }
        }

        @media (max-width: 380px) {
            .login-card {
                padding: 20px 16px;
            }
            
            .login-header {
                margin-bottom: 20px;
            }
            
            .login-header h2 {
                font-size: 20px;
            }
            
            .input-group {
                margin-bottom: 14px;
            }
            
            .error-message {
                margin-top: 14px;
                padding: 8px;
                font-size: 12px;
            }
            
            .footer {
                margin-top: 14px;
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>Добро пожаловать</h2>
                <p>Войдите в систему, чтобы продолжить</p>
            </div>

            <form method="post">
                <div class="input-group">
                    <label>Логин</label>
                    <input type="text" name="login" placeholder="Введите ваш логин" required>
                </div>

                <div class="input-group">
                    <label>Пароль</label>
                    <input type="password" name="password" placeholder="Введите пароль" required>
                </div>

                <button type="submit" class="login-btn">Войти</button>

                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
            </form>

            <div class="footer">
                <p>© Hyatt Regency</p>
            </div>
        </div>
    </div>
    <script>
        (function() {
            if (window.innerWidth <= 768) {
                document.body.addEventListener('touchmove', function(e) {
                    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                    }
                }, { passive: false });
            }
        })();
    </script>
</body>
</html>