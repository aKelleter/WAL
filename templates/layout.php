<!DOCTYPE html>
<html lang="fr">
	<?php require '_head.php'; ?>
	<body>
		<div class="container">
            <?php include '_header.php'; ?>

            <h1 class="w-app-title text-center"><?= $data['title']; ?></h1>

			<?php require $templatePath; ?>

            <?php include '_footer.php'; ?>
        </div>
	</body>
</html>