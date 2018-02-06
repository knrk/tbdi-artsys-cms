<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_article_category
 *	@property string	$title
 *	@property string	$perex
 *	@property string	$content
 *	@property string	$name
 *	@property string	$url_name
 *	@property string	$meta_title
 *	@property string	$meta_keywords
 *	@property string	$meta_description
 *	@property int		$sort
 *	@property int		$active
 *	@property int		$rights
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Article_Category	getCategory()
 *	@method \Article			setCategory( Article_Category $category )
 */
class Article extends Art_Abstract_Model_DB {
	
    protected static $_table = 'article';
    
	protected static $_link = array('category' => 'Article_Category');
	
	protected static $_foreign = array('id_article_category');
	
    protected static $_cols = array('id'					=>	array('select','insert'),
									'id_article_category'	=>	array('select','insert','update'),
                                    'title'					=>	array('select','insert','update'),
                                    'perex'					=>	array('select','insert','update'),
                                    'content'				=>	array('select','insert','update'),
									'name'					=>	array('select','insert','update'),
                                    'url_name'				=>	array('select','insert','update'),
                                    'meta_title'			=>	array('select','insert','update'),
                                    'meta_keywords'			=>	array('select','insert','update'),
                                    'meta_description'		=>	array('select','insert','update'),
                                    'sort'					=>	array('select','insert','update'),
                                    'active'				=>	array('select','insert','update'),
                                    'rights'				=>	array('select','insert','update'),
									'created_by'			=>	array('select','insert'),
									'modified_by'			=>	array('select','update'),
									'created_date'			=>	array('select'),
									'modified_date'			=>	array('select'));
	
	function save()
	{
		if(empty($this->name))
		{
			$this->name = rand_str();
		}
		
		return parent::save();
	}
	
	
	/**
	 *	Get usable META tags from article
	 * 
	 *	@return array
	 */
	function getMeta()
	{
		$output = array();
		
		if( !empty($this->meta_title) )
		{
			$output['title'] = $this->meta_title;
		}	
		else
		{
			$output['title'] = $this->title;
		}
		
		if( !empty($this->meta_keywords) )
		{
			$output['keywords'] = $this->meta_keywords;
		}	
		
		if( !empty($this->meta_description) )
		{
			$output['description'] = $this->meta_description;
		}	
		elseif( !empty($this->perex) )
		{
			$output['description'] = $this->perex;
		}
		else
		{
			$output['description'] = $this->title;
		}
		
		return $output;
	}
	
	
	/**
	 *	Get next sort value for category id
	 * 
	 *	@static
	 *	@param int $category_id
	 *	@return int
	 */
	static function getNextSort( $category_id )
	{
		$select_stmt = new Art_Model_Db_Select(self::$_table, array('function' => 'MAX(`sort`)'));
		$where_stmt = new Art_Model_Db_Where(array('name' => 'id_article_category', 'value' => $category_id));
		
		$query = Art_Main::db()->select($select_stmt, $where_stmt);
		$query->execute($where_stmt->getValues());
		$id = $query->fetchColumn();
		
		if( NULL !== $id )
		{
			++$id;
		}
		else
		{
			$id = 1;
		}
		
		return $id;
	}

	
	/**
	 *	Get article upper from this one (Empty instance if not found)
	 * 
	 *	@return Article
	 */
	function getUpper()
	{
		return $this->_getByDirection('up');
	}
	
	
	/**
	 *	Get article below this one (Empty instance if not found)
	 * 
	 *	@return Article
	 */
	function getDowner()
	{
		return $this->_getByDirection('down');
	}
	
	
	/**
	 *	Swap positions of 2 articles
	 * 
	 *	@static
	 *	@param Article $a1
	 *	@param Article $a2
	 *	@return void
	 */
	static function swapPositions( Article $a1, Article $a2 )
	{
		$buff = $a1->sort;
		
		$a1->sort = $a2->sort;
		$a2->sort = $buff;
		
		$a1->save();
		$a2->save();
	}
	
	
	/**
	 * Get closest Article to this one (by direction)
	 * 
	 *	@access protected
	 *	@param string $dir
	 *	@return \Article
	 */
	protected function _getByDirection( $dir )
	{
		switch( strtolower($dir) )
		{
			case 'up':
			{
				$rel = '<';
				$type = 'DESC';
				break;
			}
			case 'down':
			{
				$rel = '>';
				$type = 'ASC';
				break;
			}
			default: 
			{
				trigger_error('Invalid argument supplied to Article->_getByDirection()',E_USER_ERROR);
			}
		}
		
		$where_stmt = new Art_Model_Db_Where(array( array('name' => 'id_article_category', 'value' => $this->id_article_category),
													array('name' => 'sort', 'value' => $this->sort, 'relation' => $rel, 'operation' => 'AND' )));
		$order_stmt = new Art_Model_Db_Order(array( 'name' => 'sort', 'type' => $type ));
		$limit_stmt = new Art_Model_Db_Limit(1);

		$article = Article::fetchAllPrivileged($where_stmt, $order_stmt, $limit_stmt);
		if( count($article) )
		{
			return $article[0];
		}
		else
		{
			return new Article();
		}
	}
}
