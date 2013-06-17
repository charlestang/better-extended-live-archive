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
    }

    /**
     * Update the chronological index.
     * @param int $postId
     * @param WP_Post $post
     */
    public function update($postId, $post = null) {
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

        if (!empty($results)) {
            $yearTable = array_map(create_function('$entry', 'return $entry->count;'), $results);
            $this->getCache()->set('years.dat', $yearTable);
            return array_keys($yearTable);
        }
        return array();
    }

    public function getYearsTable() {
        return $this->getCache()->get('years.dat');
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
        return $this->getCache()->get($year . '.dat');
    }

    public function buildPostsInMonthTable($year, $month) {
        $excludedPostIds = $this->getOptions()->get(BelaKey::EXCLUDED_POST_IDS);

        $exclusions = "";
        if (!empty($excludedPostIds)) {
            $exclusions = "ID NOT IN (" . implode(',', $excludedPostIds) . ") ";
        }
        $sql = "SELECT ID, DAYOFMONTH(post_date) day, post_title, comment_count, comment_status "
                . "FROM {$this->getDb()->posts} WHERE YEAR(post_date)={$year} "
                . "AND MONTH(post_date)={$month} "
                . "AND post_status='publish' "
                . $exclusions
                . "ORDER By post_date DESC";
        $results = $this->getDb()->get_results($sql, OBJECT_K);
        BelaLogger::log($sql, $results);

        if (!empty($results)) {
            $postsInMonth = array_map(array($this, 'generateEntryInPostsTable'), $results);
            $this->getCache()->set($year . '-' . $month . '.dat', $postsInMonth);
        }
    }

    public function getPostsInMonthTable($year, $month) {
        return $this->getCache()->get($year . '-' . $month . '.dat');
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
