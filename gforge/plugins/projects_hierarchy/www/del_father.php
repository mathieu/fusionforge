<?php
/**
 * Role Editing Page
 *
 * Copyright 2004 (c) GForge LLC
 *
 * @version   $Id: del_father.php,v 1.0 2006/10/10 15:00:00 fregnier Exp $
 * @author Fabien Regnier fabien.regnier@sogeti.com
 * @date 2006-10-10
 *
 * This file is part of GForge.
 *
 * GForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GForge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once $gfwww.'include/pre.php';

$group_id = getIntFromRequest('group_id');
$sub_group_id = getIntFromRequest('sub_group_id');

session_require(array('group'=>$group_id,'admin_flags'=>'A'));

//plugin webcal
$params[0] = $sub_group_id;
$params[1] = $group_id;
plugin_hook('del_cal_link_father',$params);

//del link between two projects
$sql = "DELETE FROM plugin_projects_hierarchy WHERE project_id  = '".$group_id."' AND sub_project_id = '".$sub_group_id."'";
//print "<br>".$sql;
db_begin();
db_query($sql) or die(db_error());
db_commit();

header("Location: ".util_make_url ('/project/admin/index.php?group_id='.$sub_group_id));
?>
