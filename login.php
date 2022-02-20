<?php
	//Subform was submited
	if ( !empty($_POST) ) {
		$dbh = new PDO('mysql:host=us-cdbr-east-05.cleardb.net; dbname=heroku_cb4f6467da51eac; charset=utf8', 'b7ecca645b3490', 'ec42b495');
		$email = $_POST['email'];
		$password = $_POST['password'];

		$sth = $dbh->query("SELECT * FROM users WHERE email = '$email'");
		$sth->setFetchMode(PDO::FETCH_ASSOC);
		$user = $sth->fetch();
		
		//User was found in the database
		if ($user) {
			if ($user['password'] != $password) {
				$wrong_password = true;
			} else if ($user['is_blocked']) {
				$user_blocked = true;
			} else {
				$current_datetime = date('Y-m-d H:i:s');
				$sth = $dbh->prepare("UPDATE users SET last_login_date='$current_datetime' WHERE id='{$user['id']}'");
				$sth->execute();
				session_start();
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['name'] = $user['name'];
				header('Location: index.php');
				exit();
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Вход на сайт</title>
		<meta charset="utf-8">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	</head>
	<body>
		<div class="container pt-5">
			<?php if ( isset($user) && !$user ): ?>
				<div class="text-center text-danger p-5">Такого пользователя не существует</div>
			<?php endif; ?>
			<?php if ( isset($wrong_password) ): ?>
				<div class="text-center text-danger p-5">Неправильный пароль</div>
			<?php endif; ?>
			<?php if ( isset($user_blocked) ): ?>
				<div class="text-center text-danger p-5">Пользователь заблокирован</div>
			<?php endif; ?>
			<?php if ( isset($_GET['registration']) ): ?>
				<div class="text-center text-success p-5">Пользователь успешно зарегистрирован. Войдите используя свою почту и пароль.</div>
			<?php endif; ?>
			<form method="POST" class="w-25 text-center mx-auto">
				<div class="mb-3">
				    <label for="email" class="form-label">Email</label>
				    <input type="email" class="form-control" name="email" required>
				</div>
				<div class="mb-3">
				    <label for="password" class="form-label">Пароль</label>
				    <input type="password" class="form-control" name="password" required>
				</div>
				<button type="submit" class="btn btn-primary mb-3">Войти</button>
				<div>
					<a href="registration.php">Регистрация</a>
				</div>
			</form>
		</div>
	</body>
</html>