<?php
/*
// +----------------------------------------------------------------------+
// | Licenses and copyright acknowledgements are located at               |
// | http://www.sonsofskadi.net/wp-content/elalicenses.txt                |
// +----------------------------------------------------------------------+
*/

class Better_ELA_Cache_Builder {

    /**
     * Cache
     * @var BelaCache
     * @access private
     */
    private $cache;

    /**
     * Exclude Posts IDs
     * @var array
     * @access private
     * @since 0.8
     */
    var $excluded_posts = array();

    /**
     * How Many Posts In Each Year
     * @var array
     * @access private
     * @since 0.8
     */
    var $year_table = array();

    /**
     * How Many Posts In Each Month
     * @var array
     * @access private
     * @since 0.8
     */
    var $month_table = array();

	var $catsTable = array();
	var $postsInCatsTable = array();
	var $postToGenerate = array();
	var $tagsTable = array();
	var $postsInTagsTable = array();
    
	/**
     * Constructor
     */
    function Better_ELA_Cache_Builder() {
        $this->cache = bela_get_cache();
    }

    /**
     * Find out posts you don't want them to be shown in the archives
     * @param string $exclude
     */
    function find_exclude_posts($args) {
        global $wpdb;
        
        $show_page = $args['show_page'] == 1;
        $page_ids = array();
        if (!$show_page){
            $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish'";
            BelaLogger::log('SQL Query: ',$sql);
            $page_ids = $wpdb->get_col($sql);
        }
        $this->excluded_posts = $page_ids;

        $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type<>'post' AND post_type<>'page' ";
        $ids = $wpdb->get_col($sql);
        $this->excluded_posts = array_merge($this->excluded_posts, $ids);

        $exclude = trim($args['excluded_categories'], ', ');
        if (empty($exclude)) return;
        $exclude_ids = preg_split('/[\s,]+/',$exclude);
        $exclusion = '(' . implode(',', $exclude_ids) . ')';
        $sql = 'SELECT DISTINCT p.ID '
              ."FROM {$wpdb->posts} p "
              ."INNER JOIN {$wpdb->term_relationships} tr ON ( p.ID=tr.object_id ) "
              ."INNER JOIN {$wpdb->term_taxonomy} tt ON ( tr.term_taxonomy_id=tt.term_taxonomy_id ) "
              .'WHERE tt.taxonomy=\'category\' '
              ."AND tt.term_id IN {$exclusion}";
        BelaLogger::log('SQL Query: ' , $sql);
        $results = $wpdb->get_results($sql);
        BelaLogger::log('Posts In Excluded Categories: ' , $results);
        $exclude_ids = array();
        foreach ($results as $post){
            $exclude_ids[] = $post->ID;
        }
        $this->excluded_posts = array_merge($this->excluded_posts, $exclude_ids);
        $this->excluded_posts = array_unique($this->excluded_posts);
        BelaLogger::log('Posts to Exclude: ',$this->excluded_posts);
    }
	/* ***********************************
	 * Helper Function : Find info about 
	 * 		updated post.
	 * ***********************************/	
	function buildPostToGenerateTable($exclude, $id, $commentId = false) {
        global $wpdb;

        if (!empty($this->excluded_posts)) {
            $exclusions = ' AND ID NOT IN(' . implode(',', $this->excluded_posts) . ') ';
        }

		if(!$commentId) {
			if($id) { 
				$dojustid = ' AND ID = ' . intval($id) . ' ' ;
                $dojustid2 = ' AND tr.object_id = ' . intval($id) . ' ' ;
			}


			$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, tt.term_id AS `category_id`
				FROM $wpdb->posts 
				INNER JOIN {$wpdb->term_relationships} AS tr
                              ON (ID = tr.object_id)
                INNER JOIN {$wpdb->term_taxonomy} AS tt
                              ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                WHERE tt.taxonomy = 'category'
				AND post_date > 0
				$dojustid $exclusions
                GROUP BY tr.object_id
				ORDER By post_date DESC";
			$results = $wpdb->get_results($query);
            BelaLogger::log('SQL Query:' . "Result Count:" . count($results) .$query);
			if ($results) {
				foreach($results as $result) {
					$this->postToGenerate['category_id'][] = $result->category_id;
				}
                $this->postToGenerate['new_year']= $results[0]->year;
                $this->postToGenerate['new_month']= $results[0]->month;
            }else{
                return true;
            }
			

            $query = "SELECT t.term_id AS `tag_id`
                      FROM $wpdb->terms AS t
                      INNER JOIN $wpdb->term_taxonomy AS tt
                            ON (t.term_id = tt.term_id)
                      INNER JOIN $wpdb->term_relationships AS tr
                            ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                      WHERE tt.taxonomy = 'post_tag'
                      $dojustid2
                    ";
            $results = $wpdb->get_results($query);
            BelaLogger::log('SQL Query:' . "Result Count:" . count($results).$query);
            if ($results) {
                foreach($results as $result) {
                    $this->postToGenerate['tag_id'][] = $result->tag_id;
                }
            }
			
			return true;
		} else {
			$query = "SELECT comment_post_ID  
				FROM $wpdb->comments
				WHERE comment_ID = $id AND comment_approved = '1'";
			
			$result = $wpdb->get_var($query);
            BelaLogger::log('SQL Query:' . "Result Count:" . count($result).$query);
			if ($result) {
				$id = $result;
				if($id) {
					$dojustid = ' AND ID = ' . intval($id) . ' ' ;
				}

				$query = "SELECT YEAR(post_date) AS `year`, 
                                 MONTH(post_date) AS `month`,
                                 tt.term_id AS `category_id`
                          FROM $wpdb->posts
                          INNER JOIN {$wpdb->term_relationships} AS tr
                                     ON (ID = tr.object_id)
                          INNER JOIN {$wpdb->term_taxonomy} AS tt
                                     ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                          WHERE tt.taxonomy = 'category'
                          AND post_date > 0
                          $dojustid $exclusions
                          GROUP BY tr.object_id
                          ORDER By post_date DESC";
				
				$results = $wpdb->get_results($query);
                BelaLogger::log('SQL Query:' . "Result Count:" . count($results).$query ."\n". var_export($this->postToGenerate,true));
				if($results) {
					foreach($results as $result) {
						$this->postToGenerate['category_id'][]=$result->category_id;
					}
					$this->postToGenerate['post_id'] = $id;
					$this->postToGenerate['new_year']= $results[0]->year;
					$this->postToGenerate['new_month'] = $results[0]->month;
					$this->year_table = array($this->postToGenerate['new_year'] => 0);
					$this->month_table[$this->postToGenerate['new_year']] = array($this->postToGenerate['new_month'] => 0);
					$this->catsTable = $this->postToGenerate['category_id'];
					return true;
				}
			}
			return false;
		}
	}
	/* ***********************************
	 * Helper Function : build Years.
	 * ***********************************/	
    function build_years_table($id = false) {
        global $debug, $wpdb;

        if (!empty($this->excluded_posts)) {
            $exclusions = ' AND `ID` NOT IN(' . implode(',', $this->excluded_posts) . ') ';
        }

		$sql = 'SELECT YEAR(`post_date`) `year`, COUNT(`ID`) `count` '
			    ."FROM {$wpdb->posts} "
                .'WHERE `post_status` = \'publish\' '
                .$exclusions
                .'GROUP BY `year` ORDER By `post_date` DESC';		
		$year_results = $wpdb->get_results($sql);
        BelaLogger::log('SQL Query:'.$sql, 'Results Count:'.count($year_results));

        if ($year_results) {
            foreach ($year_results as $year_result) {
                if ($year_result->count > 0)
                    $this->year_table[$year_result->year] = $year_result->count;
            }
        }
		if (!empty($this->year_table)) {
            if (false !== $id){ //如果更新单篇文章
                $yearsTable = $this->cache->get('years.dat');
                $diffyears = array_diff_assoc($this->year_table, $yearsTable);                
                if (!empty($diffyears)){ //如果Year表发生变化，重写Year表cache
                    $this->cache->set('years.dat', $this->year_table);
                    BelaLogger::log('Years Table Updated:',$this->year_table);
                    $this->year_table = $diffyears;
                    BelaLogger::log('Different Years:',$diffyears);
                } else {
                    $this->year_table = array($this->postToGenerate['new_year'] => 0);
                }
            } else {
                $this->cache->set('years.dat', $this->year_table);
                BelaLogger::log('Years Table:',$this->year_table);
            }		
		}
	}
	/* ***********************************
	 * Helper Function : build Months.
	 * ***********************************/
	function build_months_table($id = false) {
        global $wpdb;

        if (!empty($this->excluded_posts)) {
            $exclusions = ' AND `ID` NOT IN(' . implode(',', $this->excluded_posts) . ') ';
        }

        foreach ($this->year_table as $year => $y) {
            $sql = 'SELECT MONTH(`post_date`) `month`, COUNT(`ID`) `count` '
				  ."FROM {$wpdb->posts} "
                  ."WHERE YEAR(`post_date`)={$year} "
				  .$exclusions
                  .'AND `post_status`=\'publish\' '
                  .'GROUP BY `month` ORDER By `post_date` DESC';
			$month_results = $wpdb->get_results($sql);
            BelaLogger::log('SQL Query: '.$sql,'Results Count: '.count($month_results));

            if (!empty($month_results)) {
                foreach ($month_results as $month_result) {
                    if ($month_result->count > 0){
                        $this->month_table[$year][$month_result->month] = $month_result->count;
                    }
                }
                if (!empty($this->month_table[$year])) {
                    if ($id !== false){
                        $monthTable = $this->cache->get($year . '.dat');
                        $diffmonth = array_diff_assoc($this->month_table[$year], $monthTable);
                        if (!empty($diffmonth)){                            
                            $this->cache->set($year . '.dat', $this->month_table[$year]);
                            BelaLogger::log('Year: ',$year,' Month Table Updated: ',$this->month_table[$year]);
                            $this->month_table[$year] = $diffmonth;
                            BelaLogger::log('Different Months: ',$this->month_table[$year]);
                        }else{
                            $this->month_table[$year] = array($this->postToGenerate['new_month'] => 0);
                        }
                    } else {
                        $this->cache->set($year . '.dat', $this->month_table[$year]);
                        BelaLogger::log('Year: ',$year,' Month Table: ',$this->month_table[$year]);
                    }                   
                }
            }
        }
    }
	/* ***********************************
	 * Helper Function : build Posts in 
	 * 			Month.
	 * ***********************************/
	function build_posts_in_months_table() {
        global $wpdb;

        if (!empty($this->excluded_posts)) {
            $exclusions = ' AND ID NOT IN(' . implode(',', $this->excluded_posts) . ') ';
        }

        $posts = array();
		$now = current_time('mysql', 1);
        if (empty($this->year_table)) { //TODO: 这里很奇怪，按理说，这里永远不会出现空的情况
            return;
        }
        
		foreach( $this->year_table as $year => $y ) {
			$posts[$year] = array();
            BelaLogger::log('Now Processing Year: ',$year);
			foreach( $this->month_table[$year] as $month =>$m ) {
				$posts[$year][$month] = array();
                BelaLogger::log('Now Processing Month: ',$month);
                $sql = 'SELECT `ID`,`post_title`,DAYOFMONTH(`post_date`) `day`,`comment_status`,`comment_count` '
                      ."FROM {$wpdb->posts} WHERE YEAR(`post_date`)={$year} "
					  ."AND MONTH(`post_date`)={$month} "
                      .'AND post_status=\'publish\' '
                      .$exclusions
					  .'ORDER By `post_date` DESC';
				$post_results = $wpdb->get_results($sql);
                BelaLogger::log('SQL Query: ',$sql,"\nResult Count: ",count($post_results));
				if( $post_results ) {
					foreach( $post_results as $post_result ) {
							$posts[$year][$month][$post_result->ID] = array(
                                $post_result->day,
                                $post_result->post_title,
                                get_permalink($post_result->ID),
                                $post_result->comment_count,
                                $post_result->comment_status
                            );
					}
				}
				if (!empty($posts[$year][$month])) {
                    $this->cache->set($year . '-' . $month . '.dat', $posts[$year][$month]);
                    BelaLogger::log($year . '-' . $month . '.dat Updated. Content is: ',$posts[$year][$month]);
				}
			}
		}
	}
	/* ***********************************
	 * Helper Function : build Categories.
	 * ***********************************/	
	function buildCatsTable($exclude='', $id = false) {
		$this->buildCatsList('ID', 'asc', FALSE, TRUE, '0', 0, $exclude, TRUE);
        BelaLogger::log("Category Table: ",$this->catsTable);
		foreach( $this->catsTable as $category ) {
			$parentcount = 0;
			if(($parentkey = $category[4])) {
				$parentcount++;
				while($parentkey) {
					$parentcount++;
					$this->catsTable[$parentkey][6] = TRUE;
					$parentkey=$this->catsTable[$parentkey][4];
				}
			}
			$this->catsTable[$category[0]][5] = $parentcount;
		}
		foreach( $this->catsTable as $category ) {
			if ( (isset($category[6]) && $category[6] == TRUE) || intval($category[3]) > 0) {
				$this->catsTable[$category[0]][6] = TRUE;
			} else {
				$this->catsTable[$category[0]][6] = FALSE;
			}
		}
		if($id) {
            $categoryTable = $this->cache->get('categories.dat');
			if ($categoryTable) {
				$diffTempo = array_diff_assoc($categoryTable, $this->catsTable);
				if(!empty($diffTempo)) $diffcats = $diffTempo;
			}
		}
        $this->cache->set('categories.dat', $this->catsTable);
		if($id) {			
			if (!empty($diffcats)) {
				$this->catsTable = $diffcats;
			} else {
				$this->catsTable = $this->postToGenerate['category_id'];
			}
		}
	}
	/* ***********************************
	 * Helper Function : build list of cats
	 * ***********************************/	
	function buildCatsList($sort_column = 'ID', $sort_order = 'asc', $hide_empty = FALSE, $children=TRUE, $child_of=0, $categories=0, $exclude = '', $hierarchical=TRUE, $id = false) {
		global $wpdb, $category_posts;
		
        $exclusions = '';
		if (!empty($exclude)) {
			$excats = preg_split('/[\s,]+/',$exclude);
			if (count($excats)) {
				foreach ($excats as $excat) {
					$exclusions .= ' AND t.term_id <> ' . intval($excat) . ' ';
				}
			}
		}

		if (intval($categories)==0){
			$sort_column = 'cat_'.$sort_column;
            if ($sort_column == 'ID'){
                $sort_column = 't.term_id';
            }else{
                $sort_column = 't.name';
            }
            $query = "SELECT t.term_id AS `cat_ID`, t.name AS `cat_name`, t.slug AS `category_nicename`, tt.parent AS `category_parent`
                      FROM $wpdb->terms AS t
                      INNER JOIN {$wpdb->term_taxonomy} AS tt
                            ON (t.term_id = tt.term_id)
                      WHERE tt.taxonomy = 'category'
                      AND t.term_id > 0
                      $exclusions
                      ORDER BY $sort_column $sort_order";
			
			$categories = $wpdb->get_results($query);
            BelaLogger::log("SQL Query : Categories: ".count($categories) . $query);
		}

		if (!count($category_posts)) {
			$now = current_time('mysql', 1);	

            $query = "SELECT `term_id` AS `cat_ID`, `count` AS `cat_count`
                      FROM {$wpdb->term_taxonomy} AS t
                      WHERE `taxonomy` = 'category'
                      $exclusions";

			
			$cat_counts = $wpdb->get_results($query);
            BelaLogger::log("SQL Query : Categories Counts: " . count($cat_counts) .$query);
	        if (! empty($cat_counts)) {
	            foreach ($cat_counts as $cat_count) {
	                if (1 != intval($hide_empty) || $cat_count > 0) {
	                    $category_posts[$cat_count->cat_ID] = $cat_count->cat_count;
	                }
	            }
	        }
		}
		foreach ($categories as $category) {
			if ((intval($hide_empty) == 0 || isset($category_posts[$category->cat_ID])) && (!$hierarchical || $category->category_parent == $child_of) ) {
				$this->catsTable[$category->cat_ID] = array(	$category->cat_ID, 
	 															$category->cat_name,
	 															$category->category_nicename, 
																$category_posts["$category->cat_ID"], 
	 															$category->category_parent);
				if ($hierarchical && $children) {
					$this->buildCatsList(	$sort_column,
										$sort_order, 
										$hide_empty, 
										$children, 
										$category->cat_ID, 
										$categories, 
										$exclude, 
										$hierarchical);
				}
			}
		}
	}
	/* ***********************************
	 * Helper Function : build Posts In 
	 * 			Categories
	 * ***********************************/	
	function buildPostsInCatsTable() {
		global $wpdb, $category_posts;

		if (!empty($this->excluded_posts)) {
            $exclusions = ' AND p.ID NOT IN(' . implode(',', $this->excluded_posts) . ') ';
		}
		$now = current_time('mysql', 1);
		BelaLogger::log($this->catsTable);
        //TODO 这里foreach也可能遍历空对象，调查原因
        if (empty($this->catsTable)) return;
		foreach( $this->catsTable as $category ) {
			$posts_in_cat[$category[0]] = array();
            $query = "SELECT p.ID AS `post_id`
                      FROM $wpdb->posts AS p
                        INNER JOIN {$wpdb->term_relationships} AS tr
                                   ON (p.ID = tr.object_id)
                        INNER JOIN {$wpdb->term_taxonomy} AS tt
                                   ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                        WHERE tt.taxonomy = 'category'
                        AND tt.term_id = $category[0]
                        $exclusions
            ";
			
			$posts_in_cat_results = $wpdb->get_results($query);
            BelaLogger::log("SQL Query :Posts in Cat:" . count($posts_in_cat_results) .$query);
			if( $posts_in_cat_results ) {
				$posts_in_cat_results = array_reverse($posts_in_cat_results);
				$post_id_set = array();
                foreach( $posts_in_cat_results as $post_in_cat_result ) {
					$post_id_set[] = $post_in_cat_result->post_id;
				}
                $post_id_set = '(' . implode(',', $post_id_set) . ')';

                $query = "SELECT ID, post_title, post_date as `day`, comment_status, comment_count
                    FROM $wpdb->posts
                    WHERE ID IN $post_id_set
                    AND post_status = 'publish'
                    AND post_date_gmt <= '$now'
                    ORDER By post_date";

                $post_results = $wpdb->get_results($query);
                BelaLogger::log("SQL Query :Post Results". count($post_results) .$query);
                if( $post_results ) {
                    foreach( $post_results as $post_result ) {
                        $this->postsInCatsTable[$category[0]][$post_result->ID] = array($post_result->day, $post_result->post_title, get_permalink($post_result->ID), $post_result->comment_count, $post_result->comment_status);
                    }
                }

				if (isset($this->postsInCatsTable[$category[0]])) {
                    $this->cache->set('cat-' . $category[0] . '.dat', $this->postsInCatsTable[$category[0]]);
				}
			}
		}
	}
	/* ***********************************
	 * Helper Function : build Tags.
	 * ***********************************/	
    function build_tags_table($id = false, $order = false, $orderparam = 0) {
		
        global $wpdb;
					
        switch ($order) {
            case 2: // X is the min number of post per tag
                $ordering = "HAVING tt.count >= " . $orderparam . " ORDER BY tt.count DESC";
                break;
            case 1: // X is the number of tag to show
                $ordering = "ORDER BY tt.count DESC LIMIT " . $orderparam;
                break;
            case 0:
            default:
                $ordering = "";
                break;
        }

        $query = "SELECT t.term_id AS `tag_id`, t.name AS `tag`, tt.count AS tag_count
                      FROM $wpdb->terms AS t
                      INNER JOIN $wpdb->term_taxonomy AS tt
                            ON (t.term_id = tt.term_id)
                      WHERE tt.taxonomy = 'post_tag'
                      $ordering
                      ";

			$tagsSet = $wpdb->get_results($query);			
            BelaLogger::log('SQL Query: ',$query,'Result Count: ',count($tagsSet));
			$tagged_posts = 0;
			$posted_tags = 0;
			if( !empty($tagsSet) ) {
				foreach($tagsSet as $tag) {
					if ($tag->tag_count) {
						$this->tagsTable[$tag->tag_id] = array($tag->tag_id, $tag->tag, $tag->tag_count );
						$tagged_posts++;
						if (intval($posted_tags) < intval($tag->tag_count)) $posted_tags = $tag->tag_count;
					}
				}
				if ($order!= false ) {
					$this->tagsTable = $this->arraySort($this->tagsTable, 1);
				}
				
				$this->tagsTable[0] = array($tagged_posts, $posted_tags);
				
                $this->cache->set('tags.dat', $this->tagsTable);
				
				if($id) {
                    $tagTable = $this->cache->get('tags.dat');
					$difftags = array_diff_assoc($tagTable, $this->tagsTable);
					if (!empty($difftags)) {
						$this->tagsTable = $difftags;
					} else {
						$this->tagsTable = $this->postToGenerate['tag_id'];
					}
				}
			}
		
		if (empty($this->tagsTable)) return false;
		return true;
	}
	/* ***********************************
	 * Helper Function : build Posts In 
	 * 			Tags
	 * ***********************************/	
	function buildPostsInTagsTable() {
		
			global $wpdb;

            if (!empty($this->excluded_posts)) {
                $exclusions = ' AND ID NOT IN(' . implode(',', $this->excluded_posts) . ') ';
            }
			
			foreach( $this->tagsTable as $key => $tag) {
                if($key==0) continue;
                $query = "SELECT ID, post_title, post_date AS `day`, comment_status, comment_count
                          FROM $wpdb->posts
                          INNER JOIN $wpdb->term_relationships AS tr
                                ON (ID = tr.object_id)
                          INNER JOIN $wpdb->term_taxonomy AS tt
                                ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                          WHERE tt.term_id = $tag[0]
                          AND tt.taxonomy = 'post_tag'
                          AND post_status = 'publish'
                          $exclusions
                          ORDER BY post_date";

				$posts_in_tag_results = $wpdb->get_results($query);
                BelaLogger::log('SQL Query: ',$query,'Result Count: ',count($posts_in_tag_results));
				if( $posts_in_tag_results ) {

					foreach( $posts_in_tag_results as $post_result ) {
						$this->postsInTagsTable[$tag[0]][$post_result->ID] = array($post_result->day, $post_result->post_title, get_permalink($post_result->ID), $post_result->comment_count, $post_result->comment_status);
					}
					if ($this->postsInTagsTable[$tag[0]]) {
                        $this->cache->set('tag-' . $tag[0] . '.dat', $this->postsInTagsTable[$tag[0]]);
					}
				}else{
                    unset($this->tagsTable[$key]);
                    $this->tagsTable[0][0] = $this->tagsTable[0][0] - 1;
                }
			}
            $this->cache->set('tags.dat', $this->tagsTable);
		
	}
	/******************************************
	 * Helper Function : sort a mulitdim array
	 ******************************************/		
	function arraySort($array, $key) {
		foreach ($array as $i => $k) {
			$sort_values[$i] = $array[$i][$key];
		}
		asort($sort_values);
		reset($sort_values);
		$i=1;
		while (list ($arr_key, $arr_val) = each ($sort_values)) {
			$sorted_arr[$i++] = $array[$arr_key];
		}
		return $sorted_arr;
	}
}
