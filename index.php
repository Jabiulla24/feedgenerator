<?php

$inputfile=$argv[1];
$filename=basename($inputfile);
$outputdirectory=$argv[3];
if (!file_exists($outputdirectory)) {
    mkdir($outputdirectory, 0777, true);
}
$outputfile=$argv[3].'/'.$filename.'.xml';
echo $outputfile;

$myfile = fopen($outputfile, "w");

$files = glob($inputfile.'/*.{jpg,png,gif,jpeg}',GLOB_BRACE);
$files = array_combine($files, array_map("filemtime", $files));
$i=10;
$inputurl=$argv[2];
//$url='http://192.168.1.15/imagefiles/images/';
$url='http://'.$inputurl.':8182/iiif/2/'.$filename.'%2F';

$thumbimage='/full/300,/0/default.jpg';
$fullimage='/full/full/0/default.jpg';
arsort($files);

$files = array_keys($files);

$header='<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0"> 
<channel>
    <title>Title of Your Feed</title>
    <description>Description of Your Feed</description>
    <link>A homepage for your feed</link>
    <language>en-us</language>
    <itunes:image href="http://path/to/feed/icon.jpg"/>
';
fwrite($myfile,$header);
foreach ( $files as $file) {
	echo $file;
  // the filenames that I'm working with include the date in them
  // with a little slicing and dicing of the names, I can get what I need for the date variable
  // there are likely better ways of doing this, but this seemed the mostly reliable route to take in my case
	$date  = date(DATE_ATOM,mktime(0, 0, 0, date("m")  , date("d"), date("Y")));

  // remove the directory structure from the filename
  // again, there are likely better ways of doing this, but I was on a preg_replace roll
  $file = preg_replace('/.*Filename/', 'Filename', $file);
	$base=basename($file);
	//echo $base;
  $body="<item>
      <title>Title " . $date . "</title>
      <link>Link</link>
      <guid>"  .$file.  "</guid>
      <pubDate>" . $date . "</pubDate>
      <enclosure url='$url$base$thumbimage' type='image/jpeg'/>
	   <enclosure url='$url$base$fullimage' type='image/jpeg'/>
    </item>
  ";
fwrite($myfile,$body);
  // let's stop after creating an RSS item for 10 files
  //if (++$i == 9) break;
}

$footer="
  </channel>

</rss>
";
fwrite($myfile,$footer);
fclose($myfile);
?>
