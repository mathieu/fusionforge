#! /usr/bin/php4 -f
<?php
/**
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id$
  *
  */

require ('squal_pre.php');

for ($i=0; $i<510; $i++) {

$time = time()-(86400 * $i);

$last_week= ( $time - (86400 * 7) );  
$this_week = ( $time );

$last_year=date('Y',$last_week);
$last_month=date('m',$last_week);
$last_day=date('d',$last_week);

$this_year=date('Y',$this_week);
$this_month=date('m',$this_week);
$this_day=date('d',$this_week);

print "\nlast_week: $last_week $last_year $last_month $last_day ";

#create a table to put the aggregates in
$sql="CREATE TABLE project_counts_weekly_tmp (
group_id int,
type text,
count float(8))";
$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}



#forum messages
$sql="INSERT INTO project_counts_weekly_tmp 
SELECT forum_group_list.group_id,'forum',log(3 * count(forum.msg_id)::float) AS count 
FROM forum,forum_group_list 
WHERE forum.group_forum_id=forum_group_list.group_forum_id 
AND date > '$last_week' 
AND date < '$this_week'
GROUP BY group_id";
$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}


#project manager tasks
$sql="INSERT INTO project_counts_weekly_tmp 
SELECT project_group_list.group_id,'tasks',log(4 * count(project_task.project_task_id)::float) AS count 
FROM project_task,project_group_list 
WHERE project_task.group_project_id=project_group_list.group_project_id 
AND end_date > '$last_week'
AND end_date < '$this_week' 
GROUP BY group_id";

#print "\n\n".$sql;

$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}

#bugs
$sql="INSERT INTO project_counts_weekly_tmp 
SELECT agl.group_id,'bugs',log(3 * count(*)::float) AS count 
FROM artifact_group_list agl,artifact a
WHERE a.open_date > '$last_week'
AND a.open_date < '$this_week'
AND a.group_artifact_id=agl.group_artifact_id 
AND agl.datatype='1'
GROUP BY agl.group_id";

#print "\n\n".$sql;

$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}


#patches
$sql="INSERT INTO project_counts_weekly_tmp 
SELECT agl.group_id,'patches',log(10 * count(*)::float) AS count 
FROM artifact_group_list agl,artifact a
WHERE a.open_date > '$last_week'
AND a.open_date < '$this_week'
AND a.group_artifact_id=agl.group_artifact_id 
AND agl.datatype='3'
GROUP BY agl.group_id";

#print "\n\n".$sql;

$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}


#support
$sql="INSERT INTO project_counts_weekly_tmp 
SELECT agl.group_id,'support',log(5 * count(*)::float) AS count 
FROM artifact_group_list agl,artifact a
WHERE a.open_date > '$last_week'
AND a.open_date < '$this_week'
AND a.group_artifact_id=agl.group_artifact_id 
AND agl.datatype='2'
GROUP BY agl.group_id";

#print "\n\n".$sql;

$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}


#file releases
$sql="INSERT INTO project_counts_weekly_tmp 
select frs_package.group_id,'filereleases',log(5 * count(*)::float)
FROM frs_release,frs_package
WHERE 
	frs_package.package_id = frs_release.package_id 
	AND frs_release.release_date > '$last_week'
	AND frs_release.release_date < '$this_week'
GROUP BY frs_package.group_id";
$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}


#create a new table to insert the final records into
$sql="CREATE TABLE project_metric_weekly_tmp1 (
ranking serial primary key,
group_id int not null,
value float (10))";
$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}



#insert the rows into the table in order, adding a sequential rank #
$sql="INSERT INTO project_metric_weekly_tmp1 (group_id,value) 
SELECT project_counts_weekly_tmp.group_id,sum(project_counts_weekly_tmp.count) AS value 
FROM project_counts_weekly_tmp
WHERE
project_counts_weekly_tmp.count > 0
GROUP BY group_id ORDER BY value DESC";
$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}

#numrows in the set
$sql="SELECT count(*) FROM project_metric_weekly_tmp1";
$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}


$counts = db_result($rel,0,0);
print "\n\nCounts: ".$counts;

db_begin();

#drop the old metrics table
$sql="DELETE FROM project_weekly_metric";
$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}


$sql="INSERT INTO project_weekly_metric (ranking,percentile,group_id)
SELECT ranking,to_char((100-(100*((ranking::float-1)/$counts))), '999D9999'),group_id
FROM project_metric_weekly_tmp1
ORDER BY ranking ASC";
$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}

//
//	Now archive the metric
//
db_query("DELETE FROM stats_project_metric WHERE month='$this_year$this_month' AND day='$this_day'");

$sql="INSERT INTO stats_project_metric (month,day,group_id,ranking,percentile) 
SELECT '$this_year$this_month'::int, '$this_day'::int,group_id,ranking,percentile
FROM project_weekly_metric";
$rel = db_query($sql);
if (!$rel) {
	echo "<p>$sql<p>".db_error();
	echo db_error();
}

db_commit();
echo db_error();


$rel=db_query("DROP TABLE project_counts_weekly_tmp;");
$rel=db_query("DROP TABLE project_metric_weekly_tmp1;");
$rel=db_query("DROP SEQUENCE project_metric_week_ranking_seq;");

}

?>
