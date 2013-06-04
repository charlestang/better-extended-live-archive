<?php

/**
 * Description of BelaExcludeIndex
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaExcludeIndex extends BelaIndex {

    public function build() {
        $excludedPageIds = $this->getExcludedPageIds();
        $excludedCategoryPostsIds = $this->getExcludedCategoryPostIds();
        $excludedPostTypeIds = $this->getExcludedPostTypePostsIds();
        $excludedIds = array_merge($excludedPageIds, $excludedCategoryPostsIds, $excludedPostTypeIds);
        $this->getOptions()->set(BelaKey::EXCLUDED_POST_IDS, array_unique($excludedIds));
    }

    public function update($postId, $post = null) {
        //@todo to be optimized
        $this->build();
    }

    /**
     * Get all page ids according to the exclude_page option.
     * 
     * @return array page ids array or empty array
     */
    private function getExcludedPageIds() {
        $pageIds = array();
        if ($this->getOptions()->get(BelaKey::EXCLUDE_PAGE)) {
            $sql = "SELECT ID FROM " . $this->getDb()->posts . " WHERE post_type='page' AND post_status='publish'";
            $pageIds = $this->getDb()->get_col($sql);
        }

        return $pageIds;
    }

    /**
     * Get the post ids that in exluded categories.
     * @return array
     */
    private function getExcludedCategoryPostIds() {
        $pageIds = array();
        $excludedCats = $this->getOptions()->get(BelaKey::EXCLUDE_CATEGORY_LIST);
        if (!empty($excludedCats)) {
            $sql = "SELECT p.ID FROM {$this->getDb()->posts} p "
                    . "INNER JOIN {$this->getDb()->term_relationships} tr ON ( p.ID=tr.object_id ) "
                    . "INNER JOIN {$this->getDb()->term_taxonomy} tt ON ( tr.term_taxonomy_id=tt.term_taxonomy_id ) "
                    . "WHERE tt.taxonomy='category' "
                    . "AND tt.term_id IN (" . implode(",", $excludedCats) . ")";
            $pageIds = $this->getDb()->get_col($sql);
        }

        return array_unique($pageIds);
    }

    /**
     * The post ids should be exclude which are in excluded post types.
     * @return array
     */
    private function getExcludedPostTypePostsIds() {
        $pageIds = array();
        $excludedPostTypes = $this->getOptions()->get(BelaKey::EXCLUDE_POST_TYPE_LIST);
        if (!empty($excludedPostTypes)) {
            $sql = "SELECT ID FROM {$this->getDb()->posts} WHERE post_type IN ('" . implode("','", $excludedPostTypes) . "')";
            $pageIds = $this->getDb()->get_col($sql);
        }

        return $pageIds;
    }

    public function initialized() {
        
    }

}
