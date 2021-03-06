<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 3.2//EN">
<html>
	<head>
		<link rel="shortcut icon" href="favicon.ico">
		<title>FusionForge home page</title>
		<style type="text/css" media="screen,projection">/*<![CDATA[*/ @import "main.css"; /*]]>*/</style>
		<link rel="stylesheet" type="text/css" href="https://fusionforge.org/themes/css/gforge.css" />
		<link rel="stylesheet" type="text/css" href="https://fusionforge.org/themes/gforge/css/theme.css" />
		<script type="text/javascript" src="tabber.js"></script>
		<link rel="stylesheet" href="tabber.css" TYPE="text/css" MEDIA="screen">
		<script type="text/javascript">
		document.write('<style type="text/css">.tabber{display:none;}<\/style>');
		</script>
	</head>
	<body>
	<blockquote>

		<table border="0" width="100%" bgcolor=black cellspacing="0" cellpadding="0">
		<tr>
				<td class="topLeft"><a href="https://fusionforge.org/"><img src="top-logo.png" border="0" alt="" width="300" height="54" /></a></td>
				<td width="100%" background="box-grad2.png"></td>
		</tr>
		</table>

		<h4>[<a HREF="http://packages.qa.debian.org/g/gforge.html">Forge package</a> |
		<a HREF="http://fusionforge.org">FusionForge Project</a> |
		<a href="http://wiki.planetforge.org/">PlanetForge Wiki</a> |
		<a href="http://planetforge.org/">PlanetForge Agregator</a>]</h4>
		</div>

		<div id="tabber" class="tabber" >
			<div class="tabbertab" title="FusionForge">
				<div align="center"><img src="fusionforge-resized-transp.png" /></div>
			</div>
			<div class="tabbertab" title="Debian">
				<h4>For debian you should create /etc/apt/sources.list.d/fusionforge file containing one of:</h4>
				<pre>deb http://<?php echo $_SERVER['SERVER_NAME']; ?>/debian lenny main
deb-src http://<?php echo $_SERVER['SERVER_NAME']; ?>/debian lenny main</pre>
				<pre>deb http://<?php echo $_SERVER['SERVER_NAME']; ?>/debian sid main
deb-src http://<?php echo $_SERVER['SERVER_NAME']; ?>/debian sid main</pre>
				<h5>You may have to add the archive key by running:</h5>
				<pre>wget -q http://<?php echo $_SERVER['SERVER_NAME']; ?>/key -O- | sudo apt-key add -</pre>
				<h5>then run the usual</h5>
				<pre>apt-get update ; apt-get install fusionforge</pre>
			</div>
			<div class="tabbertab" title="Ubuntu">
				<h4>For ubuntu you should create /etc/apt/sources.list.d/fusionforge file containing one of:</h4>
				<pre>deb http://<?php echo $_SERVER['SERVER_NAME']; ?>/ubuntu gutsy main
deb-src http://<?php echo $_SERVER['SERVER_NAME']; ?>/ubuntu gutsy main</pre>
				<pre>deb http://<?php echo $_SERVER['SERVER_NAME']; ?>/ubuntu hardy main
deb-src http://<?php echo $_SERVER['SERVER_NAME']; ?>/ubuntu hardy main</pre>
				<pre>deb http://<?php echo $_SERVER['SERVER_NAME']; ?>/ubuntu intrepid main
deb-src http://<?php echo $_SERVER['SERVER_NAME']; ?>/ubuntu intrepid main</pre>
				<h5>You may have to add the archive key by running:</h5>
				<pre>wget -q http://<?php echo $_SERVER['SERVER_NAME']; ?>/key -O- | sudo apt-key add -</pre>
				<h5>then run the usual</h5>
				<pre>apt-get update ; apt-get install fusionforge</pre>
			</div>
			<div class="tabbertab" title="RedHat / CentOS">
				<h4>For Redhat or CentOS, just download the <a href="https://fusionforge.org/frs/?group_id=6">latest tarball</a> from the project</h4>
				<p>Unpack the archive:</p>
				<pre>tar jxf fusionforge-4.7.1.tar.bz2
cd fusionforge-4.7.1</pre>
				<p>Add run the installation script:</p>
				<pre>sh ./install.sh &lt;hostname&gt;</pre>
				<p>Note: A Yum repository for RPM based packages is under contruction.</p>
			</div>
			<div class="tabbertab" title="Install CD">
				<iframe id="installcd" src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/installcd/" name="InstallCD" frameborder="0" height="50%" width="100%" scrolling="auto"></iframe>
				<!--
				<iframe id="installcd" src="http://www.planetforge.org/downloads/" name="InstallCD" frameborder="0" height="50%" width="100%" scrolling="auto"></iframe>
				-->
			</div>
			<div class="tabbertab" title="Demo">
				<h4>You can try the latest svn build of FusionForge 4.7 on a demo website.</h4>
				<p>URL: <a href="http://fusionforgedemo.no-ip.org/">http://fusionforgedemo.no-ip.org/</a></p>
				
				<p>Please be aware that this machine is recreated every day, 
				so all your projects, tests and messages will be destroyed.</p>

				<P>To login, use <b>ffadmin</b> as login and <b>ffadmin</b> as password. 
				One logged, you will get full admin rights.</p>
			</div>
			<div class="tabbertab" title="VMWare image">
				<h4>You can download a precreated VMWare image (version: 4.7.1).</h4>
				<p><a href="http://aljeux.free.fr/fusionforge/files/gf47centos52.zip">gf47centos52.zip</a></p>
				<p>Root password is : <b>fusionforge</b></p>
				<p>Once logged as root, get the ip address of the machine with the following command:</p>
				<pre>ifconfig eth0</pre>
				<p>The IP address is displayed next to the 'inet adr:' field.</p>
				<p>Then, edit your /etc/hosts file to add 'centos52.local with the IP (example with IP 192.168.0.51)</p>
				<pre>192.168.0.51  centos52.local</pre>
				<p>You can then point your browser to <a href="http://centos52.local">http://centos52.local/</a></p>
				<p>The FusionForge admin account is: <b>ffadmin</b> (and password: <b>ffadmin</b>).</p>
			</div>
			<div class="tabbertab" title="Sources">
				<h4>FusionForge is currently managed with Subversion.</h4>

				<p>Anonymous read-only access:<br />
				<tt>svn checkout svn://scm.fusionforge.org/fusionforge/trunk fusionforge-trunk</tt></p>
				<p>Read/write access (for project members):<br />
				<tt>svn checkout svn+ssh://scm.fusionforge.org/srv/svn/fusionforge/trunk fusionforge-trunk</tt></p>
				<p>Anonymous read-only Bazaar gateway (updated hourly):<br />
				<tt>bzr checkout http://scm.fusionforge.org/bzr/fusionforge/svn-trunk-ro fusionforge-trunk</tt></p>

				<p>Git gateway:<br />
				<tt>git clone git@github.com:yourlogin/fusionforge.git</tt></p>

				<p>(Note to project members: due to virtualisation on the server, SSH needs to be accessed on port 20022.)</p>
			</div>
			<div class="tabbertab" title="Wiki">
				<h4>FusionForge currently uses the PlanetForge wiki.</h4>

				<p><a href="http://wiki.planetforge.org/index.php/FusionForge">Browse the wiki</a></p>
			</div>
			<div class="tabbertab" title="Online help">
				<h5>For more info you can join FusionForge developpers on IRC at:</h5>
				<pre>irc.freenode.net channel #fusionforge </pre>
				<script>
				function bigit() {
					document.getElementById('myiframe').width = '100%, *';
					document.getElementById('myiframe').height = '100%, *';
				}
				function smallit() {
					document.getElementById('myiframe').width = '100%, *';
					document.getElementById('myiframe').height = '40%, *';
				}
				</script>
				<h6>Join #fusionforge on irc.freenode.net. Choose a nick and enter password if you have one. </h6>
				<form>
					<input id="mybutton1" type="button" onClick=bigit() value="Big Window">
					<input id="mybutton2" type="button" onClick=smallit() value="Small Window">
				</form>
				<iframe id="myiframe" width=100% height=40% scrolling=no style="border:0" src="http://embed.mibbit.com/?server=irc.freenode.net&channel=%23fusionforge&noServerNotices=true&noServerMotd=true&promptPass=true"></iframe>
			</div>
		</div>
	</blockquote>
	</body>
</html>
