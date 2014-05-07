<?php

/**
 * Build the category index
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaCategoryIndex extends BelaIndex {

    /**
     * Build the category index and output the category list with stats.
     * The return array elements are the category entry, and the keys are 
     * the category id. e.g.
     * array(
     *     3 => array(      // the key is the category ID
     *          3,          // the 1st element is the category ID too
     *          'Firefox',  // the 2nd element is the category name
     *          'firefox',  // the 3rd element is the category slug
     *          0,          // the 4th element is the parent category ID
     *          4,          // the 5th element is the number of posts in this category
     *      ),
     * );
     * @return array 
     */
    public function buildCategoriesTable() {
        $excludedCategoryIds = $this->getOptions()->get(BelaKey::EXCLUDE_CATEGORY_LIST);
        $exclusion = '';
        if (!empty($excludedCategoryIds)) {
            $exclusion = "AND tt.term_taxonomy_id NOT IN (" . implode(',', $excludedCategoryIds) . ") ";
        }

        $sql = "SELECT tt.term_taxonomy_id ID, t.name, t.slug, tt.parent, 0 as count "
                . "FROM {$this->getDb()->terms} t "
                . "INNER JOIN {$this->getDb()->term_taxonomy}  tt ON (t.term_id=tt.term_id) "
                . "WHERE tt.taxonomy = 'category' AND t.term_id > 0 "
                . $exclusion
                . "ORDER BY ID ASC";
        $results = $this->getDb()->get_results($sql, OBJECT_K);
        BelaLogger::log($sql, $results);

        $catIds = array_keys($results);

        $excludedPostIds = $this->getOptions()->get(BelaKey::EXCLUDED_POST_IDS);
        $exclusions = "";
        if (!empty($excludedPostIds)) {
            $exclusions = "AND p.ID NOT IN (" . implode(',', $excludedPostIds) . ") ";
        }

        $sql = "SELECT tr.term_taxonomy_id ID, count(p.ID) count "
                . "FROM {$this->getDb()->posts} p "
                . "INNER JOIN {$this->getDb()->term_relationships} tr ON p.ID=tr.object_id "
                . "WHERE p.post_status='publish' "
                . "AND tr.term_taxonomy_id IN (" . implode(',', $catIds) . ") "
                . $exclusions
                . "GROUP BY tr.term_taxonomy_id";
        $catStats = $this->getDb()->get_results($sql, OBJECT_K);
        BelaLogger::log($sql, $catStats);

        $catStatsRecursive = array(); //stats the category post count recursively
        if (!empty($results)) {
            foreach ($results as $id => $cat) {
                if (isset($catStats[$id])) { // if the cat contains posts, update the count
                    $results[$id]->count = $catStats[$id]->count;
                    if ($cat->parent != 0) {
                        $catStatsRecursive[$cat->parent] = $catStats[$id]->count;
                    }
                }
            }
            foreach ($results as $id => $cat) {
                if ($cat->count == 0 && !isset($catStatsRecursive[$id])) {
                    unset($results[$id]);
                }
            }
        }

        $categoryTable = array_map(array($this, 'getCategoryTableEntry'), $results);

        $this->getCache()->set('categories.dat', $categoryTable);

        return $categoryTable;
    }

    public function getCategoriesTable() {
        $categoriesTable = $this->getCache()->get('categories.dat');
        if (false === $categoriesTable) {
            $categoriesTable = $this->build();
        }
        return $categoriesTable;
    }

    public function getCategoriesList() {
        $categoriesTable = $this->getCategoriesTable();
        if ($categoriesTable && !empty($categoriesTable)) {
            return array_keys($categoriesTable);
        }
        return array();
    }

    private function getCategoryTableEntry($cat) {
        return array(
            $cat->ID,
            $cat->name,
            $cat->slug,
            $cat->parent,
            $cat->count,
        );
    }

    public function buildPostsInCategoryTable($categoryId) {
        $excludedPostIds = $this->getOptions()->get(BelaKey::EXCLUDED_POST_IDS);
        $exclusions = "";
        if (!empty($excludedPostIds)) {
            $exclusions = "AND p.ID NOT IN (" . implode(',', $excludedPostIds) . ") ";
        }

        $latestFirst = $this->getOptions()->get(BelaKey::SHOW_LATEST_FIRST);
        if ($latestFirst) {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        $sql = "SELECT p.ID, p.post_title, p.post_date "
                . "FROM {$this->getDb()->posts} p "
                . "INNER JOIN {$this->getDb()->term_relationships} tr ON p.ID=tr.object_id "
                . "WHERE tr.term_taxonomy_id={$categoryId} "
                . "AND p.post_status='publish' "
                . $exclusions
                . "ORDER BY p.post_date {$order}";
        $results = $this->getDb()->get_results($sql, OBJECT_K);
        BelaLogger::log($sql, $results);

        if (!empty($results)) {
            $postTable = array_map(array($this, 'getPostTableEntry'), $results);

            $postIds = array_keys($postTable);
            $sql2 = "SELECT p.ID, COUNT(c.comment_ID) count "
                    . "FROM {$this->getDb()->posts} p "
                    . "INNER JOIN {$this->getDb()->comments} c ON p.ID=c.comment_post_ID "
                    . "WHERE p.ID IN (" . implode(',', $postIds) . ") "
                    . "GROUP BY p.ID";

            $results2 = $this->getDb()->get_results($sql2, OBJECT_K);
            foreach ($postTable as $k => $post) {
                if (isset($results2[$k])) {
                    $postTable[$k][3] = $results2[$k]->count;
                }
            }

            $this->getCache()->set('cat-' . $categoryId . '.dat', $postTable);
        }
    }

    public function getPostsInCategoryTable($catId) {
        return $this->getCache()->get('cat-' . $catId . '.dat');
    }

    private function getPostTableEntry($post) {
        return array(
            $post->post_date,
            $post->post_title,
            get_permalink($post->ID),
            0,
        );
    }

    public function build() {
        $categories = $this->buildCategoriesTable();
        $catIds = array_keys($categories);
        foreach ($catIds as $cId) {
            $this->buildPostsInCategoryTable($cId);
        }

        return $categories;
    }

    public function beforeUpdate($postId, $postAfter, $postBefore) {
        
    }

    public function afterUpdate($postId, $post = null) {
        if ($post == null) {
            $post = get_post($postId);
        }

        if ($post->post_type == 'revision') {
            return;
        }

        $sql = "SELECT tr.term_taxonomy_id "
                . "FROM {$this->getDb()->term_relationships} tr "
                . "INNER JOIN {$this->getDb()->term_taxonomy} tt "
                . "ON tr.term_taxonomy_id=tt.term_taxonomy_id "
                . "WHERE tt.taxonomy='category' "
                . "AND tr.object_id=" . intval($postId);
        $catIds = $this->getDb()->get_col($sql);
        BelaLogger::log($sql, $catIds);

        $oldCategoriesTable = $this->getCategoriesTable();
        $newCategoriesTable = $this->buildCategoriesTable();
        $diff1 = array_diff_assoc($newCategoriesTable, $oldCategoriesTable);
        $diff2 = array_diff_assoc($oldCategoriesTable, $newCategoriesTable);

        if (!empty($diff1)) {
            $catIds = array_merge($catIds, array_keys($diff1));
        }
        if (!empty($diff2)) {
            $catIds = array_merge($catIds, array_keys($diff2));
        }
        BelaLogger::log($catIds);
        foreach ($catIds as $cId) {
            $this->buildPostsInCategoryTable($cId);
        }
    }

    /**
     * Test the category index is ok or not
     * @return boolean The category index is built or not
     */
    public function initialized() {
        return $this->getCache()->exists('categories.dat');
    }

}

