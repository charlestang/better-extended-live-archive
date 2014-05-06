<?php

/**
 * Description of BelaTagIndex
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaTagIndex extends BelaIndex {

    public function build() {
        $tags = $this->buildTagsTable();
        foreach ($tags as $id => $tag) {
            $this->buildPostsInTagTable($id);
        }
        return $tags;
    }

    public function beforeUpdate($postId, $postAfter, $postBefore) {
        ;
    }
    public function afterUpdate($postId, $post = null) {
        
    }

    public function buildTagsTable() {
        $tagStrategy = $this->getOptions()->get(BelaKey::TAGS_PICK_STRATEGY);
        $strategySubstatement = '';
        $threshold = $this->getOptions()->get(BelaKey::TAG_STRATEGY_THRESHOLD);
        switch ($tagStrategy) {
            case BelaKey::TAG_STRATEGY_TAG_AT_LEAST_X_POST: //at least <BelaKey::TAG_STRATEGY_THRESHOLD> posts marked by the tag
                $strategySubstatement = "AND tt.count>={$threshold} ";
                break;
            case BelaKey::TAG_STRATEGY_FIRST_X_MOST_USED: //first <BelaKey::TAG_STRATEGY_THRESHOLD> tags shown out
                $strategySubstatement = "ORDER BY tt.count DESC LIMIT 0, {$threshold} ";
                break;
            case BelaKey::TAG_STRATEGY_SHOW_ALL:
            default:
                break;
        }

        $sql = "SELECT tt.term_taxonomy_id, t.name, tt.count "
                . "FROM {$this->getDb()->terms} t "
                . "INNER JOIN {$this->getDb()->term_taxonomy} tt "
                . "ON t.term_id=tt.term_id "
                . "WHERE tt.taxonomy='post_tag' "
                . $strategySubstatement;
        $results = $this->getDb()->get_results($sql, OBJECT_K);
        BelaLogger::log($sql, $results);

        $tagsTable = array_map(array($this, 'getTagTableEntry'), $results);
        $this->getCache()->set('tags.dat', $tagsTable);

        return $tagsTable;
    }

    public function getTagsTable() {
        $tagsTable = $this->getCache()->get('tags.dat');
        if (false === $tagsTable) {
            $tagsTable = $this->build();
        }

        return $tagsTable;
    }

    private function getTagTableEntry($tag) {
        return array(
            $tag->term_taxonomy_id,
            $tag->name,
            $tag->count,
        );
    }

    public function buildPostsInTagTable($tagId) {
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
                . "INNER JOIN {$this->getDb()->term_relationships} tr "
                . "ON p.ID=tr.object_id "
                . "WHERE tr.term_taxonomy_id={$tagId} "
                . $exclusions
                . "ORDER BY p.post_date {$order}";
        $results = $this->getDb()->get_results($sql, OBJECT_K);
        BelaLogger::log($sql, $results);
        if (empty($results)) {
            return;
        }

        $postTable = array_map(array($this, 'getPostTableEntry'), $results);
        $postIds = array_keys($postTable);

        $sql2 = "SELECT p.ID, COUNT(c.comment_ID) count "
                . "FROM {$this->getDb()->posts} p "
                . "INNER JOIN {$this->getDb()->comments} c ON p.ID=c.comment_post_ID "
                . "WHERE p.ID IN (" . implode(',', $postIds) . ") "
                . "GROUP BY p.ID ";
        $results2 = $this->getDb()->get_results($sql2, OBJECT_K);
        BelaLogger::log($sql2, $results2);

        foreach ($postTable as $k => $post) {
            if (isset($results2[$k])) {
                $postTable[$k][3] = $results2[$k]->count;
            }
        }

        $this->getCache()->set('tag-' . $tagId. '.dat', $postTable);
    }

    public function getPostsInTagTable($tagId) {
        return $this->getCache()->get('tag-' . $tagId . '.dat');
    }

    private function getPostTableEntry($post) {
        return array(
            $post->post_date,
            $post->post_title,
            get_permalink($post->ID),
            0,
        );
    }

    public function initialized() {
        $ret = $this->getCache()->exists('tags.dat');       
        return $ret;
    }

}

