<?php

class Pagination 
{
	
	/**
	 * Current Page
	 *
	 * @var integer
	 */
	var $page;
	
	/**
	 * Size of the records per page
	 *
	 * @var integer
	 */
	var $size;
	
	/**
	 * Total records
	 *
	 * @var integer
	 */
	var $total_records;
	
	/**
	 * Link used to build navigation
	 *
	 * @var string
	 */
	var $link;
	
	/**
	 * Class Constructor
	 *
	 * @param integer $page
	 * @param integer $size
	 * @param integer $total_records
	 */
	function Pagination($page = null, $size = null, $total_records = null)
	{
		$this->page = $page;
		$this->size = $size;
		$this->total_records = $total_records;
	}
	
	/**
	 * Set's the current page
	 *
	 * @param unknown_type $page
	 */
	function setPage($page)
	{
		$this->page = 0+$page;
	}
	
	/**
	 * Set's the records per page
	 *
	 * @param integer $size
	 */
	function setSize($size)
	{
		$this->size = 0+$size;
	}
		
	/**
	 * Set's total records
	 *
	 * @param integer $total
	 */
	function setTotalRecords($total)
	{
		$this->total_records = 0+$total;
	}
	
	/**
	 * Sets the link url for navigation pages
	 *
	 * @param string $url
	 */
	function setLink($url)
	{
		$this->link = $url;
	}
	
	/**
	 * Returns the LIMIT sql statement
	 *
	 * @return string
	 */
	function getLimitSql()
	{
		$sql = "LIMIT " . $this->getLimit();
		return $sql;
	}
		
	/**
	 * Get the LIMIT statment
	 *
	 * @return string
	 */
	function getLimit()
	{
		if ($this->total_records == 0)
		{
			$lastpage = 0;
		}
		else 
		{
			$lastpage = ceil($this->total_records/$this->size);
		}
		
		$page = $this->page;		
		
		if ($this->page < 1)
		{
			$page = 1;
		} 
		else if ($this->page > $lastpage && $lastpage > 0)
		{
			$page = $lastpage;
		}
		else 
		{
			$page = $this->page;
		}
		
		$sql = ($page - 1) * $this->size . "," . $this->size;
		
		return $sql;
	}
	
	/**
	 * Creates page navigation links
	 *
	 * @return 	string
	 */
	function create_links()
	{
		$totalItems = $this->total_records;
		$perPage = $this->size;
		$currentPage = $this->page;
		$link = $this->link;
		
		$totalPages = floor($totalItems / $perPage);
		$totalPages += ($totalItems % $perPage != 0) ? 1 : 0;

		if ($totalPages < 1 || $totalPages == 1){
			return null;
		}

		$output = null;
		//$output = '<span id="total_page">Page (' . $currentPage . '/' . $totalPages . ')</span>&nbsp;';
				
		$loopStart = 1; 
		$loopEnd = $totalPages;

		if ($totalPages > 5)
		{
			if ($currentPage <= 3)
			{
				$loopStart = 1;
				$loopEnd = 5;
			}
			else if ($currentPage >= $totalPages - 2)
			{
				$loopStart = $totalPages - 4;
				$loopEnd = $totalPages;
			}
			else
			{
				$loopStart = $currentPage - 2;
				$loopEnd = $currentPage + 2;
			}
		}

/*		if ($loopStart != 1){
			$output .= sprintf('<a href="' . $link . '"><img src="img/icons/go_first.png" width="16" height="16" /></a>', '1');
		} */
		
		if ($currentPage > 1){
			$output .= sprintf('<li class="previous"><a href="' . $link . '"><span><i class="fa fa-angle-left"></i> Previous</span></a></li>', $currentPage - 1);
		}
		else
			$output .= sprintf('<li class="previous uk-disabled"><a href="#"><span><i class="fa fa-angle-left"></i> Previous</span></a></li>', $currentPage - 1);
		
		for ($i = $loopStart; $i <= $loopEnd; $i++)
		{
			if ($i == $currentPage){
				$output .= '<li class="active"><a href="#">'.$i.'</a></li>';
			} else {
				$output .= sprintf('<li><a href="'.$link.'">',$i).$i.'</a></li>';
			}
		}

		if ($currentPage < $totalPages){
			$output .= sprintf('<li class="next"><a href="'.$link .'"> Next <i class="fa fa-angle-right"></i></a></li>', $currentPage + 1);
		}
		else
			$output .= sprintf('<li class="next uk-disabled"><a href="#"> Next <i class="fa fa-angle-right"></i></i></a></li>', $currentPage + 1);
		
/*		if ($loopEnd != $totalPages){
			$output .= sprintf('<a href="' . $link . '"><img src="img/icons/go_last.png" width="16" height="16" /></a>', $totalPages);
		} */

		return $output;
	}
}

?>