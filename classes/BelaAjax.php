<?php

/**
 * Description of BelaAjax
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaAjax {

    const BELA_AJAX_VAR = 'bela_ajax';

    /**
     * The BelaOptions object
     * @var BelaOptions 
     */
    public $options = null;

    /**
     * @var BelaIndicesBuilder 
     */
    public $builder = null;

    /**
     * Constructor of the AJAX processor
     */
    public function __construct($options) {
        $this->options = $options;
        $this->builder = new BelaIndicesBuilder($this->options, BELA_CACHE_TYPE);
    }

    /**
     * Ajax request entry point
     */
    public function entry() {
        $params = $this->getRequest();

        switch ($params['menu']) {
            case BelaKey::ORDER_KEY_BY_DATE:
                $data = $this->generateChronologicalContent($params['year'], $params['month']);
                break;

            case BelaKey::ORDER_KEY_BY_CATEGORY:
                $data = $this->generateCatgoryContent($params['cat']);
                break;

            case BelaKey::ORDER_KEY_BY_TAGS:
                $data = $this->generateTagContent($params['tag']);
                break;

            default:
                break;
        }
        echo json_encode(array('ret'  => 0, 'msg'  => 'success', 'data' => $data));
        die();
    }

    /**
     * Retrieve the user get params from the request
     * @return array
     */
    public function getRequest() {
        $params = array();
        $menu = BelaAdmin::getParam('menu', reset($this->options->get(BelaKey::NAVIGATION_TABS_ORDER)));
        $params['menu'] = $menu;
        switch ($menu) {
            case BelaKey::ORDER_KEY_BY_DATE:
                $params['year'] = BelaAdmin::getParam('year', false);
                if (false === $params['year']) {
                    $years = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_DATE)->getYearsTable();
                    $params['year'] = reset(array_keys($years));
                }
                $params['month'] = BelaAdmin::getParam('month', false);
                if (false === $params['month']) {
                    $months = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_DATE)->getMonthsInYearTable($params['year']);
                    $params['month'] = reset(array_keys($months));
                }
                break;
            case BelaKey::ORDER_KEY_BY_CATEGORY:
                $params['cat'] = BelaAdmin::getParam('cat', false);
                if (false === $params['cat']) {
                    $categories = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_CATEGORY)->getCategoriesTable();
                    $params['cat'] = reset(array_keys($categories));
                }
                break;
            case BelaKey::ORDER_KEY_BY_TAGS:
                $params['tag'] = BelaAdmin::getParam('tag', false);
                if (false === $params['tag']) {
                    $tags = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_TAGS)->getTagsTable();
                    $params['tag'] = reset(array_keys($tags));
                }
                break;
            default:
                break;
        }
        return $params;
    }

    public function generateChronologicalContent($year, $month) {
        global $wp_locale;
        $index = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_DATE);
        $years = $index->getYearsTable();
        $months = $index->getMonthsInYearTable($year);
        $posts = $index->getPostsInMonthTable($year, $month);

        $showNumOfEntry = $this->options->get(BelaKey::SHOW_NUMBER_OF_ENTRIES);
        if ($showNumOfEntry) {
            $templateNumOfEntry = $this->options->get(BelaKey::TEMPLATE_NUMBER_OF_ENTRIES);
        }

        $abbreviateMonth = $this->options->get(BelaKey::ABBREVIATE_MONTH_NAME);
        
        ob_start();
        ?>
        <ul class="bela-chrono-year">
            <?php
            foreach ($years as $y => $count) {
                if ($showNumOfEntry) {
                    $numStr = str_replace('%', $count, $templateNumOfEntry);
                } else {
                    $numStr = '';
                }
                echo BelaHtml::menuItem($y . $numStr, array(
                    'class'  => 'year-entry',
                    'year'   => $y,
                    'menu'   => BelaKey::ORDER_KEY_BY_DATE,
                    'active' => $y == $year,
                ));
            }
            ?>
        </ul>
        <ul class="bela-chrono-month">
            <?php
            foreach ($months as $m => $count) {
                if ($showNumOfEntry) {
                    $numStr = str_replace('%', $count, $templateNumOfEntry);
                } else {
                    $numStr = '';
                }

                $monthName = $wp_locale->get_month($m);
                if ($abbreviateMonth) {
                    $monthName = $wp_locale->get_month_abbrev($monthName);
                } 
                
                echo BelaHtml::menuItem($monthName . $numStr, array(
                    'class'  => 'month-entry',
                    'year'   => $year,
                    'month'  => $m,
                    'menu'   => BelaKey::ORDER_KEY_BY_DATE,
                    'active' => $m == $month,
                ));
            }
            ?>
        </ul>
        <ul class="bela-post-list" menu="<?php echo BelaKey::ORDER_KEY_BY_DATE; ?>" year="<?php echo $year; ?>" month="<?php echo $month; ?>">
            <?php $this->printPostList($posts); ?>
        </ul>
        <?php
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    public function generateCatgoryContent($catId) {
        $index = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_CATEGORY);
        $categories = $index->getCategoriesTable();
        $posts = $index->getPostsInCategoryTable($catId);

        $showNumOfEntry = $this->options->get(BelaKey::SHOW_NUMBER_OF_ENTRIES);
        if ($showNumOfEntry) {
            $templateNumOfEntry = $this->options->get(BelaKey::TEMPLATE_NUMBER_OF_ENTRIES);
        }
        ob_start();
        ?>
        <ul class="bela-category">
            <?php
            foreach ($categories as $id => $cat) {
                if ($showNumOfEntry) {
                    $numStr = str_replace('%', $cat[4], $templateNumOfEntry);
                } else {
                    $numStr = '';
                }
                echo BelaHtml::menuItem($cat[1] . $numStr, array(
                    'class'  => 'category-entry',
                    'menu'   => BelaKey::ORDER_KEY_BY_CATEGORY,
                    'cat'    => $id,
                    'active' => $id == $catId,
                ));
            }
            ?>
        </ul>
        <ul class="bela-post-list" menu="<?php echo BelaKey::ORDER_KEY_BY_CATEGORY; ?>" cat="<?php echo $catId; ?>">
            <?php $this->printPostList($posts); ?>
        </ul>
        <?php
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    public function generateTagContent($tagId) {
        $index = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_TAGS);
        $tags = $index->getTagsTable();
        $posts = $index->getPostsInTagTable($tagId);
        $showNumberOfEntriesPerTag = $this->options->get(BelaKey::SHOW_NUMBER_OF_ENTRIES_PER_TAG);
        if ($showNumberOfEntriesPerTag) {
            $numberTemplate = $this->options->get(BelaKey::TEMPLATE_NUMBER_OF_ENTRIES_PER_TAG);
        }
        ob_start();
        ?>
        <ul class="bela-tag">
            <?php
            foreach ($tags as $tid => $tag) {
                if ($showNumberOfEntriesPerTag) {
                    $numStr = str_replace('%', $tag[2], $numberTemplate);
                } else {
                    $numStr = '';
                }
                echo BelaHtml::menuItem($tag[1] . $numStr, array(
                    'class'  => 'tag-entry',
                    'menu'   => BelaKey::ORDER_KEY_BY_TAGS,
                    'tag'    => $tid,
                    'active' => $tid == $tagId,
                ));
            }
            ?>
        </ul>
        <ul class="bela-post-list" menu="<?php echo BelaKey::ORDER_KEY_BY_TAGS; ?>" tag="<?php echo $tagId; ?>">
            <?php $this->printPostList($posts); ?>
        </ul>
        <?php
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }

    public function printPostList($posts) {
        if (is_array($posts) && !empty($posts)) {
            $showCommentsCount = $this->options->get(BelaKey::SHOW_NUMBER_OF_COMMENTS);
            if ($showCommentsCount) {
                $commentsCountTemplate = $this->options->get(BelaKey::TEMPLATE_NUMBER_OF_COMMENTS);
            }
            foreach ($posts as $ID => $p) {
                if ($showCommentsCount) {
                    $numStr = str_replace('%', $p[3], $commentsCountTemplate);
                } else {
                    $numStr = '';
                }
                ?>
                <li id="bela-post-<?php echo $ID; ?>" class="bela-post-entry">
                    <a class="bela-post-link" href="<?php echo $p[2]; ?>" title="<?php echo $p[1]; ?>">
                        <?php echo $p[1], $numStr; ?>
                    </a>
                </li>
                <?php
            }
        } else {
            echo '<li class="bela-post-entry">empty</li>';
        }
    }

}
