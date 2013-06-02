<?php

/**
 * Build the category index
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaCategoryIndex extends BelaIndex {

    /**
     * 
     * @return array
     */
    public function buildCategoriesTable() {
        $excludedCategoryIds = $this->getOptions()->get(BelaKey::EXCLUDE_CATEGORY_LIST);
        $exclusion = '';
        if (!empty($excludedCategoryIds)) {
            $exclusion = "AND tt.term_taxonomy_id NOT IN (" . implode(',', $excludedCategoryIds) . ") ";
        }

        $sql = "SELECT tt.term_taxonomy_id ID, t.name, t.slug, tt.parent, 0 as count"
                . "FROM {$this->getDb()->terms} t "
                . "INNER JOIN {$this->getDb()->term_taxonomy}  tt ON (t.term_id=tt.term_id) "
                . "WHERE tt.taxonomy = 'category' AND t.term_id > 0 "
                . $exclusion
                . "ORDER BY ID ASC";
        $results = $this->getDb()->get_results($sql, OBJECT_K);

        $catIds = array_keys($results);

        $excludedPostIds = $this->getOptions()->get(BelaKey::EXCLUDED_POST_IDS);
        $exclusions = "";
        if (!empty($excludedPostIds)) {
            $exclusions = "AND p.ID NOT IN (" . implode(',', $excludedPostIds) . ") ";
        }

        $sql = "SELECT tr.term_taxonomy_id ID, count(p.ID) count "
                . "FROM {$this->getDb()->posts} p "
                . "INNER JOIN {$this->getDb()->term_relationships} tr ON p.ID=tr.object_id "
                . "WHERE tr.term_taxonomy_id IN (" . implode(',', $catIds) . ") "
                . $exclusions
                . "GROUP BY tr.term_taxonomy_id";
        $catStats = $this->getDb()->get_results($sql, OBJECT_K);
        if (!empty($results)) {
            foreach ($results as $id => $cat) {
                $results[$id]->count = $catStats[$id]->count;
            }
        }

        $categoryTable = array_map(array($this, 'getCategoryTableEntry'), $results);

        $this->getCache()->set('categories.dat', $categoryTable);

        return $categoryTable;
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

        $sql = "SELECT p.ID, p.post_title, p.post_date "
                . "FROM {$this->getDb()->posts} p"
                . "INNER JOIN {$this->getDb()->term_relationships} tr ON p.ID=tr.term_taxonomy_id "
                . "WHERE tr.term_taxonomy_id={$categoryId} "
                . "AND p.post_status='publish' "
                . $exclusions
                . "ORDER BY p.post_date DESC";
        $results = $this->getDb()->get_results($sql, OBJECT_K);
        $postTable = array_map(array($this, 'getPostTableEntry'), $results);

        $postIds = array_keys($postTable);
        $sql2 = "SELECT p.ID, COUNT(c.comment_ID) count"
                . "FROM {$this->getDb()->posts} p "
                . "INNER JOIN {$this->getDb()->comments} c ON p.ID=c.comment_post_ID "
                . "WHERE p.ID IN (" . implode(',', $postIds) . ") "
                . "GROUP BY p.ID ";


        $results2 = $this->getDb()->get_results($sql, OBJECT_K);
        foreach ($postTable as $k => $post) {
            if (isset($results2[$k])) {
                $postTable[$k][3] = $results2[$k]->count;
            }
        }

        $this->getCache()->set('cat-' . $categoryId . '.dat', $postTable);
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
    }

    public function update($postId, $post = null) {
        
    }

    public function initialized() {
        
        return $this->getCache()->exists('categories.dat');
    }

}

