<?
if(function_exists(tixNav)){

    if(!$_GET['project']) $_GET['project'] = 0;
    if(!$_GET['sort']) $_GET['sort'] = 'age';

    $title  = 'Ticket Administration:';
    $output = tixNav($_GET['project'], $_GET['sort']);
}
?>
