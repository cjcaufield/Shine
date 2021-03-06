<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'stats';

	$applications = DBObject::glob('Application', 'SELECT * FROM shine_applications ORDER BY name');

	$db = Database::getDatabase();
	$keys = $db->getValues("SELECT DISTINCT(`key`) FROM shine_sparkle_data");

	$charts = array();
	foreach($keys as $k)
	{
		$data = array();
		$rows = $db->getRows("SELECT COUNT(*) as num, `data` FROM shine_sparkle_data WHERE `key` = '$k' GROUP BY `data` ORDER BY num DESC");
		
		$count = 0;
		$total = 0;
		foreach($rows as $row)
		{
			if($count++ < 5) // Limit the pie chart to the top 5 values
			{
				$data[$row['data']] = $row['num'];
				$total += $row['num'];
			}
		}
		
		$charts[$k] = $data;
	}
	
	unset($charts['id']);
	unset($charts['appName']);
	unset($charts['appVersion']);
?>
<?PHP include('inc/header.inc.php'); ?>

        <div id="bd">
            <div id="yui-main">
                <div class="yui-b"><div class="yui-g">


                    <div class="block tabs spaces">
                        <div class="hd">
                            <h2>Sparkle Stats</h2>
							<ul>
								<li class="<?PHP if(!isset($_GET['id'])) echo 'active'; ?>"><a href="stats.php">All Apps</a></li>
								<?PHP foreach($applications as $a) : ?>
								<li class="<?PHP if(@$_GET['id'] == $a->id) echo 'active'; ?>"><a href="stats.php?id=<?PHP echo $a->id; ?>"><?PHP echo $a->name; ?></a></li>
								<?PHP endforeach; ?>
							</ul>
							<div class="clear"></div>
                        </div>
					</div>
					
					<?PHP foreach($charts as $title => $data) : ?>
					<div class="block" style="float:left;margin-right:2em;">
						<div class="hd">
							<h2><?PHP echo $title; ?></h2>
						</div>
						<div class="bd">
							<?PHP
								$gc = new googleChart(implode(',', $data), 'pie');
								$gc->setLabels(implode('|', array_keys($data)));
								$gc->draw(true);
							?>
						</div>
					</div>
					<?PHP endforeach; ?>
              
                </div></div>
            </div>
            <div id="sidebar" class="yui-b">

            </div>
        </div>

<?PHP include('inc/footer.inc.php'); ?>
