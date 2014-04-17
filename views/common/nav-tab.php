<h2>Better Extended Live Archive Options</h2>
<ul class="subsubsub">
    <li>
        <a <?php echo $this->isCurr('whatToShow') ? 'class="current"' : ''; ?> 
            href="<?php echo BelaAdmin::URL('whatToShow'); ?>">What to show </a> |</li>
    <li>
        <a <?php echo $this->isCurr('howToShow') ? 'class="current"' : ''; ?> 
            href="<?php echo BelaAdmin::URL('howToShow'); ?>">How to show </a> |</li>
    <li>
        <a <?php echo $this->isCurr('howToCut') ? 'class="current"' : ''; ?> 
            href="<?php echo BelaAdmin::URL('howToCut'); ?>">How to cut</a> |</li>
    <li>
        <a <?php echo $this->isCurr('menuSettings') ? 'class="current"' : ''; ?> 
            href="<?php echo BelaAdmin::URL('menuSettings'); ?>">Menu settings</a> |</li>
    <li>
        <a <?php echo $this->isCurr('catSettings') ? 'class="current"' : ''; ?> 
            href="<?php echo BelaAdmin::URL('catSettings'); ?>">Category exclusion</a> |</li>
    <li>
        <a <?php echo $this->isCurr('pagination') ? 'class="current"' : ''; ?> 
            href="<?php echo BelaAdmin::URL('pagination'); ?>">Pagination</a> |</li>
    <li>
        <a <?php echo $this->isCurr('appearance') ? 'class="current"' : ''; ?> 
            href="<?php echo BelaAdmin::URL('appearance'); ?>">Appearance</a></li>
</ul>
<div class="clear"></div>