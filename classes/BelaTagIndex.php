<?php

/**
 * Description of BelaTagIndex
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaTagIndex extends BelaIndex {

    public function build() {
        
    }

    public function update($postId, $post = null) {
        
    }

    public function buildTagTable() {
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

    }

    public function buildPostInTagTable() {
        
    }

}

