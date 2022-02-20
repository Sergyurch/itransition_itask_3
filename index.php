<?php
	// mb_internal_encoding('utf-8');
	error_reporting(E_ALL);
	session_start();

	//No authentification or clicked logout
	if ( !isset($_SESSION['user_id']) || isset($_GET['logout']) ) logout();

	//List of chosen users
	if ( isset($_GET['users_id']) ) {
		$users_id = implode(',', $_GET['users_id']);
	}

	$dbh = new PDO('mysql: host=127.0.0.1; dbname=heroku_cb4f6467da51eac; charset=utf8', 'b7ecca645b3490', 'ec42b495');

	//Delete action
	if ( isset($_GET['delete']) ) {
		$sth = $dbh->query("DELETE FROM users WHERE id IN ($users_id)");
		if ( in_array($_SESSION['user_id'], $_GET['users_id']) ) logout();
	}

	//Block action
	if ( isset($_GET['block']) ) {
		$sth = $dbh->prepare("UPDATE users SET is_blocked=1 WHERE id IN ($users_id)");
		$sth->execute();
		if ( in_array($_SESSION['user_id'], $_GET['users_id']) ) logout();
	}

	//Unblock action
	if ( isset($_GET['unblock']) ) {
		$sth = $dbh->prepare("UPDATE users SET is_blocked=0 WHERE id IN ($users_id)");
		$sth->execute();
	}

	$sth = $dbh->query("SELECT * FROM users");
	$sth->setFetchMode(PDO::FETCH_ASSOC);
	
	function logout() {
		session_unset();
		header('Location: login.php');
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Пользователи</title>
		<meta charset="utf-8">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script>
			$(document).ready(function(){
			    $("#all_users").click(function(){
			    	$(".user_checkbox").prop('checked', ( $(this).prop('checked') ) ? true : false ); 
			    });

			    $(".user_checkbox").click(function(){
			    	$("#all_users").prop('checked', false); 
			    });
			});
		</script>
	</head>
	<body>
		<div class="container">
			<nav class="navbar navbar-dark bg-primary px-2">
				<div class="text-white">Вы вошли как <?= $_SESSION['name']; ?></div>
				<a href="index.php?logout=1" class="btn btn-light">Выйти</a>
			</nav>
			<form method="GET">
				<div class="p-3 text-end">
					<button type="submit" class="btn btn-warning" name="block"><i class="bi bi-lock-fill"></i></button>
					<button type="submit" class="btn btn-success" name="unblock"><i class="bi bi-unlock-fill"></i></button>
					<button type="submit" class="btn btn-danger" name="delete"><i class="bi bi-trash"></i></button>
				</div>
				<table class="table text-center">
					<thead>
						<tr>
							<th scope="col">
								<input type="checkbox" name="all_users" id="all_users">
							</th>
							<th scope="col">id</th>
							<th scope="col">Имя</th>
							<th scope="col">Email</th>
							<th scope="col">Статус</th>
							<th scope="col">Дата регистрации</th>
							<th scope="col">Дата последнего логина</th>
						</tr>
					</thead>
					<tbody>
						<?php while ( $row = $sth->fetch() ): ?>
							<tr>
								<td>
									<input type="checkbox" name="users_id[]" class="user_checkbox" value="<?= $row['id']; ?>">
								</td>
								<td><?= $row['id']; ?></td>
								<td><?= $row['name']; ?></td>
								<td><?= $row['email']; ?></td>
								<?= ($row['is_blocked']) ? '<td>Заблокирован</td>' : '<td>Активный</td>'; ?>
								<td><?= $row['create_date']; ?></td>
								<td><?= $row['last_login_date']; ?></td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</form>
		</div>
	</body>
</html>