<?php
/**
  *
  * SourceForge Exports
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id$
  *
  */


require_once('pre.php');

$HTML->header(array(title=>"Exports Available"));
?>
<p><?php echo $GLOBALS['sys_name']; ?> data is exported in a variety of standard formats. Many of
the export URLs can also accept form/get data to customize the output. All
data generated by these pages is realtime.</p>

<h3>
XML Exports
</h3>

<h4>News Data</h4>
<ul>
<li><a href="rss_sfnews.php"><?php echo $GLOBALS['sys_name']; ?> Front Page/Project News</a>
(<a href="http://my.netscape.com/publish/formats/rss-spec-0.91.html">RSS 0.91</a>,
<a href="http://my.netscape.com/publish/formats/rss-0.91.dtd">&lt;rss-0.91.dtd&gt;</a>)</li>
<li><a href="rss_sfnewreleases.php"><?php echo $GLOBALS['sys_name']; ?> New Releases</a>
(<a href="http://my.netscape.com/publish/formats/rss-spec-0.91.html">RSS 0.91</a>,
<a href="http://my.netscape.com/publish/formats/rss-0.91.dtd">&lt;rss-0.91.dtd&gt;</a>)</li>
<li><a href="rss_osdnnews.php">Misc SF Statistics</a></li>
</ul>

<h4>Site Information</h4>
<ul>
<li><a href="rss_sfprojects.php"><?php echo $GLOBALS['sys_name']; ?> Full Project Listing</a>
(<a href="http://my.netscape.com/publish/formats/rss-spec-0.91.html">RSS 0.91</a>,
<a href="http://my.netscape.com/publish/formats/rss-0.91.dtd">&lt;rss-0.91.dtd&gt;</a>)</li>
<li><a href="trove_tree.php"><?php echo $GLOBALS['sys_name']; ?> Trove Categories Tree</a>
(<a href="http://www.w3.org/XML">XML</a>,
<a href="trove_tree_0.1.dtd">&lt;trove_tree_0.1.dtd&gt;</a>)</li>
</ul>

<h4>Project Information</h4>
<p>
All links below require <tt>?group_id=</tt> parameter with id of specific
group. Exports which provide en masse access to data which otherwise
project property, require project admin privilege.
</p>

<ul>
<li><a href="nitf_sfforums.php">Project Forums</a>
(<a href="sf_forum_0.1.dtd.txt">&lt;sf_forum_0.1.dtd&gt;</a>)</li>
<li><a href="bug_dump.php">Project Bugs</a>
(<a href="sf_bugs_0.1.dtd">&lt;sf_bugs_0.1.dtd&gt;</a>)</li>
<li><a href="patch_dump.php">Project Patches</a>
(<a href="sf_patches_0.1.dtd">&lt;sf_patches_0.1.dtd&gt;</a>)</li>
</ul>

<h3>
HTML Exports
</h3>
<p>
While XML data allows for arbitrary processing and formatting, many
projects will find ready-to-use HTML exports suitable for their needs.
For details, check <a href="http://sourceforge.net/docman/display_doc.php?docid=1502&amp;group_id=1">this</a> out.
</p>



<?php
$HTML->footer(array());
?>
