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

            default:
                break;
        }
        echo json_encode(array('ret'  => 0, 'msg'  => 'success', 'data' => $data));
        die();
    }

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
                break;
            default:
                break;
        }
        return $params;
    }

    public function generateChronologicalContent($year, $month) {
        $index = $this->builder->getIndex(BelaKey::ORDER_KEY_BY_DATE);
        $years = $index->getYearsTable();
        $months = $index->getMonthsInYearTable($year);
        $posts = $index->getPostsInMonthTable($year, $month);
        ob_start();
        ?>
        <ul class="bela-chrono-year">
            <?php
            foreach ($years as $y => $count) {
                echo BelaHtml::menuItem($y, array(
                    'class'  => 'year-entry',
                    'year'   => $y,
                    'menu'   => BelaKey::ORDER_KEY_BY_DATE,
                    'active' => $y == $year,));
            }
            ?>
        </ul>
        <ul class="bela-chrono-month">
            <?php
            foreach ($months as $m => $count) {
                echo BelaHtml::menuItem($m, array(
                    'class'  => 'month-entry',
                    'year'   => $year,
                    'month'  => $m,
                    'menu'   => BelaKey::ORDER_KEY_BY_DATE,
                    'active' => $m == $month,));
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
        ob_start();
        ?>
        <ul class="bela-category">
            <?php
            foreach ($categories as $id => $cat) {
                echo BelaHtml::menuItem($cat[1], array(
                    'class'  => 'category-entry',
                    'menu'   => BelaKey::ORDER_KEY_BY_CATEGORY,
                    'cat'    => $id,
                    'active' => $id == $catId));
            }
            ?>
        </ul>
        <ul class="bela-post-list" cat="<?php echo $catId; ?>">
            <?php $this->printPostList($posts); ?>
        </ul>
        <?php
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    public function printPostList($posts) {
        if (is_array($posts) && !empty($posts))  {
        foreach ($posts as $ID => $p) {
            ?>
            <li id="bela-post-<?php echo $ID; ?>" class="bela-post-entry">
                <a class="bela-post-link" href="<?php echo $p[2]; ?>" title="<?php echo $p[1]; ?>">
                    <?php echo $p[1]; ?>
                </a>
            </li>
            <?php
        }
        } else {
            echo '<li class="bela-post-entry">empty</li>';
        }
    }

}
