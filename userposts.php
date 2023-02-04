<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2011 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * User posts page
 *
 * $URL$
 * $Id$
 *
*/
require_once('class2.php');

e107::coreLan('userposts');

require_once(e_HANDLER.'comment_class.php');
$cobj = new comment();

$e107 = e107::getInstance();
$sql = e107::getDb();
$pref = e107::getPref();
$tp = e107::getParser();
$ns = e107::getRender();

require_once(HEADERF);

$action = 'exit';
if (e_QUERY)
{
  $tmp = explode('.', e_QUERY);
  $from = intval($tmp[0]);			// Always defined
  $action = varset($tmp[1],'exit');
  if (!isset($tmp[2])) $action = 'exit';
  $id = intval(varset($tmp[2],0));
  if ($id <= 0) $action = 'exit';
  if (($id != USERID) && !check_class(varset($pref['memberlist_access'], 253))) $action = 'exit';
  unset($tmp);
}
if(isset($_POST['fsearch']))
{
	$action = 'forums';
}

if ($action == 'exit')
{
	e107::redirect();
	exit;
}

if ($action == "comments")
{

		if($id == e107::getUser()->getId())
		{
			$user_name = USERNAME;
		}
		else
		{
			$user_name = e107::getSystemUser($id, false)->getName(LAN_ANONYMOUS);
		}


	// new template engine - override in THEME/templates/userposts_template.php
	$USERPOSTS_TEMPLATE = e107::getCoreTemplate('userposts');

	$sql2 = e107::getDb('sql2');
	if($user_name)
	{
		$ccaption = str_replace('[x]', $user_name, UP_LAN_1);

		$ctotal = e107::getSystemUser($id, false)->getValue('comments', 0); // user_* getter shorthand
		$data = $cobj->getCommentData(10, $from, 'comment_author_id ='.$id);
	}
	else // posts by IP currently disabled (see Query filtering - top of the page)
	{
		e107::redirect();
		exit;
		/*$dip = $id;
		if (strlen($dip) == 8)
		{  // Legacy decode (IPV4 address as it used to be stored - hex string)
		  $hexip = explode('.', chunk_split($dip, 2, '.'));
		  $dip = hexdec($hexip[0]). '.' . hexdec($hexip[1]) . '.' . hexdec($hexip[2]) . '.' . hexdec($hexip[3]);

		}
		$ccaption = UP_LAN_1.$dip;
		$data = $cobj->getCommentData($amount='10', $from, "comment_ip = '".$id."'");
		$data = $cobj->getCommentData(10, $from, 'comment_ip ='.$tp->toDB($user_ip));*/
	}

	$ctext = '';
	if(empty($data) || !is_array($data))
	{
		$ctext = "<span class='mediumtext'>".UP_LAN_7."</span>";
	}

	else
	{
		$userposts_comments_table_string = '';
		foreach($data as $row)
		{
			$userposts_comments_table_string .= parse_userposts_comments_table($row, $USERPOSTS_TEMPLATE['comments_table']);
		}

		$parms = $ctotal.",10,".$from.",".e_REQUEST_SELF."?[FROM].comments.".$id;
		$nextprev = $ctotal ? $tp->parseTemplate("{NEXTPREV={$parms}}") : '';
		if($nextprev) $nextprev = str_replace('{USERPOSTS_NEXTPREV}', $nextprev, $USERPOSTS_TEMPLATE['np_table']);
		$vars = new e_vars(array(
			'NEXTPREV' => $nextprev
		));

		// preg_replace("/\{(.*?)\}/e", '$\1', $USERPOSTS_TEMPLATE['comments_table_start']);
		$userposts_comments_table_start = $tp->simpleParse($USERPOSTS_TEMPLATE['comments_table_start'], $vars);
		// preg_replace("/\{(.*?)\}/e", '$\1', $USERPOSTS_TEMPLATE['comments_table_end'])
		$userposts_comments_table_end = $tp->simpleParse($USERPOSTS_TEMPLATE['comments_table_end'], $vars);

		$ctext .= $userposts_comments_table_start.$userposts_comments_table_string.$userposts_comments_table_end;

	}
	$ns->tablerender($ccaption, $ctext);
}
 
 
else
{
	e107::redirect();
	exit;
}




require_once(FOOTERF);


function parse_userposts_comments_table($row, $template)
{
//	global $USERPOSTS_COMMENTS_TABLE, $pref, $gen, $tp, $id, $sql2, $comment_files;

	$gen = e107::getDateConvert();
	$datestamp = $gen->convert_date($row['comment_datestamp'], "short");
	$bullet = '';
	if(defined('BULLET'))
	{
		$bullet = '<img src="'.THEME_ABS.'images/'.BULLET.'" alt="" class="icon" />';
	}
	elseif(file_exists(THEME.'images/bullet2.gif'))
	{
		$bullet = '<img src="'.THEME_ABS.'images/bullet2.gif" alt="" class="icon" />';
	}
	$vars = new e_vars();

	$vars->USERPOSTS_COMMENTS_ICON		= $bullet;
	$vars->USERPOSTS_COMMENTS_DATESTAMP	= UP_LAN_11." ".$datestamp;
	$vars->USERPOSTS_COMMENTS_HEADING	= $row['comment_title'];
	$vars->USERPOSTS_COMMENTS_COMMENT	= $row['comment_comment'];
	$vars->USERPOSTS_COMMENTS_HREF_PRE	= "<a href='".$row['comment_url']."'>";
	$vars->USERPOSTS_COMMENTS_TYPE		= $row['comment_type'];

	//return(preg_replace("/\{(.*?)\}/e", '$\1', $USERPOSTS_COMMENTS_TABLE));
	return e107::getParser()->simpleParse($template, $vars);
}


