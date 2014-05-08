<?php

/**
 * Maintain the chronological indices of posts and pages.
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaTimeIndex extends BelaIndex {

    /**
     * Build the chronological indices.
     * 
     * 1. build the years stats, that means how many posts in each year.
     *    this step will generate the years.bat cache.
     * 2. build the month stats, that means how many posts in each month.
     *    this step will generate the <year>.dat cache.
     * 3. build the month table, that means to build the post list in month.
     *    this step will generate the <year>-<month>.dat cache.
     */
    public function build() {
        $years = $this->buildYearsTable();
        foreach ($years as $year) {
            $months = $this->buildMonthsInYearTable($year);
            foreach ($months as $month) {
                $this->buildPostsInMonthTable($year, $month);
            }
        }
        return $years;
    }

    public function beforeUpdate($postId, $postAfter, $postBefore) {
        
    }

    /**
     * Update the chronological index.
     * @param int $postId
     * @param WP_Post $post
     */
    public function afterUpdate($postId, $post = null) {
        BelaLogger::log($postId, $post);
        if (is_null($post)) {
            $post = get_post($postId); // when the post is deleted, the post can
            // still be fetched because of the cache
        }
        if ($post->post_type == 'revision') {
            return;
        }
        $time = strtotime($post->post_date);
        $year = date('Y', $time);
        $month = intval(date('m', $time));
        $this->buildYearsTable();
        $this->buildMonthsInYearTable($year);
        $this->buildPostsInMonthTable($year, $month);
    }

    /**
     * This table should be rebuild everytime a post updated
     * @return array The years set
     */
    public function buildYearsTable() {
        $excludedPostIds = $this->getOptions()->get(BelaKey::EXCLUDED_POST_IDS);

        $exclusions = "";
        if (!empty($excludedPostIds)) {
            $exclusions = "ID NOT IN (" . implode(',', $excludedPostIds) . ") ";
        }
        $sql = "SELECT YEAR(post_date) as year, COUNT(ID) as `count` "
                . "FROM {$this->getDb()->posts} "
                . "WHERE post_status='publish' "
                . $exclusions
                . 'GROUP BY year ORDER By post_date DESC';
        $results = $this->getDb()->get_results($sql, OBJECT_K);
        BelaLogger::log($sql, $results);

        $yearTable = array();
        if (!empty($results)) {
            $yearTable = array_map(create_function('$entry', 'return $entry->count;'), $results);
        }
        $this->getCache()->set('years.dat', $yearTable);
        return array_keys($yearTable);
    }

    public function getYearsTable() {
        $yearTable = $this->getCache()->get('years.dat');
        if (false === $yearTable) {
            $yearTable = $this->build();
        }
        return $yearTable;
    }

    /**
     * Read the year list from the year table.
     * @return array the array of year.
     */
    public function getYearsList() {
        $yearTable = $this->getYearsTable();
        if ($yearTable && !empty($yearTable)) {
            return array_keys($yearTable);
        }
        return array();
    }

    /**
     * This table should be rebuild, everytime a post is updated,
     * just the related year should be rebuild
     * @return array The month set in the year
     */
    public function buildMonthsInYearTable($year) {
        $excludedPostIds = $this->getOptions()->get(BelaKey::EXCLUDED_POST_IDS);

        $exclusions = "";
        if (!empty($excludedPostIds)) {
            $exclusions = "ID NOT IN (" . implode(',', $excludedPostIds) . ") ";
        }
        $sql = "SELECT MONTH(post_date) month, COUNT(ID) count "
                . "FROM {$this->getDb()->posts} "
                . "WHERE YEAR(post_date)={$year} "
                . $exclusions
                . "AND post_status='publish' "
                . "AND (post_type='post' OR post_type='page')"
                . "GROUP BY month ORDER By post_date DESC";
        $results = $this->getDb()->get_results($sql, OBJECT_K);
        BelaLogger::log($sql, $results);

        if (!empty($results)) {
            $monthTable = array_map(create_function('$entry', 'return $entry->count;'), $results);
            $this->getCache()->set($year . '.dat', $monthTable);
            return array_keys($monthTable);
        }

        return array();
    }

    public function getMonthsInYearTable($year) {
        $monthInYearTable = $this->getCache()->get($year . '.dat');
        return (false === $monthInYearTable) ? array() : $monthInYearTable;
    }

    public function getMonthsInYearList($year) {
        $monthInYearTable = $this->getMonthsInYearTable($year);
        if ($monthInYearTable && !empty($monthInYearTable)) {
            return array_keys($monthInYearTable);
        }
        return array();
    }

    public function buildPostsInMonthTable($year, $month) {
        $excludedPostIds = $this->getOptions()->get(BelaKey::EXCLUDED_POST_IDS);

        $latestFirst = $this->getOptions()->get(BelaKey::SHOW_LATEST_FIRST);
        if ($latestFirst) {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        $exclusions = "";
        if (!empty($excludedPostIds)) {
            $exclusions = "ID NOT IN (" . implode(',', $excludedPostIds) . ") ";
        }
        $sql = "SELECT ID, DAYOFMONTH(post_date) day, post_title, comment_count, comment_status "
                . "FROM {$this->getDb()->posts} WHERE YEAR(post_date)={$year} "
                . "AND MONTH(post_date)={$month} "
                . "AND post_status='publish' "
                . "AND (post_type='post' OR post_type='page')"
                . $exclusions
                . "ORDER By post_date {$order}";
        $results = $this->getDb()->get_results($sql, OBJECT_K);
        BelaLogger::log($sql, $results);

        if (!empty($results)) {
            $postsInMonth = array_map(array($this, 'generateEntryInPostsTable'), $results);
            $this->getCache()->set($year . '-' . $month . '.dat', $postsInMonth);
        }
    }

    public function getPostsInMonthTable($year, $month) {
        $postInMonthTable = $this->getCache()->get($year . '-' . $month . '.dat');
        return (false === $postInMonthTable) ? array() : $postInMonthTable;
    }

    public function generateEntryInPostsTable($post) {
        return array(
            $post->day,
            $post->post_title,
            get_permalink($post->ID),
            $post->comment_count,
            $post->comment_status,
        );
    }

    public function initialized() {
        return $this->getCache()->exists('years.dat');
    }

}

