<?php
/** ggit - Git Plugin for Gforge
 *
 * Copyright 2009 Ferenc Székely <ferenc@maemo.org>
 *
 * This file is not part of the GForge software.
 *
 * This plugin is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with the plugin. See the LICENSE file.
 */

global $gfplugins;
require_once $gfplugins.'ggit/common/GgitPlugin.class.php' ;

$GgitPluginObject = new GgitPlugin();

register_plugin($GgitPluginObject);

?>
