<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//

require "pre.php";

$HTML->header(array(title=>"Project Exports"));
?>
<h2>RSS/XML Exports</h2>

<p><?php print $GLOBALS[sys_default_name] ?> data is exported in a variety of standard formats. Many of
the export URLs can also accept form/get data to customize the output. All
data generated by these pages is realtime.

<h3>News Data</h3>
<p>
To get Project News or New Project Releases of a specific project use the Links below.
<ul>
<li><a href="rss_sfnews.php?group_id=<?php echo getIntFromRequest(group_id); ?>"><?php print $GLOBALS[sys_name] ?> Developer Project News</a>
(<a href="http://my.netscape.com/publish/formats/rss-spec-0.91.html">RSS 0.91</a>,
<a href="http://my.netscape.com/publish/formats/rss-0.91.dtd">&lt;rss-0.91.dtd&gt;</a>)</li>
<li><a href="rss_sfnewreleases.php?group_id=<?php echo getIntFromRequest(group_id); ?>"><?php print $GLOBALS[sys_name] ?> Developer New Project Releases</a>
(<a href="http://my.netscape.com/publish/formats/rss-spec-0.91.html">RSS 0.91</a>,
<a href="http://my.netscape.com/publish/formats/rss-0.91.dtd">&lt;rss-0.91.dtd&gt;</a>)</li>
</ul>


<ul>
<li><a href="rss20_news.php?group_id=<?php echo getIntFromRequest(group_id); ?>"><?php print $GLOBALS[sys_name] ?> Developer Project News</a>
(<a href="http://blogs.law.harvard.edu/tech/rss">RSS 2.0</a>)</li>
<li><a href="rss20_newreleases.php?group_id=<?php echo getIntFromRequest(group_id); ?>"><?php print $GLOBALS[sys_name] ?> Developer New Project Releases</a>
(<a href="http://blogs.law.harvard.edu/tech/rss">RSS 2.0</a>)</li>
</ul>

<a HREF="javascript:history.go(-1)">[Go back]</a>
<br>
<?php
$HTML->footer(array());
?>
