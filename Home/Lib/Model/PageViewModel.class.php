<?php

class PageViewModel extends ViewModel{

    public $viewFields = array(
        'Page'  => array('*', '_type'=>'LEFT'),
        'Group' => array('_as'=>'ck_group', 'name'=>'group_name', '_on'=>'Page.group_id=ck_group.id'), // group是关键字，所以_as=>ck_group
    );

}

?>