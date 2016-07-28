<?
if ($myNewsConf['default']['channel'] == 'on'){
	require($myNewsConf['path']['sys']['index'] . '/include/classes/mysql_rdf.class');

	$genrdf = new mysql_rdf;

	$genrdf->host     =	$myNewsConf['db']['hostname'];
	$genrdf->db       = $myNewsConf['db']['dbName'];
	$genrdf->login    = $myNewsConf['db']['dbUser'];
	$genrdf->password = $myNewsConf['db']['dbPass'];

	$genrdf->title_field = "title";
	$genrdf->story_path = $myNewsConf['default']['siteurl'] . "/" . $myNewsConf['scripts']['story'] . "/view/";
	$genrdf->link_field  = "artnr";

	$genrdf->sql      = "SELECT * FROM " . $myNewsConf['db']['table']['news'] . " ORDER BY artnr DESC limit 10";

	$genrdf->rdf_title = $myNewsConf['default']['sitename'];
	$genrdf->rdf_link  = $myNewsConf['default']['siteurl'];
	$genrdf->rdf_descr = $myNewsConf['default']['desc'];
	$genrdf->rdf_lang  = "en-us";

	$genrdf->rdf_encoding = "UTF-8";
	$genrdf->connect($myNewsConf['path']['sys']['index'] . "/" . $myNewsConf['default']['channel_file']); 
}

?>
