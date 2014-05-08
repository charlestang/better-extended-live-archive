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
        $menu = BelaAdmin::getParam('menu');
        if (null == $menu) {
            $navigation_tabs_array = $this->options->get(BelaKey::NAVIGATION_TABS_ORDER);
            $menu = reset($navigation_tabs_array);
        }
        $params['menu'] = $menu;
        switch ($menu) {
            case BelaKey::ORDER_KEY_BY_DATE:
                $params['year'] = BelaAdmin::getParam('year', false);
                if (false === $params['year']) {
                    $years = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_DATE)->getYearsList();
                    $params['year'] = reset($years);
                }
                $params['month'] = BelaAdmin::getParam('month', false);
                if (false === $params['month']) {
                    $months = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_DATE)->getMonthsInYearList($params['year']);
                    $params['month'] = reset($months);
                }
                break;
            case BelaKey::ORDER_KEY_BY_CATEGORY:
                $params['cat'] = BelaAdmin::getParam('cat', false);
                if (false === $params['cat']) {
                    $categories = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_CATEGORY)->getCategoriesList();
                    $params['cat'] = reset($categories);
                }
                break;
            case BelaKey::ORDER_KEY_BY_TAGS:
                $params['tag'] = BelaAdmin::getParam('tag', false);
                if (false === $params['tag']) {
                    $tags = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_TAGS)->getTagsTable();
                    $tags = array_keys($tags);
                    $params['tag'] = $tags[0];
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
        $catobjs = array();
        foreach ($categories as $cat) {
            $catobj = new stdClass;
            $catobj->id = $cat[0];
            $catobj->name = $cat[1];
            $catobj->slug = $cat[2];
            $catobj->parent = $cat[3];
            $catobj->count = $cat[4];
            $catobjs[] = $catobj;
        }
        $textBeforeChildCategory = $this->options->get(BelaKey::TEXT_BEFORE_CHILD_CATEGORY);
        ob_start();
        ?>
        <ul class="bela-category">
            <?php
            $walker = new BelaCategoryWalker;
            echo $walker->walk($catobjs, 0, $catId, BelaKey::ORDER_KEY_BY_CATEGORY, $showNumOfEntry, $templateNumOfEntry, $textBeforeChildCategory);
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
        <ul class="bela-post-list bela-orderby-tag" menu="<?php echo BelaKey::ORDER_KEY_BY_TAGS; ?>" tag="<?php echo $tagId; ?>">
            <?php $this->printPostList($posts); ?>
        </ul>
        <?php
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }

    public function printPostList($posts) {
        if (is_array($posts) && !empty($posts)) {
            //the count of the posts list
            $count = count($posts);

            //pagination options
            $pagination = $this->options->get(BelaKey::PAGINATE_THE_LIST);
            $pagesize = $this->options->get(BelaKey::PAGE_OPT_NUMBER_PER_PAGE);
            $pretext = $this->options->get(BelaKey::PAGE_OPT_PREVIOUS_PAGE_TEXT);
            $nexttext = $this->options->get(BelaKey::PAGE_OPT_NEXT_PAGE_TEXT);

            //if paginate, truncate the posts list
            if ($pagination) {
                $curpage = intval(BelaAdmin::getParam('page', 0));
                $offset = $curpage * $pagesize;
                if ($offset < $count) {
                    $posts = array_slice($posts, $offset, $pagesize);
                }
            }

            $showCommentsCount = $this->options->get(BelaKey::SHOW_NUMBER_OF_COMMENTS);
            if ($showCommentsCount) {
                $commentsCountTemplate = $this->options->get(BelaKey::TEMPLATE_NUMBER_OF_COMMENTS);
            }

            //pagination
            if ($pagination && $curpage > 0) {
                echo '<div class="bela-pre-page" page="', $curpage - 1, '">', $pretext, '</div>';
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

            //pagination
            if ($pagination && $count > $pagesize && $offset + $pagesize < $count) {
                echo '<div class="bela-next-page" page="', $curpage + 1, '">', $nexttext, '</div>';
            }
        } else {
            echo '<li class="bela-post-entry">empty</li>';
        }
    }

}
