<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>/css/style.css?v=1" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>/script/ui/css/redmond/jquery-ui-1.8.12.custom.css" />
<script type="text/javascript" language="javascript" src="<?php echo $base_url; ?>/script/jquery-1.6.1.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $base_url; ?>/script/ui/jquery-ui-1.8.12.custom.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $base_url; ?>/script/main.js"></script>
<script type="text/javascript">

	$(document).ready(function() {
		$(".perPage").change(function(){
			$.get("<?php echo $base_url; ?>/home/setNewItemsPerPage/" + $(this).val(), function(){
				var currentUrl = '<?php echo $currentUrl;?>';
				var resetPagPos = currentUrl.indexOf("resetPagination");
				if (resetPagPos === -1) {
					var qmarkPos = currentUrl.indexOf("?");
					if (qmarkPos === -1) {
						window.location.href = currentUrl + "?resetPagination=true&amp;posts=display";
					} else {
						if (qmarkPos === (window.location.href.length - 1))
							window.location.href = currentUrl + "resetPagination=true&amp;posts=display";
						else
							window.location.href = currentUrl + "&amp;resetPagination=true";
					}
				} else {
					window.location.href = currentUrl;
				}
			})
		});

		$( ".datepicker" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			onSelect: function(dateText, inst) {
				document.eventFilters.submit();
			}
		});

		function submitForm() {
			$('#hdPageNumber').val(1);
			document.eventFilters.submit();
		}
	});

	function getExcelReport() {
		let filterFileName = $("#filterFileName").val();
		if (filterFileName === "") {
			filterFileName = "null";
		}

		let filterFileDateFrom = $("#filterFileDateFrom").val();
		if (filterFileDateFrom === "") {
			filterFileDateFrom = "1980-01-01";
		}

		let filterFileDateTo = $("#filterFileDateTo").val();
		if (filterFileDateTo === "") {
			filterFileDateTo = "2100-01-01";
		}

		//generate excel for every possible outcome
		$.get("<?php echo $base_url; ?>/home/createExcel/" + filterFileName + "/" + filterFileDateFrom+ "/" + filterFileDateTo + "/upload" , function(res){
			window.location = '<?php echo $base_url; ?>' + '/upload/' + res;
		 })
	 }

	function getExcelDailyReport() {
		// generate excel as daily report
		$.get("<?php echo $base_url; ?>/home/createExcelDaily/" , function(res){
			window.location = '<?php echo $base_url; ?>' + '/uploadDaily/' + res;
		})
	}

	function setPagination(position)
	{
		var currentPosition = parseInt($('#hdPageNumber').val());
		var isWorking = true;

		if (position == "leftTotal")
		{
			if (currentPosition > 1) { $('#hdPageNumber').val(1); } else { isWorking = false; }
		}
		else if (position == "rightTotal")
		{
			if (currentPosition < <?php echo $numberOfPages; ?>) { $('#hdPageNumber').val(<?php echo $numberOfPages; ?>); } else { isWorking = false; }
		}
		else if (position == "left")
		{
			if (currentPosition > 1) { $('#hdPageNumber').val((currentPosition - 1)); } else { isWorking = false; }
		}
		else
		{
			if (currentPosition < <?php echo $numberOfPages; ?>) { $('#hdPageNumber').val((currentPosition + 1)); } else { isWorking = false; }
		}

		if (isWorking == true)
		{
			document.eventFilters.submit();
		}

		return false;
	}

</script>
</head>
<body>
<div id="divContainer">
	<?php $this->load->view('header_view'); ?>
     <div id="content">
		 <h1 class="title"><?php echo "Files Log"; ?></h1>
		 <div class="headButtons clearfix">
			 <a href="javascript: void(0);" onclick="getExcelReport()" style="color: black;" class="headButton backLink2 tinkButton"><?php echo 'Export Data as Excel File';?></a><br />
			 <a href="javascript: void(0);" onclick="getExcelDailyReport()" style="color: black;" class="headButton backLink2 tinkButton"><?php echo 'Get Daily Report';?></a>
		 </div>

		 <div class="delimiterLine"></div>
		 <div id="navigation">
			 <a href="javascript:void(0);" onclick="setPagination('leftTotal')" class="<?php echo $hideNavigation; ?>" ><img src="<?php echo $base_url;?>/images/nav_left_2<?php echo $imageLeft; ?>.png" width="19" height="19" border="0" id="leftTotal" /></a>
			 <a href="javascript:void(0);" onclssick="setPagination('left')" class="<?php echo $hideNavigation; ?>"><img src="<?php echo $base_url;?>/images/nav_left<?php echo $imageLeft; ?>.png" width="19" height="19" border="0" id="left" /></a>
			 <span class="<?php echo $hideNavigation; ?>"><?php echo $currentPage; ?>/<?php echo $numberOfPages; ?></span>
			 <a href="javascript:void(0);" onclick="setPagination('right')" class="<?php echo $hideNavigation; ?>"><img src="<?php echo $base_url;?>/images/nav_right<?php echo $imageRight; ?>.png" width="19" height="19" border="0" id="right" /></a>
			 <a href="javascript:void(0);" onclick="setPagination('rightTotal')" class="<?php echo $hideNavigation; ?>"><img src="<?php echo $base_url;?>/images/nav_right_2<?php echo $imageRight; ?>.png" width="19" height="19" border="0" id="rightTotal" /></a>
			 <select name="perPage" id="perPage" class="perPage">
				 <option value="10" <?php if ($perPage == "10") echo "selected"; ?>>10 <?php echo 'files'; ?></option>
				 <option value="50" <?php if ($perPage == "50") echo "selected"; ?>>50 <?php echo 'files'; ?></option>
				 <option value="100" <?php if ($perPage == "100") echo "selected"; ?>>100 <?php echo 'files'; ?></option>
			 </select>
		 </div>
		 <form name="eventFilters" id="eventFilters" action="<?php echo $base_url; ?>/home/" method="get" onsubmit="submitForm();">
			 <table name="tbNews" class="tbTink" border="0" cellpadding="0" cellspacing="0" width="100%">
				 <tr>
					 <th width="10%"><?php echo 'ID'; ?></th>
					 <th width="15%"><?php echo 'File Name'; ?></th>
					 <th width="15%"><?php echo 'File Size'; ?></th>
					 <th width="23%"><?php echo 'Encryption Status'; ?></th>
					 <th width="11%"><?php echo 'Location'; ?></th>
					 <th width="11%"><?php echo 'File timestamp'; ?></th>
					 <th width="8%" class="thBorderRight"><?php echo 'Timestamp (DB log)'; ?></th>
				 </tr>
				 <tr class="filtersTr">
					 <th></th>
					 <th align="center"><input type="text" name="filterFileName" id="filterFileName" class="filter" value="<?php echo $filterFileName; ?>" style="width: 96%;" /></th>
					 <th></th>
					 <th</th>
					 <th></th>
					 <th></th>
					 <th>
						 <input type="text" name="filterFileDateFrom" id="filterFileDateFrom" class="filter datepicker" value="<?php echo $filterFileDateFrom;?>" style="width: 50%;" />
						 <input type="text" name="filterFileDateTo" id="filterFileDateTo" class="filter datepicker" value="<?php echo $filterFileDateTo;?>" style="width: 50%;" />
					 </th>
					 <th class="thBorderRight"></th>
				 </tr>
				 <?php
				 if (!empty ($filesList)):
					 $rowCounter = 1;
					 $totalRows = count($filesList);
					 foreach ($filesList as $row):
						 ?>
						 <tr class="<?php if ($rowCounter == $totalRows) echo "lastColumn"; ?>" height="50">
							 <td align="center" style="padding: 5px;"><?php echo $row->id; ?></td>
							 <td align="center" style="padding: 5px;"><?php echo $row->fileName; ?></td>
							 <td align="center" style="padding: 5px;"><?php echo $row->fileSize; ?></td>
							 <td align="center" style="padding: 5px;"><?php echo $row->encryptionStatus; ?></td>
							 <td align="center" style="padding: 5px;"><?php echo $locationList[$row->fileLocation]; ?></td>
							 <td align="center" style="padding: 5px;"><?php echo $row->fileTimestamp; ?></td>
							 <td align="center" class="thBorderRight"><?php echo $row->timestamp; ?></td>
						 </tr>
						 <?php $rowCounter++;
						 $startingPoint++;
					 endforeach; else: ?>
					 <tr class="lastColumn">
						 <td class="thBorderRight" align="center" colspan="9" height="80" valign="middle"><?php echo 'No files found for given search criteria'; ?></td>
					 </tr>
				 <?php endif; ?>
			 </table>
			 <input type="hidden" name="hdPerPage" id="hdPerPage" value="<?php echo $numPerPage; ?>" />
			 <input type="hidden" name="hdPageNumber" id="hdPageNumber" value="<?php echo $currentPage; ?>" />
			 <input type="submit" name="submitProductsSearch" value="" style="position: absolute; left: -9999px; width: 1px; height: 1px;" />
		 </form>
		 <div id="navigation">
			 <a href="javascript:void(0);" onclick="setPagination('leftTotal')" class="<?php echo $hideNavigation; ?>" ><img src="<?php echo $base_url; ?>/images/nav_left_2<?php echo $imageLeft; ?>.png" width="19" height="19" border="0" id="leftTotal" /></a>
			 <a href="javascript:void(0);" onclick="setPagination('left')" class="<?php echo $hideNavigation; ?>"><img src="<?php echo $base_url; ?>/images/nav_left<?php echo $imageLeft; ?>.png" width="19" height="19" border="0" id="left" /></a>
			 <span class="<?php echo $hideNavigation; ?>"><?php echo $currentPage; ?>/<?php echo $numberOfPages; ?></span>
			 <a href="javascript:void(0);" onclick="setPagination('right')" class="<?php echo $hideNavigation; ?>"><img src="<?php echo $base_url; ?>/images/nav_right<?php echo $imageRight; ?>.png" width="19" height="19" border="0" id="right" /></a>
			 <a href="javascript:void(0);" onclick="setPagination('rightTotal')" class="<?php echo $hideNavigation; ?>"><img src="<?php echo $base_url; ?>/images/nav_right_2<?php echo $imageRight; ?>.png" width="19" height="19" border="0" id="rightTotal" /></a>
			 <select name="perPage" id="perPage" class="perPage">
				 <option value="10" <?php if ($perPage == "10") echo "selected"; ?>>10 <?php echo 'files'; ?></option>
				 <option value="50" <?php if ($perPage == "50") echo "selected"; ?>>50 <?php echo 'files'; ?></option>
				 <option value="100" <?php if ($perPage == "100") echo "selected"; ?>>100 <?php echo 'files'; ?></option>
			 </select>
		 </div>
     </div>
</div>
</body>
</html>
