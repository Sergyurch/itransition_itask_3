<?php
	// mb_internal_encoding('utf-8');
	error_reporting(E_ALL);

	//Subform was submited
	if ( !empty($_POST) ) {
		$dbh = new PDO('mysql:host=us-cdbr-east-05.cleardb.net; dbname=heroku_cb4f6467da51eac; charset=utf8', 'b7ecca645b3490', 'ec42b495');
		$name = $_POST['name'];
		$email = $_POST['email'];
		$password = $_POST['password'];

		$sth = $dbh->query("SELECT * FROM users WHERE email = '$email'");
		$sth->setFetchMode(PDO::FETCH_ASSOC);
		$user = $sth->fetch();
		
		//User was found in the database
		if ($user) {
			$user_exists = true;
		} else {
			$sth = $dbh->prepare("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')");

			if ( !$sth->execute() ) {
				$error = true;
			} else {
				header('Location: login.php?registration=success');
				exit();
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Регистрация</title>
		<meta charset="utf-8">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	</head>
	<body>
		<div class="container pt-5">
			<h1 class="text-center mb-5">Регистрация нового пользователя</h1>
			<?php if ( $user_exists ): ?>
				<div class="text-center text-danger p-5">Пользователь c таким Email уже существует</div>
			<?php endif; ?>
			<?php if ( $error ): ?>
				<div class="text-center text-danger p-5">Произошла ошибка</div>
			<?php endif; ?>
			<form method="POST" class="w-25 text-center mx-auto">
				<div class="mb-3">
				    <label for="name" class="form-label">Ваше имя</label>
				    <input type="text" class="form-control" name="name" required>
				</div>
				<div class="mb-3">
				    <label for="email" class="form-label">Email</label>
				    <input type="email" class="form-control" name="email" required>
				</div>
				<div class="mb-3">
				    <label for="password" class="form-label">Пароль</label>
				    <input type="password" class="form-control" name="password" required>
				</div>
				<button type="submit" class="btn btn-primary mb-3">Отправить</button>
				<div>
					<a href="login.php">У меня уже есть аккаунт</a>
				</div>
			</form>
		</div>
	</body>
</html>