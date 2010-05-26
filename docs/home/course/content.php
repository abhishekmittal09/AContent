<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../../include/');
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'../home/classes/ContentUtility.class.php');

global $_current_user, $_course_id, $_content_id, $contentManager;

$cid = $_content_id;

if ($cid == 0) {
	header('Location: '.$_base_href.'index.php');
	exit;
}
if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
	$_SESSION['course_id'] = $cid;  // used by get.php
}

/* show the content page */
if (isset($contentManager)) $content_row = $contentManager->getContentPage($cid);

if (!$content_row || !isset($contentManager)) {
	$_pages['home/course/content.php']['title_var'] = 'missing_content';
	$_pages['home/course/content.php']['parent']    = 'home/index.php';
	$_pages['home/course/content.php']['ignore']	= true;


	require(TR_INCLUDE_PATH.'header.inc.php');

	$msg->addError('MISSING_CONTENT');
	$msg->printAll();

	require (TR_INCLUDE_PATH.'footer.inc.php');
	exit;
} /* else: */

if (defined('TR_FORCE_GET_FILE') && TR_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_course_id . '/';
}

/* the "heading navigation": */
$path	= $contentManager->getContentPath($cid);

if ($content_row['content_path']) {
	$content_base_href = $content_row['content_path'].'/';
}

$parent_headings = '';
$num_in_path = count($path);

/* the page title: */
$page_title = '';
$page_title .= $content_row['title'];

for ($i=0; $i<$num_in_path; $i++) {
	$content_info = $path[$i];
	if ($_SESSION['prefs']['PREF_NUMBERING']) {
		if ($contentManager->_menu_info[$content_info['content_id']]['content_parent_id'] == 0) {
			$top_num = $contentManager->_menu_info[$content_info['content_id']]['ordering'];
			$parent_headings .= $top_num;
		} else {
			$top_num = $top_num.'.'.$contentManager->_menu_info[$content_info['content_id']]['ordering'];
			$parent_headings .= $top_num;
		}
		if ($_SESSION['prefs']['PREF_NUMBERING']) {
			$path[$i]['content_number'] = $top_num . ' ';
		}
		$parent_headings .= ' ';
	}
}

if ($_SESSION['prefs']['PREF_NUMBERING']) {
	if ($top_num != '') {
		$top_num = $top_num.'.'.$content_row['ordering'];
		$page_title .= $top_num.' ';
	} else {
		$top_num = $content_row['ordering'];
		$page_title .= $top_num.' ';
	}
}

$parent = 0;

foreach ($path as $i=>$page) {
	// When login is a student, remove content folder from breadcrumb path as content folders are
	// just toggles for students. Keep content folder in breadcrumb path for instructors as they
	// can edit content folder title. 
	if ((!isset($_current_user) || !$_current_user->isAuthor($_course_id)) && 
	    $contentManager->_menu_info[$page['content_id']]['content_type'] == CONTENT_TYPE_FOLDER) {
		unset($path[$i]);
		continue;
	}
	
	if ($contentManager->_menu_info[$page['content_id']]['content_type'] == CONTENT_TYPE_FOLDER)
		$content_url = 'home/course/edit_content_folder.php?_cid='.$page['content_id'];
	else
		$content_url = 'home/course/content.php?_cid='.$page['content_id'];
		
	if (!$parent) {
		$_pages[$content_url]['title']    = $page['content_number'] . $page['title'];
		$_pages[$content_url]['parent']   = 'home/index.php';
	} else {
		$_pages[$content_url]['title']    = $page['content_number'] . $page['title'];
		$_pages[$content_url]['parent']   = 'home/course/edit_content_folder.php?_cid='.$parent;
	}

	$_pages[$content_url]['ignore'] = true;
	$parent = $page['content_id'];
}
$last_page = array_pop($_pages);
$_pages['home/course/content.php'] = $last_page;

reset($path);
$first_page = current($path);

/* the content test extension page */
$content_test_ids = array();	//the html
$content_test_rows = $contentManager->getContentTestsAssoc($cid);
if (is_array($content_test_rows))
{
	foreach ($content_test_rows as $content_test_row){
		$content_test_ids[] = $content_test_row;
	}
}

/*TODO***************BOLOGNA***************REMOVE ME**********/
/* the content forums extension page*/
//$content_forum_ids = array();	//the html
//$content_forum_rows = $contentManager->getContentForumsAssoc($cid);
//if (is_array($content_forum_rows))
//{
//	foreach ($content_forum_rows as $content_forum_row){
//		$content_forum_ids[] = $content_forum_row;
//	}
//}

// use any styles that were part of the imported document
// $_custom_css = $_base_href.'headstuff.php?cid='.$cid.SEP.'path='.urlEncode($_base_href.$course_base_href.$content_base_href);

if ($content_row['use_customized_head'] && strlen($content_row['head']) > 0)
{
	$_custom_head .= $content_row['head'];
}

global $_custom_head;
$_custom_head .= '
	<script language="javascript" type="text/javascript">
	//<!--
	jQuery(function() {
	jQuery(\'a.tooltip\').tooltip( { showBody: ": ", showURL: false } );
	} );
	//-->
	</script>
';

if (isset($_SESSION['user_id'])) ContentUtility::saveLastCid($cid);

if (isset($top_num) && $top_num != (int) $top_num) {
	$top_num = substr($top_num, 0, strpos($top_num, '.'));
}
/*
$shortcuts = array();
if (((!$content_row['content_parent_id'] && ($_SESSION['packaging'] == 'top'))
      || ($_SESSION['packaging'] == 'all'))
	  || (isset($_current_user) && $_current_user->isAuthor($_course_id))) {

	$shortcuts[] = array('title' => _AT('export_content'), 'url' => $_base_href . 'tools/ims/ims_export.php?cid='.$cid);
}

if (isset($_current_user) && $_current_user->isAuthor($_course_id)) {
	$shortcuts[] = array('title' => _AT('edit_this_page'),   'url' => $_base_href . 'home/editor/edit_content.php?_cid='.$cid);
//	$shortcuts[] = array('title' => _AT('add_top_folder'),   'url' => $_base_href . 'home/editor/edit_content_folder.php?_course_id='.$_course_id);

	if ($contentManager->_menu_info[$cid]['content_parent_id']) {
		$shortcuts[] = array('title' => _AT('add_sibling_folder'), 'url' => $_base_href .
			'home/editor/edit_content_folder.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id'].SEP.'_course_id='.$_course_id);
	}
//	$shortcuts[] = array('title' => _AT('add_top_page'),     'url' => $_base_href . 'home/editor/edit_content.php?_course_id='.$_course_id);
	if ($contentManager->_menu_info[$cid]['content_parent_id']) {
		$shortcuts[] = array('title' => _AT('add_sibling_page'), 'url' => $_base_href .
			'home/editor/edit_content.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id'].SEP.'_course_id='.$_course_id);
	}
	$shortcuts[] = array('title' => _AT('delete_this_page'), 'url' => $_base_href . 'home/editor/delete_content.php?_cid='.$cid);
}
$savant->assign('shortcuts', $shortcuts);
*/

/*if (((!$content_row['content_parent_id'] && ($_SESSION['packaging'] == 'top'))
      || ($_SESSION['packaging'] == 'all'))
	  || (isset($_current_user) && $_current_user->isAuthor($_course_id))) {

	$_tool_shortcuts[] = array(
		  'title' => _AT('export_content'), 
		  'url' => $_base_href . 'home/imscc/ims_export.php?_cid='.$cid,
		  'icon' => $_base_href . 'images/download.png');
}

if (isset($_current_user) && $_current_user->isAuthor($_course_id)) {
	$_tool_shortcuts[] = array(
		  'title' => _AT('edit_this_page'),   
		   'url' => $_base_href . 'home/editor/edit_content.php?_cid='.$cid,
		  'icon' => $_base_href . 'images/medit.gif');

	if ($contentManager->_menu_info[$cid]['content_parent_id']) {
		  $_tool_shortcuts[] = array(
		  'title' => _AT('add_sibling_folder'), 
		  'url' => $_base_href .
			'home/editor/edit_content_folder.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id'].SEP.'_course_id='.$_course_id,
		   'icon' => $_base_href . 'images/folder_new_sibling.gif');
	}
	if ($contentManager->_menu_info[$cid]['content_parent_id']) {
		$_tool_shortcuts[] = array(
		  'title' => _AT('add_sibling_page'), 
		  'url' => $_base_href .
			'home/editor/edit_content.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id'].SEP.'_course_id='.$_course_id,
		  'icon' => $_base_href . 'images/page_add_sibling.gif');
	}
	$_tool_shortcuts[] = array(
		  'title' => _AT('delete_this_page'), 	
		  'url' => $_base_href . 'home/editor/delete_content.php?_cid='.$cid,
		  'icon' => $_base_href . 'images/page_delete.gif');
}*/
$_tool_shortcuts = ContentUtility::getToolShortcuts($content_row);

//if it has test and forum associated with it, still display it even if the content is empty
if ($content_row['text'] == '' && empty($content_test_ids)){
	$msg->addInfo('NO_PAGE_CONTENT');
	$savant->assign('body', '');
} else {

	//Silvia: to provide appropriated content on the basis of users' preferences
	$content = ContentUtility::applyAlternatives($cid, $content_row['text']);
                
	$content = ContentUtility::formatContent($content, $content_row['formatting']);

	$content_array = ContentUtility::getContentTable($content);
	
	$savant->assign('content_table', $content_array[0]);
	$savant->assign('body', $content_array[1]);

	//assign test pages if there are tests associated with this content page
	if (!empty($content_test_ids)){
		$savant->assign('test_message', $content_row['test_message']);
		$savant->assign('test_ids', $content_test_ids);
	} else {
		$savant->assign('test_message', '');
		$savant->assign('test_ids', array());
	}

		/*TODO***************BOLOGNA***************REMOVE ME**********/
		//assign forum pages if there are forums associated with this content page
//		if (!empty($content_forum_ids)){
//			$savant->assign('forum_message','');
//			$savant->assign('forum_ids', $content_forum_ids);
//		} else {
//			$savant->assign('forum_message', '');
//			$savant->assign('forum_ids', array());
//		}
}

$savant->assign('content_info', _AT('page_info', AT_date(_AT('page_info_date_format'), $content_row['last_modified'], TR_DATE_MYSQL_DATETIME), $content_row['revision'], AT_date(_AT('inbox_date_format'), $content_row['release_date'], TR_DATE_MYSQL_DATETIME)));
$savant->assign('course_id', $_course_id);

require(TR_INCLUDE_PATH.'header.inc.php');

$savant->display('home/course/content.tmpl.php');

//save last visit page.
$_SESSION['last_visited_page'] = $server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

require (TR_INCLUDE_PATH.'footer.inc.php');
?>