<?php

function wp_ozh_randomwords_adminpage () {
	global $wp_ozh_randomwords;
	wp_ozh_randomwords_readlists();
	if (@$_POST) wp_ozh_randomwords_processforms();
	wp_ozh_randomwords_print_help();
	wp_ozh_randomwords_printjs($wp_ozh_randomwords['nlist']);
	wp_ozh_randomwords_print_addlist();
	wp_ozh_randomwords_print_editlists();
	wp_ozh_randomwords_print_deletelist();
}


function wp_ozh_randomwords_printjs($nlist){
	print <<<JAVASCRIPT
	<script>
	function ozh_random_show_list(what) {
		for (var i=0; i< ($nlist+1) ; i++ ) {
			document.getElementById('list'+i).style.display = 'none';
		}
		document.getElementById('list'+what).style.display = 'block';
		document.getElementById('list'+what).focus();
	}
	</script>
JAVASCRIPT;
}

function wp_ozh_randomwords_readlists() {
	global $wp_ozh_randomwords;
	$wp_ozh_randomwords['status'] = get_option('ozh_randomwords_status');
	$wp_ozh_randomwords['lists'] = get_option('ozh_randomwords');
	$wp_ozh_randomwords['nlist'] = count( $wp_ozh_randomwords['lists'] );
	return true;
}

function wp_ozh_randomwords_print_addlist() {
	echo <<<ADDLIST
	<div class="wrap">
	<h2>Add a new list</h2>
	<form method="post" action="" name="create">
	<input type="hidden" name="action" value="add">
	<table class="form-table"><tr>
		<td valign="top" width="1%">Name of the list</td>
		<td valign="top">Items of the list</td>
		<td valign="top" rowspan="3" width="25%">&nbsp;</td>
	</tr><tr>
		<td valign="top"><input type="text" style='font-size:12px;width: 10em;' class="code" name="new_list_name"></td>
		<td valign="top">
		<textarea name="new_list_values" cols="60" rows="4" style="font-size: 12px;" class="code"></textarea>
		</td>
	</tr><tr>
		<td><p class="submit"><input type="submit" value="Save new list" class="button-primary" /></p>
		<td></td>
		<td></td>
	</tr>
	</table>
	</form>
	</div>
ADDLIST;
}

function wp_ozh_randomwords_print_editlists() {
	global $wp_ozh_randomwords;
	if ($wp_ozh_randomwords['status'] == 'installed') {
		echo '<div class="wrap">
		  <h2>Edit existing lists</h2>';
		if ($wp_ozh_randomwords['lists']) {
			print '<form method="post" action="" name="edit">';
			print '<input type="hidden" name="action" value="edit"/>';
			print '<table class="form-table"><tr>
				<td valign="top" width="1%">Existing Lists</td>
				<td valign="top">Items of the list</td>
				<td valign="top" rowspan="3">
				</td>
				</tr>';
			print '<tr><td valign="top">';
			print "<select name='lists' style='min-width:10.5em;font-size:12px;' onChange='ozh_random_show_list(edit.lists.selectedIndex);'" . ">\n" ;
			print "<option value='0' selected>Select list to edit\n";
			$i=1;
			foreach ($wp_ozh_randomwords['lists'] as $array) {
				print "<option value='$i' class='code'/>". stripslashes ($array[0]) . "\n";
				$i++;
			}
			print "</select>\n";
			print '</td><td valign="top">'."\n";

			print '<textarea id="list0" cols="60" rows="5" style="font-size:12px;display:block;" class="code" disabled></textarea>
				';
			$i=1;
			foreach ($wp_ozh_randomwords['lists'] as $array) {
				print '<textarea id="list'.$i.'" name="list[]" cols="60" rows="5" class="code" style="font-size:12px;display:none;">';
				$pouet = array_shift ($array);
				foreach($array as $item) {print stripslashes($item). "\n";}
				print "</textarea>\n";
				$i++;
			}
			print '</td></tr><tr>
				<td><p class="submit"><input type="submit" name="edit_list_save" value="Save changes" /></p>
				<td></td>
				<td></td>
				</tr>
				</table></form>
				<p>Use the following syntax:<br/>
				In posts: <strong><tt>[random:<em>name_of_the_list</em>]</tt></strong><br/>
				in PHP: <strong><tt>&lt?php wp_ozh_randomwords(\'<em>name_of_the_list</em>\') ?></tt></strong></p>
				</ul>
				</div>';
		} else {
			print "<p>Well, create some lists first !</p>";
		}
		print "</div>\n";
	}
}

function wp_ozh_randomwords_print_deletelist() {
	global $wp_ozh_randomwords;
	if ($wp_ozh_randomwords['status'] == 'installed') {
		print '<div class="wrap">
			<h2>Delete lists</h2>
			';
		if ( $wp_ozh_randomwords['lists'] ) {
			print "<style>.listdelete:hover{color:red}</style>\n";
			print '<form method="post" action="" name="frmdelete">
				<input type="hidden" name="action" value="delete">
				<ul style="margin-left:10px;overflow:auto;list-style:none">';
			$i=0;
			foreach ($wp_ozh_randomwords['lists'] as $array) {
				print '<li  style="float:left;width:22%">';
				print "<label class='listdelete' for='del_list_$i'>";
				$list_name = stripslashes(array_shift($array));
				print "<input name='del_list[]' type='checkbox' id='del_list_$i' value='$i' /><span title='Delete this list'> $list_name </span></label></li>\n";
				$i++;
			}
			print "</ul>\n";
			print '<p class="submit"><input class="listdelete" type="submit" value="Delete selected" onclick="return confirm(\'Permanently deleted selected lists ?  OK to delete, Cancel to stop.\');"/></p>';
			print "</form>\n";
		} else {
			print "<p>No list yet ...</p>";
		}
		print "</div>\n";
	}
}

function wp_ozh_randomwords_print_help() {
	global $wp_ozh_randomwords;
	if ($wp_ozh_randomwords['status'] != 'installed' || !$wp_ozh_randomwords['lists'] ) {
		print '<div class="wrap"><h2>Initialization</h2>';
		if ($wp_ozh_randomwords['status'] != 'installed') {
			echo '<p>Welcome on the admin page of "Ozh\'s Random Words" plugin, a plugin introducing some randomness and entropy in your pages or posts.</p>
			<p>The concept of this plugin is the following :<ul><li>create a list of words, for instance a list named "<strong>comments</strong>" with items "<em>said</em>", "<em>wrote</em>" and "<em>replied</em>".</li>
			<li>in your comment pages for example, you can now use <strong><code>&lt?php wp_ozh_randomwords(\'comments\') ?&gt</code></strong> to output a random word of this list next to each commenter\'s name, instead of always using the same boring "said".</li>
			<li>in your posts, you can type in <strong><code>[random:comments]</code></strong> to echo a different element from the list each time the post item is displayed</li>
			</ul></p>
			<p>This plugin very simple to use, but to make things even easier, you can now go ahead with the automatic installation that will help you setting up your first list.</p>
			';
		}
		print "<p>At this time you have no list defined, you can set up an example list by pressing the following button, or manually create your own list in the box below. It will be easy then to update, modify or delete it.<br/>Create list \"comments\" with items \"<em>said</em>\", \"<em>wrote</em>\" and \"<em>replied</em>\" ?</p>
		<form method='post' action='' name='create'>
		<input type='hidden' name='action' value='add' />
		<input type='hidden' name='new_list_name' value='comment' />
		<input type='hidden' name='new_list_values' value='said\nwrote\nreplied\n' />
		<input type='submit' value='Create new list' />
		</form></div>\n";
	}
}

function wp_ozh_randomwords_processforms() {
	print '<div class="updated">';
	switch ($_POST['action']) {
		case 'install':
			wp_ozh_randomwords_do_init();
			break;
		case 'edit':
			wp_ozh_randomwords_updatelists();
			break;
		case 'add' :
			wp_ozh_randomwords_addlist();
			break;
		case 'delete':
			wp_ozh_randomwords_deletelists();
			break;
	}
	wp_ozh_randomwords_readlists();
	print '</div>';
}

function wp_ozh_randomwords_updatelists() {
	global $wp_ozh_randomwords;
	$new = array();
	$i=0;
	foreach ($_POST['list'] as $list) {
		$list = split ("\n",trim($list));
		array_unshift($list,$wp_ozh_randomwords['lists'][$i][0]);
		$new[] = $list;
		$i++;
	}
	if (update_option('ozh_randomwords',$new)) {
		print "<p>Lists saved successfully.</p>";
	} else {
	die('<p><span style="color:red;">Something went wrong:</span> unable to save lists in the datadase.</p>');
	}
}

function wp_ozh_randomwords_do_init() {
	global $wp_ozh_randomwords;
	$wp_ozh_randomwords['lists'] = array();
	add_option('ozh_randomwords',$wp_ozh_randomwords['lists'], "Ozh's Random Words");
	add_option('ozh_randomwords_status','installed', "Ozh's Random Words plugin status");
	print "<p>Database entries created upon plugin initialization.</p>";
}

function wp_ozh_randomwords_addlist() {
	global $wp_ozh_randomwords;
	if ($wp_ozh_randomwords['status'] != 'installed') {
		wp_ozh_randomwords_do_init();
	}
	$new = split("\n", trim($_POST['new_list_values'],"\n\r"));
	array_unshift( $new , trim($_POST['new_list_name']));
	array_push($wp_ozh_randomwords['lists'],$new);
	if (update_option('ozh_randomwords',$wp_ozh_randomwords['lists'])) {
		print "<p>New list saved successfully.</p>";
	} else {
		die('<p><span style="color:red;">Something went wrong:</span> unable to save list in the datadase.</p>');
	}
}

function wp_ozh_randomwords_deletelists() {
	if (!isset($_POST['del_list'])) {
		print "<p>No list deleted</p>";
	} else {
		global $wp_ozh_randomwords;
		$new = array();
		$i=0;
		$del=0;
		foreach ($wp_ozh_randomwords['lists'] as $array) {
			if (!in_array($i,$_POST['del_list'])) {
				$new[] = $array;
			} else {
				$del++;
			}
			$i++;
		}
		if (update_option('ozh_randomwords',$new)) {
			$plural = ($del>1)?'s':'';
			print "<p>$del list$plural deleted successfully.</p>";
		} else {
			die('<p><span style="color:red;">Something went wrong:</span> unable to delete lists in the datadase.</p>');
		}
	}
}

?>