<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JudgeLists</title>
</head>
<body>
<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath('../includes/'));
require_once 'Scorpio/bootstrap.php';
require_once realpath('../config/setting.dist.php');

Sco_Loader_Autoloader::getInstance()
	->pushAutoloader(BASE_TRUST_PATH, 'HOF_', true)
;

HOF::getInstance();

for ($i = 1000; $i < 9999; $i++)
{
	$j = HOF_Model_Data::getJudgeData($i);
	if ($j)
	{
		print ("case {$i}:// {$j['exp']}<br />");
		$list[] = $i;
	}
}
print ("array(<br />\n");
foreach ($list as $var)
{
	$A++;
	print ("$var, ");
	if ($A % 5 == 0) print ("<br />\n");
}
print ("<br />\n);");


?>
</body>
</html>