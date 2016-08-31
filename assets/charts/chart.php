<?php // content="text/plain; charset=utf-8"
 
define('__ROOT__', dirname(dirname(__FILE__))); 
require_once ('jpgraph-4.0.0/src/jpgraph.php');
require_once ('jpgraph-4.0.0/src/jpgraph_bar.php');
require_once ('jpgraph-4.0.0/src/jpgraph_error.php');
 
 
$x_axis = array();
$y_axis = array();
$x2_axis = array();
$y2_axis = array();
$i = 0;
$j = 0; 
$con=mysqli_connect("localhost","root","!!!power4all","dhcp");
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
 
$result = mysqli_query($con,"select subnet,count(*) as total, IFNULL(free, 0) used from ip subnet left join (select substring_index(ip, '.', 3) as tmp, count(*) as free from dhcp.dynamic group by tmp) as t on subnet=t.tmp where subnet is not null group by subnet order by subnet desc;");
while($row = mysqli_fetch_array($result))
{
	$x_axis[$i] =  $row["subnet"];
	$y_axis[$i] = $row["used"];
	    $i++;
}
mysqli_close($con);

$con2=mysqli_connect("localhost","root","!!!power4all","dhcp");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result2 = mysqli_query($con2,"select IFNULL(ip.subnet, 0) subnet,count(*) as total, IFNULL(free, 0) free, IFNULL(used, 0) used, IFNULL((free-used),free) calc from ip left join (select subnet, count(*) as free from dhcp.ip where ip_type='free' or ip_type='dynamic' group by subnet) as t on ip.subnet=t.subnet left join (select substring_index(ip, '.', 3) as tmp, count(*) as used from dhcp.dynamic group by tmp) as t2 on t.subnet=t2.tmp where ip.subnet is not null group by subnet order by subnet desc;");
while($row = mysqli_fetch_array($result2))
{
        $x2_axis[$j] =  $row["subnet"];
        $y2_axis[$j] = $row["calc"];
            $j++;
}     
mysqli_close($con2);


// Basic initialization
$dt = new DateTime();
$currentDate = date("D M d, Y H:i:s");
$title = 'Dynamic IP usage per subnet';
$subtitle = '(includes active leases only)';
$subsubtitle = $currentDate;
$title2 = 'Free IP count per subnet';
$subtitle2 = '(includes dynamic/free only)';

// Size of graph
$width=500;
$height=550;
 
// Set the basic parameters of the graph
$graph = new Graph($width,$height);
$graph->SetScale('textlin');
$graph->img->SetMargin(40,30,20,40);

// Construct the 2nd graph
$graph2 = new Graph($width,$height);
$graph2->SetScale('textlin');
$graph2->img->SetMargin(40,30,20,40);
 
$top = 100;
$bottom = 0;
$left = 80;
$right = 0;
$graph->Set90AndMargin($left,$right,$top,$bottom);
$graph2->Set90AndMargin($left,$right,$top,$bottom);
 
// Label align for X-axis
$graph->xaxis->SetLabelAlign('right','center','right');
$graph2->xaxis->SetLabelAlign('right','center','right');

// Label align for Y-axis
$graph->yaxis->SetLabelAlign('center','bottom');
$graph2->yaxis->SetLabelAlign('center','bottom');

// Setup the titles for graph1
$graph->title->Set($title);
$graph->subtitle->Set($subtitle);
$graph->subsubtitle->Set($subsubtitle);
$graph->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,10);
$graph->subtitle->SetFont(FF_DV_SANSSERIF,FS_NORMAL,8);
$graph->subsubtitle->SetFont(FF_DV_SANSSERIF,FS_BOLD,8);
$graph->xaxis->SetTickLabels($x_axis);
$graph->xaxis->title->Set('');
$graph->yaxis->title->Set('');
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);

// Setup the titles for graph2
$graph2->title->Set($title2);
$graph2->subtitle->Set($subtitle2);
$graph2->subsubtitle->Set($subsubtitle);
$graph2->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,10);
$graph2->subtitle->SetFont(FF_DV_SANSSERIF,FS_NORMAL,8);
$graph2->subsubtitle->SetFont(FF_DV_SANSSERIF,FS_BOLD,8);
$graph2->xaxis->SetTickLabels($x2_axis);
$graph2->xaxis->title->Set('');
$graph2->yaxis->title->Set('');
$graph2->xaxis->title->SetFont(FF_DV_SANSSERIF,FS_BOLD);
$graph2->yaxis->title->SetFont(FF_DV_SANSSERIF,FS_BOLD);

// Add some grace to the top so that the scale doesn't
// end exactly at the max value.
$graph->yaxis->SetLabelAngle(35); 
$graph->yaxis->scale->SetGrace(30);

$graph2->yaxis->SetLabelAngle(35); 
$graph2->yaxis->scale->SetGrace(30);

// Create a bar plot
$bplot = new BarPlot($y_axis);
$graph->Add($bplot); 

// Create a bar plot for graph2
$bplot2 = new BarPlot($y2_axis);
$graph2->Add($bplot2); 

// Setup the values that are displayed on top of each bar
$bplot->SetValuePos('top');
$bplot->SetWidth(0.4);
$bplot->value->HideZero();
$bplot->value->SetColor("black","red");
$bplot->value->SetFormat('%d IPs');
$bplot->value->Show();

$bplot2->SetValuePos('top');
$bplot2->SetFillColor("#5cb85c");
$bplot2->SetWidth(0.4);
$bplot2->value->HideZero();
$bplot2->value->SetColor("black","red");
$bplot2->value->SetFormat('%d IPs');
$bplot2->value->Show();

// Display the graph
$graph->Stroke("assets/images/tmp/subnet_overview.png");
$graph2->Stroke("assets/images/tmp/subnet_free_overview.png");
?> 
 
