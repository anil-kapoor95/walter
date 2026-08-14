<?php
if (isset($tpl['paginator']) && $tpl['paginator']['pages'] > 1)
{		
	$page = 1 ;
	if(isset($_GET['page']))
	{
		$page = $_GET['page'];
	}
	?>
	<footer class="pjMrBFooter">
		<nav class="pjMrBPaging">
			
			<ul class="pagination">
				<?php
				$stages = 3;
				$lastpage = $tpl['paginator']['pages'];
									
				if ($page > 1)
				{
					?>
					<li>
						<a class="pjMrbPaging" href="#" data-page="<?php echo $page - 1; ?>" aria-label="Previous">&laquo;</a>
					</li>
					<?php
				}else{
					?>
					<li>
						<a href="javascript:void(0);" aria-label="Previous">&laquo;</a>
					</li>
					<?php
				}
				if ($lastpage < 7 + ($stages * 2))
				{
					for ($counter = 1; $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
						{
							?><li class="active"><a href="javascript:void(0);"><?php echo $counter; ?></a></li><?php
						}else{
							?><li><a class="pjMrbPaging" href="#" data-page="<?php echo $counter; ?>"><?php echo $counter; ?></a></li><?php
						}
					}
				} else if ($lastpage > 5 + ($stages * 2)){
					
					if($page < 1 + ($stages * 2))		
					{
						for ($counter = 1; $counter < 4 + ($stages * 2); $counter++)
						{
							if ($counter == $page)
							{
								?><li class="active"><a href="javascript:void(0);"><?php echo $counter; ?></a></li><?php
							}else{
								?><li><a class="pjMrbPaging" href="#" data-page="<?php echo $counter; ?>"><?php echo $counter; ?></a></li><?php
							}	
						}
						?>
						<li class="visible-xs-inline-block">
							<span>...</span>
						</li>
						<li><a class="pjMrbPaging" href="#" data-page="<?php echo $lastpage - 1; ?>"><?php echo $lastpage - 1; ?></a></li>
						<li><a class="pjMrbPaging" href="#" data-page="<?php echo $lastpage; ?>"><?php echo $lastpage; ?></a></li>
						<?php
					}else if($lastpage - ($stages * 2) > $page && $page > ($stages * 2)){
						?>
						<li><a class="pjMrbPaging" href="#" data-page="1">1</a></li>
						<li><a class="pjMrbPaging" href="#" data-page="2">2</a></li>
						<li class="visible-xs-inline-block">
							<span>...</span>
						</li>
						<?php
						for ($counter = $page - $stages; $counter <= $page + $stages; $counter++){
							if ($counter == $page)
							{
								?><li class="active"><a href="javascript:void(0);"><?php echo $counter; ?></a></li><?php
							}else{
								?><li><a class="pjMrbPaging" href="#" data-page="<?php echo $counter; ?>"><?php echo $counter; ?></a></li><?php
							}
						}
						?>
						<li class="visible-xs-inline-block">
							<span>...</span>
						</li>
						<li><a class="pjMrbPaging" href="#" data-page="<?php echo $lastpage - 1; ?>"><?php echo $lastpage - 1; ?></a></li>
						<li><a class="pjMrbPaging" href="#" data-page="<?php echo $lastpage; ?>"><?php echo $lastpage - 1; ?></a></li>
						<?php
					}else{
						?>
						<li><a class="pjMrbPaging" href="#" data-page="1">1</a></li>
						<li><a class="pjMrbPaging" href="#" data-page="2">2</a></li>
						<li class="visible-xs-inline-block">
							<span>...</span>
						</li>
						<?php
						for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++)
						{
							if ($counter == $page)
							{
								?><li class="active"><a href="javascript:void(0);"><?php echo $counter; ?></a></li><?php
							}else{
								?><li><a class="pjMrbPaging" href="#" data-page="<?php echo $counter; ?>"><?php echo $counter; ?></a></li><?php
							}
						}
					}	
				}
				if ($page < $counter - 1){
					?>
					<li>
						<a href="#" class="pjMrbPaging" data-page="<?php echo $page + 1; ?>" aria-label="Next">&raquo;</a>
					</li>
					<?php
				}else{
					?>
					<li>
						<a href="javascript:void(0);" aria-label="Next">&raquo;</a>
					</li>
					<?php
				}
				?>
			</ul>
		</nav>
	</footer>
	<?php
} 
?>