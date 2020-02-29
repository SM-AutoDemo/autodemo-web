<?php
define('_settings_included', 1);
define('_interface_included', 1);

if(isset($_POST['port']) && isset($_POST['key']) && isset($_POST['data']))
{
	$sAddress = $_SERVER['REMOTE_ADDR'] . ':' . $_POST['port'];
	$sSettings = require 'settings.php';

	if(isset($sSettings[$sAddress]) && $_POST['key'] == $sSettings[$sAddress]['api_key'])
	{
		$sData = json_decode($_POST['data'], true);

		$sDir = 'data/logs/';

		if(!file_exists($sDir))
		{
			mkdir($sDir, 755);
		}

		file_put_contents($sDir . "{$sAddress}.json", json_encode([date('m.d.y - H:i:s'), $sData]) . ",\n", FILE_APPEND | LOCK_EX);

		if($sData['event'] == 'demo_unload' && !empty($sFTPSettings = $sSettings[$sAddress]['ftp']))
		{
			$sFTPSettings = $sSettings[$sAddress]['ftp'];

			if(!empty($sFTPSettings))
			{
				$sFTPConnect = explode(':', $sFTPSettings['host']);
				$pFTPConnect = ftp_connect($sFTPConnect[0], isset($sFTPConnect[1]) ? $sFTPConnect[1] : 21);

				if(!$pFTPConnect || !ftp_login($pFTPConnect, $sFTPSettings['user'], $sFTPSettings['password']))
				{
					error_log('FTP connection error. User: ' . $sFTPSettings['user'] . '. Connect №' . (int)$pFTPConnect . '.');
					exit;
				}

				if(!ftp_chdir($pFTPConnect, $sFTPSettings['demo_path']))
				{
					error_log('FTP chdir error. ' . $sFTPSettings['demo_path'] . ' - is not found.');
					exit;
				}

				$sDir = 'data/demos/';

				if(!file_exists($sDir))
				{
					mkdir($sDir, 755);
				}

				set_time_limit($sData['params']['time_limit'] + 8);

				$sFile = '';

				while($sFile = readdir($pDir))
				{
					if(filetype($sDir . $sFile) == 'file' && time() > (int)explode('-', $sFile, 3)[1] + (int)$sSettings[$sAddress]['demo_life_time'])
					{
						unlink($sDir . $sFile);
					}
				}

				foreach($sData['params']['files'] as $sFile)
				{
					if(ftp_fget($pFTPConnect, $pLocalFile = fopen($sDir . $sFile, 'w+'), $sFile, FTP_BINARY))
					{
						$pZip = new ZipArchive();
						$pZip->open($sDir . $sFile . '.zip', ZipArchive::CREATE);
						$pZip->addFile($sDir . $sFile, $sFile);
						$pZip->close();

						unlink($sDir . $sFile);
						fclose($pLocalFile);
						ftp_delete($pFTPConnect, $sFile);
					}
				}

				ftp_close($pFTPConnect);
			}
		}
		exit;
	}
}

$sDir = 'data/demos/';
$pDir = opendir($sDir);
$sFiles = [];

if($pDir)
{
	$sFile = '';
	$sInfo = [];

	while($sFile = readdir($pDir))
	{
		if(filetype($sDir . $sFile) == 'file')
		{
			$sInfo = explode('-', $sFile, 5);
			$sFiles[] =
			[
				'name' => $sFile,
				'server' => (($sInfo[2] >> 24 & 255) . '.' . ($sInfo[2] >> 16 & 255) . '.' . ($sInfo[2] >> 8 & 255) . '.' . ($sInfo[2] & 255) . ':' . $sInfo[3]),
				'map' => explode('.', $sInfo[4], 2)[0],
				'date' => $sInfo[1],
				'size' => filesize($sDir . $sFile)
			];
		}
	}
}

closedir($pDir);

function array_orderby()
{
	$sArgs = func_get_args();
	$sData = array_shift($sArgs);

	foreach($sArgs as $n => $sField)
	{
		if(is_string($sField))
		{
			$sTmp = array();

			foreach ($sData as $sKey => $sRow)
			{
				$sTmp[$sKey] = $sRow[$sField];
			}

			$sArgs[$n] = $sTmp;
		}
	}

	$sArgs[] = &$sData;
	call_user_func_array('array_multisort', $sArgs);

	return array_pop($sArgs);
}

if(!isset($_GET['order']))
{
	$_GET['order'] = 'server';
}

$bIsDesc = false;
$sFiles = array_orderby($sFiles, isset($_GET['order']) && empty($sFiles[$_GET['order']]) ? $_GET['order'] : 'server', ($bIsDesc = isset($_GET['desc']) && !empty($_GET['desc'])) ? SORT_DESC : SORT_ASC);

require_once 'interface.php';
?>