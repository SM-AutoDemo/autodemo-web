<?php
if(!defined('_interface_included'))
{
	header('HTTP/1.0 403 Forbidden');
	exit;
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="author" content="https://vk.com/wend4r">
		<meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=5.0">
		<title>WocAWP :: Demos</title>
		<link rel="preload" href="style.css" as="style">
		<link rel="stylesheet" href="style.css"/>
	</head>
	<body>
		<header>
			<a href="//wocawp.ru"><img class="wocawp" src="data/img/logo.svg"/></a>
		</header>
		<table class="center">
			<thead>
				<tr>
					<th><a href="?order=server<?php if(!$bIsDesc && $_GET['order'] == 'server'): echo '&desc=1'; endif; ?>">Сервер</a></th>
					<th><a href="?order=date<?php if(!$bIsDesc && $_GET['order'] == 'date'): echo '&desc=1'; endif; ?>">Дата</a></th>
					<th><a href="?order=map<?php if(!$bIsDesc && $_GET['order'] == 'map'): echo '&desc=1'; endif; ?>">Карта</a></th>
					<th><a href="?order=size<?php if(!$bIsDesc && $_GET['order'] == 'size'): echo '&desc=1'; endif; ?>">Скачать</a></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($sFiles as $sFile): ?><tr>
					<td><?php echo $sFile['server']; ?></td>
					<td><?php echo date('H:i:s - m.d.y', $sFile['date']); ?></td>
					<td><?php echo $sFile['map']; ?></td>
					<td><a href="<?php echo $sDir . $sFile['name']; ?>"><?php echo round($sFile['size'] / 1048576, 1) . ' MB (.' . substr(strrchr($sFile['name'], '.'), 1) . ')'; ?></a></td>
				</tr>
				<?php endforeach; ?><tr class="padding"/>
			</tbody>
		</table>
	</body>
</html>