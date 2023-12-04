<?php

	//----------------------------------------------------------
	/**
	* Get the paging information to display at the bottom
    * of the page for navigation
	*
	* @param string $pagesize
    * @param int $startrow
    * @param int $pageno
    * @param int $totalrowcount
    *
	* @return string
	*/
    function displaypaging($pagesize, $startrow, $pageno, $totalrowcount) {

		    //echo("Page size : $pagesize<br>");
		    //echo("Start row : $startrow<br>");
		    //echo("Page number : $pageno<br>");
		    //echo("Total number of rows : $totalrowcount<br>");
            //exit();

		    //If number of rows <= the page size, then no of pages = 1
		    if ($totalrowcount <= $pagesize) {
			    $noofpages = 1;
		    } else {
			    $noofpages = ceil($totalrowcount / $pagesize) ;			
			    //echo("Number of pages : $noofpages ");
		    }

        $previouspageno = $pageno - 1;
        $nextpageno = $pageno + 1;    
    
        $result = '';
        $result = $result . '<nav aria-label="Page navigation">';
        $result = $result . '<ul class="pagination justify-content-end">';    
        if ($pageno == 1) { 
            $result = $result . '<li class="page-item disabled">';
            $result = $result . '<a class="page-link">Previous</a>';
            $result = $result . '</li>';
        }
        else
        {
            $result = $result . '<li class="page-item">';
            $result = $result . '<a class="page-link" href=' . $_SERVER['PHP_SELF'] . '?page=' . $previouspageno . '>Previous</a>';
            $result = $result . '</li>';
        }
    
		    $c = 1;
		    for ($pageno; $c < $noofpages + 1; $c++) {
		
			    if ($c == $pageno) { //active link
				    $result = $result . '<li class="page-item active"><a class="page-link" href=' . $_SERVER['PHP_SELF'] . '?page=' . $c . '>' . $c . '</a></li>';
			    } else {
				    $result = $result . '<li class="page-item"><a class="page-link" href=' . $_SERVER['PHP_SELF'] . '?page=' . $c . '>' . $c . '</a></li>';
			    }
			
		    }
    
        if ($nextpageno <= $noofpages)
        {
            $result = $result . '<li class="page-item">';
            $result = $result . '<a class="page-link" href=' . $_SERVER['PHP_SELF'] . '?page=' . $nextpageno . '>Next</a>';
            $result = $result . '</li>';
        }
        else
        {
            $result = $result . '<li class="page-item disabled">';
            $result = $result . '<a class="page-link">Next</a>';
            $result = $result . '</li>';        
        }    
    
        //$result = $result . '</li>';
        $result = $result . '</ul>';
        $result = $result . '</nav>';    

        return $result;
    }
    //----------------------------------------------------------
	/**
	* Gets the number of rows in the table tblitems
	*
	* @param string $condition
    *
	* @return string
	*/
    function getrowcount($condition) {

	    return count($items);

    }
   
   
?>
